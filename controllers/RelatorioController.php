<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/config/database.php';

class RelatorioController
{
    private PDO $db;

    public function __construct()
    {
        Auth::require('admin', 'gestor');
        $this->db = Database::getConnection();
    }

    // ... resto do código continua igualzinho para baixo ...
    /* ------------------------------------------------------------------ */
    /* VISÃO GERAL                                                           */
    /* ------------------------------------------------------------------ */

    public function index(): void
    {
        $tipo = $_GET['tipo'] ?? 'projetos';
        $dados = match ($tipo) {
            'tarefas'      => $this->dadosTarefas(),
            'usuarios'     => $this->dadosUsuarios(),
            'departamentos' => $this->dadosDepartamentos(),
            default        => $this->dadosProjetos(),
        };

        require ROOT_PATH . '/views/relatorios/index.php';
    }

    /* ------------------------------------------------------------------ */
    /* RELATÓRIO — PROJETOS                                                  */
    /* ------------------------------------------------------------------ */

    public function projetos(): void
    {
        $filtros = [];
        if (!empty($_GET['status']))           $filtros['status']          = $_GET['status'];
        if (!empty($_GET['id_departamento'])) $filtros['id_departamento'] = (int) $_GET['id_departamento'];
        if (!empty($_GET['data_inicio_de']))  $filtros['data_inicio_de']  = $_GET['data_inicio_de'];
        if (!empty($_GET['data_inicio_ate'])) $filtros['data_inicio_ate'] = $_GET['data_inicio_ate'];

        $dados = $this->dadosProjetos($filtros);

        if (!empty($_GET['exportar']) && $_GET['exportar'] === 'csv') {
            $this->exportarCsv($dados['linhas'], $dados['cabecalho'], 'relatorio_projetos');
            return;
        }

        $tipo = 'projetos';
        require ROOT_PATH . '/views/relatorios/index.php';
    }

    /* ------------------------------------------------------------------ */
    /* RELATÓRIO — TAREFAS                                                   */
    /* ------------------------------------------------------------------ */

    public function tarefas(): void
    {
        $filtros = [];
        if (!empty($_GET['status']))     $filtros['status']    = $_GET['status'];
        if (!empty($_GET['prioridade'])) $filtros['prioridade'] = $_GET['prioridade'];
        if (!empty($_GET['id_projeto'])) $filtros['id_projeto'] = (int) $_GET['id_projeto'];

        $dados = $this->dadosTarefas($filtros);

        if (!empty($_GET['exportar']) && $_GET['exportar'] === 'csv') {
            $this->exportarCsv($dados['linhas'], $dados['cabecalho'], 'relatorio_tarefas');
            return;
        }

        $tipo = 'tarefas';
        require ROOT_PATH . '/views/relatorios/index.php';
    }

    /* ------------------------------------------------------------------ */
    /* EXPORTAR CSV                                                          */
    /* ------------------------------------------------------------------ */

    private function exportarCsv(array $linhas, array $cabecalho, string $nome): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nome . '_' . date('Ymd_His') . '.csv"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para Excel reconhecer UTF-8
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, $cabecalho, ';');

        foreach ($linhas as $linha) {
            fputcsv($out, $linha, ';');
        }
        fclose($out);
    }

    /* ------------------------------------------------------------------ */
    /* BUILDERS DE DADOS                                                     */
    /* ------------------------------------------------------------------ */

    private function dadosProjetos(array $filtros = []): array
    {
        $sql = "SELECT p.titulo, p.status, p.data_inicio, p.data_fim,
                       u.nome AS responsavel, d.nome AS departamento,
                       COUNT(DISTINCT t.id_tarefa)                                      AS total_tarefas,
                       COUNT(DISTINCT CASE WHEN t.status='concluida' THEN t.id_tarefa END) AS concluidas
                FROM projetos p
                LEFT JOIN usuarios      u ON p.id_responsavel  = u.id_usuario
                LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
                LEFT JOIN tarefas       t ON t.id_projeto      = p.id_projeto
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['status'])) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $filtros['status'];
        }
        if (!empty($filtros['id_departamento'])) {
            $sql .= " AND p.id_departamento = :id_departamento";
            $params[':id_departamento'] = $filtros['id_departamento'];
        }
        if (!empty($filtros['data_inicio_de'])) {
            $sql .= " AND p.data_inicio >= :de";
            $params[':de'] = $filtros['data_inicio_de'];
        }
        if (!empty($filtros['data_inicio_ate'])) {
            $sql .= " AND p.data_inicio <= :ate";
            $params[':ate'] = $filtros['data_inicio_ate'];
        }

        $sql .= " GROUP BY p.id_projeto ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $cabecalho = ['Título', 'Status', 'Início', 'Fim', 'Responsável', 'Departamento', 'Total Tarefas', 'Concluídas'];
        $linhas = array_map(fn($r) => [
            $r['titulo'], $r['status'],
            $r['data_inicio'] ? date('d/m/Y', strtotime($r['data_inicio'])) : '',
            $r['data_fim']    ? date('d/m/Y', strtotime($r['data_fim']))    : '',
            $r['responsavel'] ?? '', $r['departamento'] ?? '',
            $r['total_tarefas'], $r['concluidas'],
        ], $rows);

        return compact('cabecalho', 'linhas', 'rows');
    }

    private function dadosTarefas(array $filtros = []): array
    {
        $sql = "SELECT t.titulo, t.status, t.prioridade, t.data_vencimento,
                       p.titulo AS projeto, u.nome AS responsavel
                FROM tarefas t
                LEFT JOIN projetos  p ON t.id_projeto    = p.id_projeto
                LEFT JOIN usuarios  u ON t.id_responsavel = u.id_usuario
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['status'])) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $filtros['status'];
        }
        if (!empty($filtros['prioridade'])) {
            $sql .= " AND t.prioridade = :prioridade";
            $params[':prioridade'] = $filtros['prioridade'];
        }
        if (!empty($filtros['id_projeto'])) {
            $sql .= " AND t.id_projeto = :id_projeto";
            $params[':id_projeto'] = $filtros['id_projeto'];
        }

        $sql .= " ORDER BY FIELD(t.prioridade,'critica','alta','media','baixa'), t.data_vencimento";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $cabecalho = ['Título', 'Status', 'Prioridade', 'Vencimento', 'Projeto', 'Responsável'];
        $linhas = array_map(fn($r) => [
            $r['titulo'], $r['status'], $r['prioridade'],
            $r['data_vencimento'] ? date('d/m/Y', strtotime($r['data_vencimento'])) : '',
            $r['projeto'] ?? '', $r['responsavel'] ?? '',
        ], $rows);

        return compact('cabecalho', 'linhas', 'rows');
    }

    private function dadosUsuarios(): array
    {
        $stmt = $this->db->query(
            "SELECT u.nome, u.email, u.perfil, u.ativo,
                    d.nome AS departamento, c.nome AS cargo,
                    COUNT(DISTINCT t.id_tarefa) AS total_tarefas
             FROM usuarios u
             LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento
             LEFT JOIN cargos        c ON u.id_cargo        = c.id_cargo
             LEFT JOIN tarefas       t ON t.id_responsavel  = u.id_usuario AND t.status != 'concluida'
             GROUP BY u.id_usuario
             ORDER BY u.nome"
        );
        $rows      = $stmt->fetchAll();
        $cabecalho = ['Nome', 'E-mail', 'Perfil', 'Ativo', 'Departamento', 'Cargo', 'Tarefas Abertas'];
        $linhas    = array_map(fn($r) => [
            $r['nome'], $r['email'], $r['perfil'],
            $r['ativo'] ? 'Sim' : 'Não',
            $r['departamento'] ?? '', $r['cargo'] ?? '',
            $r['total_tarefas'],
        ], $rows);

        return compact('cabecalho', 'linhas', 'rows');
    }

    private function dadosDepartamentos(): array
    {
        $stmt = $this->db->query(
            "SELECT d.nome AS departamento,
                    COUNT(DISTINCT u.id_usuario) AS total_usuarios,
                    COUNT(DISTINCT c.id_cargo)   AS total_cargos,
                    COUNT(DISTINCT p.id_projeto) AS total_projetos
             FROM departamentos d
             LEFT JOIN usuarios  u ON u.id_departamento = d.id_departamento AND u.ativo = 1
             LEFT JOIN cargos    c ON c.id_departamento = d.id_departamento
             LEFT JOIN projetos  p ON p.id_departamento = d.id_departamento AND p.status != 'cancelado'
             GROUP BY d.id_departamento
             ORDER BY d.nome"
        );
        $rows      = $stmt->fetchAll();
        $cabecalho = ['Departamento', 'Usuários Ativos', 'Cargos', 'Projetos Ativos'];
        $linhas    = array_map(fn($r) => [
            $r['departamento'], $r['total_usuarios'],
            $r['total_cargos'], $r['total_projetos'],
        ], $rows);

        return compact('cabecalho', 'linhas', 'rows');
    }
}
