<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Tarefa.php';
require_once ROOT_PATH . '/models/Comentario.php';
require_once ROOT_PATH . '/models/Projeto.php';
require_once ROOT_PATH . '/models/Usuario.php';

class TarefaController
{
    private Tarefa $model;
    private Comentario $modelComentario;
    private ProjetoModel $modelProjeto;
    private Usuario $modelUsuario;

    public function __construct()
    {
        Auth::check();
        $this->model            = new Tarefa();
        $this->modelComentario  = new Comentario();
        $this->modelProjeto     = new ProjetoModel();
        $this->modelUsuario     = new Usuario();
    }

    public function index(): void
    {
        $u = Auth::user();
        $filtros = ['_usuario_id' => $u['id'], '_perfil' => $u['perfil']];

        if (!empty($_GET['busca']))          $filtros['busca']         = $_GET['busca'];
        if (!empty($_GET['status']))          $filtros['status']        = $_GET['status'];
        if (!empty($_GET['prioridade']))      $filtros['prioridade']    = $_GET['prioridade'];
        if (!empty($_GET['id_responsavel']))  $filtros['id_responsavel'] = (int) $_GET['id_responsavel'];
        if (!empty($_GET['id_projeto']))      $filtros['id_projeto']    = (int) $_GET['id_projeto'];
        if (!empty($_GET['atrasadas']))       $filtros['atrasadas']     = true;

        $tarefas   = $this->model->listar($filtros);
        $usuarios  = $this->modelUsuario->listarAtivos();
        $projetos  = $this->modelProjeto->listar([]);
        $status    = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;

        require ROOT_PATH . '/views/tarefas/index.php';
    }

    public function create(): void
    {
        $idProjeto = !empty($_GET['id_projeto']) ? (int) $_GET['id_projeto'] : 0;
        $projeto   = $idProjeto ? $this->modelProjeto->buscarPorId($idProjeto) : null;

        // Colaborador só pode criar tarefa em projeto que for responsável ou participante
        if (Auth::can('colaborador') && !$projeto) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => 'Selecione um projeto válido.'];
            header('Location: ' . BASE_URL . '/tarefas');
            exit;
        }

        $erros     = [];
        $dados     = [
            'id_projeto'  => $idProjeto,
            'status'      => 'pendente',
            'prioridade'  => 'media',
        ];
        $usuarios  = $this->modelUsuario->listarAtivos();
        $projetos  = $this->modelProjeto->listar([]);
        $status    = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;

        require ROOT_PATH . '/views/tarefas/create.php';
    }

    public function store(): void
    {
        $dados = [
            'titulo'          => $_POST['titulo']          ?? '',
            'descricao'       => $_POST['descricao']       ?? '',
            'id_projeto'      => $_POST['id_projeto']      ?? '',
            'id_responsavel'  => $_POST['id_responsavel']  ?? '',
            'data_vencimento' => $_POST['data_vencimento'] ?? '',
            'status'          => $_POST['status']          ?? 'pendente',
            'prioridade'      => $_POST['prioridade']      ?? 'media',
        ];

        $erros = $this->model->validar($dados);
        if (!empty($erros)) {
            $usuarios    = $this->modelUsuario->listarAtivos();
            $projetos    = $this->modelProjeto->listar([]);
            $status      = Tarefa::STATUS;
            $prioridades = Tarefa::PRIORIDADE;
            require ROOT_PATH . '/views/tarefas/create.php';
            return;
        }

        $id = $this->model->criar($dados);
        if ($id) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Tarefa criada com sucesso!'];
            $redir = !empty($dados['id_projeto'])
                ? BASE_URL . '/projetos/' . $dados['id_projeto']
                : BASE_URL . '/tarefas/' . $id;
            header('Location: ' . $redir);
            exit;
        }

        $erros       = ['Erro ao criar tarefa.'];
        $usuarios    = $this->modelUsuario->listarAtivos();
        $projetos    = $this->modelProjeto->listar([]);
        $status      = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;
        require ROOT_PATH . '/views/tarefas/create.php';
    }

    public function show(int $id): void
    {
        $tarefa = $this->model->buscarPorId($id);
        if (!$tarefa) {
            http_response_code(404);
            echo '<div class="alert alert-danger">Tarefa não encontrada.</div>';
            return;
        }

        $comentarios = $this->modelComentario->listarPorTarefa($id);
        $usuarios    = $this->modelUsuario->listarAtivos();
        $status      = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;

        require ROOT_PATH . '/views/tarefas/show.php';
    }

    public function edit(int $id): void
    {
        $tarefa = $this->model->buscarPorId($id);
        if (!$tarefa) {
            http_response_code(404);
            return;
        }

        // Colaborador só edita suas próprias tarefas
        if (Auth::can('colaborador') && $tarefa['id_responsavel'] !== Auth::user()['id']) {
            http_response_code(403);
            die('Acesso negado.');
        }

        $erros     = [];
        $dados     = $tarefa;
        $usuarios  = $this->modelUsuario->listarAtivos();
        $projetos  = $this->modelProjeto->listar([]);
        $status    = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;

        require ROOT_PATH . '/views/tarefas/edit.php';
    }

    public function update(int $id): void
    {
        $tarefa = $this->model->buscarPorId($id);
        if (!$tarefa) {
            http_response_code(404);
            return;
        }

        if (Auth::can('colaborador') && $tarefa['id_responsavel'] !== Auth::user()['id']) {
            http_response_code(403);
            die('Acesso negado.');
        }

        $dados = [
            'titulo'          => $_POST['titulo']          ?? '',
            'descricao'       => $_POST['descricao']       ?? '',
            'id_projeto'      => $_POST['id_projeto']      ?? $tarefa['id_projeto'],
            'id_responsavel'  => $_POST['id_responsavel']  ?? '',
            'data_vencimento' => $_POST['data_vencimento'] ?? '',
            'status'          => $_POST['status']          ?? '',
            'prioridade'      => $_POST['prioridade']      ?? '',
        ];

        $erros = $this->model->validar($dados);
        if (!empty($erros)) {
            $usuarios    = $this->modelUsuario->listarAtivos();
            $projetos    = $this->modelProjeto->listar([]);
            $status      = Tarefa::STATUS;
            $prioridades = Tarefa::PRIORIDADE;
            require ROOT_PATH . '/views/tarefas/edit.php';
            return;
        }

        if ($this->model->atualizar($id, $dados)) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Tarefa atualizada com sucesso!'];
            header('Location: ' . BASE_URL . '/tarefas/' . $id);
            exit;
        }

        $erros       = ['Erro ao atualizar tarefa.'];
        $usuarios    = $this->modelUsuario->listarAtivos();
        $projetos    = $this->modelProjeto->listar([]);
        $status      = Tarefa::STATUS;
        $prioridades = Tarefa::PRIORIDADE;
        require ROOT_PATH . '/views/tarefas/edit.php';
    }

    public function atualizarStatus(int $id): void
    {
        $tarefa = $this->model->buscarPorId($id);
        if (!$tarefa) {
            http_response_code(404);
            return;
        }

        $status = $_POST['status'] ?? '';
        $this->model->atualizarStatus($id, $status);

        $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Status atualizado!'];
        header('Location: ' . BASE_URL . '/tarefas/' . $id);
        exit;
    }

    public function destroy(int $id): void
    {
        Auth::require('admin', 'gestor');
        $tarefa = $this->model->buscarPorId($id);
        $idProjeto = $tarefa['id_projeto'] ?? 0;

        $this->model->excluir($id);
        $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Tarefa excluída com sucesso!'];

        $redir = $idProjeto ? BASE_URL . '/projetos/' . $idProjeto : BASE_URL . '/tarefas';
        header('Location: ' . $redir);
        exit;
    }
}
