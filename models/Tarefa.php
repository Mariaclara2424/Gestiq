<?php
require_once ROOT_PATH . '/config/database.php';

class Tarefa
{
    private PDO $db;

    public const STATUS     = ['pendente', 'em_andamento', 'concluida', 'cancelada'];
    public const PRIORIDADE = ['baixa', 'media', 'alta', 'critica'];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ------------------------------------------------------------------ */
    /* LISTAGEM E BUSCA                                                     */
    /* ------------------------------------------------------------------ */

    public function listar(array $filtros = []): array
    {
        $sql = "SELECT t.*,
                       p.titulo     AS projeto_titulo,
                       u.nome       AS responsavel_nome,
                       COUNT(c.id_comentario) AS total_comentarios
                FROM tarefas t
                LEFT JOIN projetos  p ON t.id_projeto    = p.id_projeto
                LEFT JOIN usuarios  u ON t.id_responsavel = u.id_usuario
                LEFT JOIN comentarios c ON c.id_tarefa   = t.id_tarefa
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['id_projeto'])) {
            $sql .= " AND t.id_projeto = :id_projeto";
            $params[':id_projeto'] = (int) $filtros['id_projeto'];
        }

        if (!empty($filtros['busca'])) {
            $sql .= " AND (t.titulo LIKE :busca OR t.descricao LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $filtros['status'];
        }

        if (!empty($filtros['prioridade'])) {
            $sql .= " AND t.prioridade = :prioridade";
            $params[':prioridade'] = $filtros['prioridade'];
        }

        if (!empty($filtros['id_responsavel'])) {
            $sql .= " AND t.id_responsavel = :id_responsavel";
            $params[':id_responsavel'] = (int) $filtros['id_responsavel'];
        }

        if (!empty($filtros['vencendo_hoje'])) {
            $sql .= " AND t.data_vencimento = CURDATE() AND t.status NOT IN ('concluida','cancelada')";
        }

        if (!empty($filtros['atrasadas'])) {
            $sql .= " AND t.data_vencimento < CURDATE() AND t.status NOT IN ('concluida','cancelada')";
        }

        // Colaborador vê apenas suas tarefas
        if (!empty($filtros['_usuario_id']) && !empty($filtros['_perfil']) && $filtros['_perfil'] === 'colaborador') {
            $sql .= " AND t.id_responsavel = :uid";
            $params[':uid'] = $filtros['_usuario_id'];
        }

        $sql .= " GROUP BY t.id_tarefa ORDER BY
                  FIELD(t.prioridade, 'critica','alta','media','baixa'),
                  t.data_vencimento ASC,
                  t.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT t.*,
                    p.titulo  AS projeto_titulo,
                    p.status  AS projeto_status,
                    u.nome    AS responsavel_nome
             FROM tarefas t
             LEFT JOIN projetos p ON t.id_projeto    = p.id_projeto
             LEFT JOIN usuarios u ON t.id_responsavel = u.id_usuario
             WHERE t.id_tarefa = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function contarAtrasadas(int $idUsuario = 0): int
    {
        $sql    = "SELECT COUNT(*) FROM tarefas
                   WHERE data_vencimento < CURDATE() AND status NOT IN ('concluida','cancelada')";
        $params = [];

        if ($idUsuario) {
            $sql .= " AND id_responsavel = :uid";
            $params[':uid'] = $idUsuario;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function resumoPorStatus(int $idProjeto = 0): array
    {
        $sql    = "SELECT status, COUNT(*) AS total FROM tarefas WHERE 1=1";
        $params = [];

        if ($idProjeto) {
            $sql .= " AND id_projeto = :id";
            $params[':id'] = $idProjeto;
        }

        $sql .= " GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /* ------------------------------------------------------------------ */
    /* CRUD                                                                  */
    /* ------------------------------------------------------------------ */

    public function criar(array $dados): int|false
    {
        $erros = $this->validar($dados);
        if (!empty($erros)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO tarefas (titulo, descricao, id_projeto, id_responsavel, data_vencimento, status, prioridade)
             VALUES (:titulo, :descricao, :id_projeto, :id_responsavel, :data_vencimento, :status, :prioridade)"
        );
        $stmt->execute([
            ':titulo'          => trim($dados['titulo']),
            ':descricao'       => trim($dados['descricao'] ?? ''),
            ':id_projeto'      => (int) $dados['id_projeto'],
            ':id_responsavel'  => !empty($dados['id_responsavel']) ? (int) $dados['id_responsavel'] : null,
            ':data_vencimento' => !empty($dados['data_vencimento']) ? $dados['data_vencimento'] : null,
            ':status'          => $dados['status']    ?? 'pendente',
            ':prioridade'      => $dados['prioridade'] ?? 'media',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $erros = $this->validar($dados);
        if (!empty($erros)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE tarefas
             SET titulo = :titulo, descricao = :descricao, id_projeto = :id_projeto,
                 id_responsavel = :id_responsavel, data_vencimento = :data_vencimento,
                 status = :status, prioridade = :prioridade
             WHERE id_tarefa = :id"
        );
        return $stmt->execute([
            ':titulo'          => trim($dados['titulo']),
            ':descricao'       => trim($dados['descricao'] ?? ''),
            ':id_projeto'      => (int) $dados['id_projeto'],
            ':id_responsavel'  => !empty($dados['id_responsavel']) ? (int) $dados['id_responsavel'] : null,
            ':data_vencimento' => !empty($dados['data_vencimento']) ? $dados['data_vencimento'] : null,
            ':status'          => $dados['status'],
            ':prioridade'      => $dados['prioridade'],
            ':id'              => $id,
        ]);
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUS, true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE tarefas SET status = :status WHERE id_tarefa = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tarefas WHERE id_tarefa = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* ------------------------------------------------------------------ */
    /* VALIDAÇÃO                                                             */
    /* ------------------------------------------------------------------ */

    public function validar(array $dados): array
    {
        $erros = [];

        if (empty(trim($dados['titulo'] ?? ''))) {
            $erros[] = 'O título da tarefa é obrigatório.';
        } elseif (mb_strlen(trim($dados['titulo'])) > 200) {
            $erros[] = 'O título deve ter no máximo 200 caracteres.';
        }

        if (empty($dados['id_projeto'])) {
            $erros[] = 'O projeto é obrigatório.';
        }

        if (!in_array($dados['status'] ?? '', self::STATUS, true)) {
            $erros[] = 'Status inválido.';
        }

        if (!in_array($dados['prioridade'] ?? '', self::PRIORIDADE, true)) {
            $erros[] = 'Prioridade inválida.';
        }

        return $erros;
    }
}