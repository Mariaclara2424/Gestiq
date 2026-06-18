<?php
/**
 * ProjetoModel.php
 * Model responsável pelos dados de projetos no GESTIQ.
 * Adaptado e otimizado para MySQL via PDO.
 */

require_once ROOT_PATH . '/config/database.php';

class ProjetoModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ------------------------------------------------------------------ */
    /* DASHBOARD — CONTAGENS E KPIs                                       */
    /* ------------------------------------------------------------------ */

    /**
     * Retorna totais agrupados por status para os cards do dashboard.
     * Retorna: ['total', 'planejamento', 'em_andamento', 'concluido', 'cancelado']
     */
    public function contarPorStatus(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'planejamento' THEN 1 ELSE 0 END) AS planejamento,
                SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) AS em_andamento,
                SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) AS concluido,
                SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) AS cancelado
             FROM projetos"
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna contagem de tarefas agrupadas por status.
     * Retorna: ['total', 'pendente', 'em_andamento', 'concluida', 'cancelada', 'atrasadas']
     */
    public function contarTarefasPorStatus(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) AS pendente,
                SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) AS em_andamento,
                SUM(CASE WHEN status = 'concluida' THEN 1 ELSE 0 END) AS concluida,
                SUM(CASE WHEN status = 'cancelada' THEN 1 ELSE 0 END) AS cancelada,
                SUM(CASE WHEN data_vencimento < CURRENT_DATE() 
                          AND status NOT IN ('concluida','cancelada') THEN 1 ELSE 0 END) AS atrasadas
             FROM tarefas"
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna os projetos mais recentes em andamento para exibição no dashboard.
     */
    public function listarRecentesEmAndamento(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                p.id_projeto,
                p.titulo,
                p.data_inicio,
                p.data_fim,
                p.status,
                u.nome AS responsavel_nome,
                d.nome AS departamento_nome,
                COUNT(t.id_tarefa) AS total_tarefas,
                SUM(CASE WHEN t.status = 'concluida' THEN 1 ELSE 0 END) AS tarefas_concluidas
             FROM projetos p
             LEFT JOIN usuarios u ON p.id_responsavel = u.id_usuario
             LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
             LEFT JOIN tarefas t ON t.id_projeto = p.id_projeto
             WHERE p.status = 'em_andamento'
             GROUP BY p.id_projeto, u.nome, d.nome
             ORDER BY p.created_at DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna tarefas urgentes (prioridade crítica/alta, não concluídas).
     */
    public function listarTarefasUrgentes(int $idUsuario = 0, int $limite = 6): array
    {
        $sql = "SELECT
                    t.id_tarefa,
                    t.titulo,
                    t.prioridade,
                    t.status,
                    t.data_vencimento,
                    p.titulo AS projeto_titulo,
                    u.nome AS responsavel_nome
                FROM tarefas t
                LEFT JOIN projetos p ON t.id_projeto = p.id_projeto
                LEFT JOIN usuarios u ON t.id_responsavel = u.id_usuario
                WHERE t.prioridade IN ('critica', 'alta')
                  AND t.status NOT IN ('concluida', 'cancelada')";
        $params = [];

        if ($idUsuario > 0) {
            $sql .= " AND t.id_responsavel = :uid";
            $params[':uid'] = $idUsuario;
        }

        // MySQL trata NULLS de forma diferente no ORDER BY. 
        // Usar "t.data_vencimento IS NULL" joga os nulos para o final.
        $sql .= " ORDER BY
                      CASE t.prioridade WHEN 'critica' THEN 0 ELSE 1 END ASC,
                      t.data_vencimento IS NULL ASC, 
                      t.data_vencimento ASC
                  LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Distribuição de projetos por departamento para o gráfico de pizza/barra.
     */
    public function distribuicaoPorDepartamento(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COALESCE(d.nome, 'Sem departamento') AS label,
                COUNT(p.id_projeto) AS total
             FROM projetos p
             LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
             WHERE p.status != 'cancelado'
             GROUP BY d.nome
             ORDER BY total DESC
             LIMIT 6"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Evolução mensal de projetos criados nos últimos 6 meses.
     */
    public function evolucaoMensal(): array
    {
        $stmt = $this->db->query(
            "SELECT
                DATE_FORMAT(created_at, '%b/%y') AS mes,
                COUNT(*) AS total
             FROM projetos
             WHERE created_at >= NOW() - INTERVAL 6 MONTH
             GROUP BY DATE_FORMAT(created_at, '%b/%y'), DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Taxa de conclusão global (projetos concluídos / total não cancelados * 100).
     */
    public function taxaConclusao(): float
    {
        $stmt = $this->db->query(
            "SELECT
                SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) AS concluidos,
                SUM(CASE WHEN status != 'cancelado' THEN 1 ELSE 0 END) AS validos
             FROM projetos"
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row) || (int)$row['validos'] === 0) return 0.0;
        return round((int)$row['concluidos'] / (int)$row['validos'] * 100, 1);
    }

    /**
     * Resumo por usuário: quantas tarefas abertas cada um tem.
     */
    public function cargaPorUsuario(int $limite = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                u.nome,
                COUNT(t.id_tarefa) AS tarefas_abertas
             FROM usuarios u
             INNER JOIN tarefas t ON t.id_responsavel = u.id_usuario
             WHERE t.status NOT IN ('concluida', 'cancelada')
               AND u.ativo = 1
             GROUP BY u.id_usuario, u.nome
             ORDER BY tarefas_abertas DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
   
     }
     /* ------------------------------------------------------------------ */
    /* MÉTODOS GERAIS (Listagem e Busca para uso em outros Controllers)   */
    /* ------------------------------------------------------------------ */

    /**
     * Lista os projetos (usado para preencher selects e tabelas).
     */
    public function listar(array $filtros = []): array
    {
        $sql = "SELECT p.*, d.nome AS departamento_nome, u.nome AS responsavel_nome
                FROM projetos p
                LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
                LEFT JOIN usuarios u ON p.id_responsavel = u.id_usuario
                WHERE 1=1";

        if (!empty($filtros['status'])) {
            $sql .= " AND p.status = :status";
        }

        $sql .= " ORDER BY p.titulo ASC";

        $stmt = $this->db->prepare($sql);

        if (!empty($filtros['status'])) {
            $stmt->bindValue(':status', $filtros['status']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um único projeto pelo ID.
     */
    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, d.nome AS departamento_nome, u.nome AS responsavel_nome
             FROM projetos p
             LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
             LEFT JOIN usuarios u ON p.id_responsavel = u.id_usuario
             WHERE p.id_projeto = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}