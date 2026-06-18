<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Projeto.php';

class ProjetoController
{
    private ProjetoModel $model;

    public function __construct()
    {
        Auth::check();
        $this->model = new ProjetoModel();
    }

    public function index(): void
    {
        require ROOT_PATH . '/views/projetos/index.php';
    }

    public function create(): void
    {
        Auth::require('admin', 'gestor');

        require_once ROOT_PATH . '/models/Departamento.php';
        require_once ROOT_PATH . '/models/Usuario.php';

        $deptModel = new Departamento();
        $userModel = new Usuario();

        $departamentos = $deptModel->listarSimples();
        $usuarios      = $userModel->listarAtivos();
        $status_list   = ['planejamento', 'em_andamento', 'concluido', 'cancelado'];

        $erros = [];
        $dados = [
            'titulo' => '', 'descricao' => '', 'id_departamento' => '',
            'id_responsavel' => '', 'data_inicio' => '', 'data_fim' => '', 'status' => 'planejamento'
        ];

        require ROOT_PATH . '/views/projetos/create.php';
    }

    public function store(): void
    {
        Auth::require('admin', 'gestor');

        require_once ROOT_PATH . '/models/Departamento.php';
        require_once ROOT_PATH . '/models/Usuario.php';
        require_once ROOT_PATH . '/config/database.php';

        $dados = [
            'titulo'          => trim($_POST['titulo']          ?? ''),
            'descricao'       => trim($_POST['descricao']       ?? ''),
            'id_departamento' => $_POST['id_departamento']      ?? '',
            'id_responsavel'  => $_POST['id_responsavel']       ?? '',
            'data_inicio'     => $_POST['data_inicio']          ?? '',
            'data_fim'        => $_POST['data_fim']             ?? '',
            'status'          => $_POST['status']               ?? 'planejamento',
        ];

        // ── Validação ──────────────────────────────────────────────────
        $erros = [];

        if (empty($dados['titulo'])) {
            $erros[] = 'O título do projeto é obrigatório.';
        } elseif (mb_strlen($dados['titulo']) > 200) {
            $erros[] = 'O título deve ter no máximo 200 caracteres.';
        }

        if (empty($dados['id_responsavel'])) {
            $erros[] = 'Selecione um responsável para o projeto.';
        }

        $statusValidos = ['planejamento', 'em_andamento', 'concluido', 'cancelado'];
        if (!in_array($dados['status'], $statusValidos, true)) {
            $dados['status'] = 'planejamento';
        }

        if (!empty($dados['data_inicio']) && !empty($dados['data_fim'])) {
            if ($dados['data_fim'] < $dados['data_inicio']) {
                $erros[] = 'A previsão de conclusão não pode ser anterior à data de início.';
            }
        }

        // ── Se houver erros, volta ao formulário ──────────────────────
        if (!empty($erros)) {
            $deptModel     = new Departamento();
            $userModel     = new Usuario();
            $departamentos = $deptModel->listarSimples();
            $usuarios      = $userModel->listarAtivos();
            $status_list   = $statusValidos;
            require ROOT_PATH . '/views/projetos/create.php';
            return;
        }

        // ── Salva no banco ────────────────────────────────────────────
        try {
            $pdo  = Database::getConnection();
            $sql  = "INSERT INTO projetos
                        (titulo, descricao, id_departamento, id_responsavel, data_inicio, data_fim, status)
                     VALUES
                        (:titulo, :descricao, :id_departamento, :id_responsavel, :data_inicio, :data_fim, :status)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo'          => $dados['titulo'],
                ':descricao'       => $dados['descricao'] ?: null,
                ':id_departamento' => !empty($dados['id_departamento']) ? (int)$dados['id_departamento'] : null,
                ':id_responsavel'  => (int)$dados['id_responsavel'],
                ':data_inicio'     => !empty($dados['data_inicio']) ? $dados['data_inicio'] : null,
                ':data_fim'        => !empty($dados['data_fim'])    ? $dados['data_fim']    : null,
                ':status'          => $dados['status'],
            ]);

            $_SESSION['flash'] = [
                'tipo' => 'success',
                'msg'  => 'Projeto <strong>' . htmlspecialchars($dados['titulo']) . '</strong> criado com sucesso!'
            ];
            header('Location: ' . BASE_URL . '/projetos');
            exit;

        } catch (PDOException $e) {
            $erros[] = 'Erro ao salvar no banco de dados: ' . $e->getMessage();

            $deptModel     = new Departamento();
            $userModel     = new Usuario();
            $departamentos = $deptModel->listarSimples();
            $usuarios      = $userModel->listarAtivos();
            $status_list   = $statusValidos;
            require ROOT_PATH . '/views/projetos/create.php';
        }
    }

    public function show(int $id): void
    {
        $projeto = $this->model->buscarPorId($id);
        if (!$projeto) {
            $_SESSION['flash'] = ['tipo' => 'warning', 'msg' => 'Projeto não encontrado.'];
            header('Location: ' . BASE_URL . '/projetos');
            exit;
        }
        require ROOT_PATH . '/views/projetos/show.php';
    }

    public function edit(int $id): void
    {
        Auth::require('admin', 'gestor');
        $projeto = $this->model->buscarPorId($id);
        if (!$projeto) {
            $_SESSION['flash'] = ['tipo' => 'warning', 'msg' => 'Projeto não encontrado.'];
            header('Location: ' . BASE_URL . '/projetos');
            exit;
        }

        require_once ROOT_PATH . '/models/Departamento.php';
        require_once ROOT_PATH . '/models/Usuario.php';
        $departamentos = (new Departamento())->listarSimples();
        $usuarios      = (new Usuario())->listarAtivos();
        $status_list   = ['planejamento', 'em_andamento', 'concluido', 'cancelado'];
        $dados         = $projeto;
        $erros         = [];

        require ROOT_PATH . '/views/projetos/edit.php';
    }

    public function update(int $id): void
    {
        Auth::require('admin', 'gestor');
        // mesma lógica do store() mas com UPDATE — implemente quando criar a view de edição
        header('Location: ' . BASE_URL . '/projetos');
        exit;
    }

    public function destroy(int $id): void
    {
        Auth::require('admin');
        try {
            $pdo  = Database::getConnection();
            $stmt = $pdo->prepare("DELETE FROM projetos WHERE id_projeto = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Projeto excluído com sucesso.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => 'Erro ao excluir: ' . $e->getMessage()];
        }
        header('Location: ' . BASE_URL . '/projetos');
        exit;
    }

    public function atualizarStatus(int $id): void
    {
        Auth::require('admin', 'gestor');
        $novoStatus    = $_POST['status'] ?? '';
        $statusValidos = ['planejamento', 'em_andamento', 'concluido', 'cancelado'];

        if (in_array($novoStatus, $statusValidos, true)) {
            $pdo  = Database::getConnection();
            $stmt = $pdo->prepare("UPDATE projetos SET status = :status WHERE id_projeto = :id");
            $stmt->execute([':status' => $novoStatus, ':id' => $id]);
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Status atualizado com sucesso.'];
        }

        header('Location: ' . BASE_URL . '/projetos/' . $id);
        exit;
    }
}