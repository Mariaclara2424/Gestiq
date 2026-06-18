<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Comentario.php';
require_once ROOT_PATH . '/models/Tarefa.php';

class ComentarioController
{
    private Comentario $model;
    private Tarefa $modelTarefa;

    public function __construct()
    {
        Auth::check();
        $this->model        = new Comentario();
        $this->modelTarefa  = new Tarefa();
    }

    public function store(int $idTarefa): void
    {
        $tarefa = $this->modelTarefa->buscarPorId($idTarefa);
        if (!$tarefa) {
            http_response_code(404);
            return;
        }

        $dados = [
            'id_tarefa'  => $idTarefa,
            'id_usuario' => (int) Auth::user()['id'],
            'texto'      => $_POST['texto'] ?? '',
        ];

        $erros = $this->model->validar($dados);
        if (!empty($erros)) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => implode(' ', $erros)];
        } else {
            $this->model->criar($dados);
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Comentário adicionado!'];
        }

        header('Location: ' . BASE_URL . '/tarefas/' . $idTarefa);
        exit;
    }

    public function edit(int $id): void
    {
        $comentario = $this->model->buscarPorId($id);
        if (!$comentario) {
            http_response_code(404);
            return;
        }

        $u = Auth::user();
        if ($comentario['id_usuario'] !== $u['id'] && !Auth::can('admin')) {
            http_response_code(403);
            die('Acesso negado.');
        }

        require ROOT_PATH . '/views/comentarios/edit.php';
    }

    public function update(int $id): void
    {
        $comentario = $this->model->buscarPorId($id);
        if (!$comentario) {
            http_response_code(404);
            return;
        }

        $u    = Auth::user();
        $uid  = (int) $u['id'];
        $isAdmin = Auth::can('admin');

        if ($comentario['id_usuario'] !== $uid && !$isAdmin) {
            http_response_code(403);
            die('Acesso negado.');
        }

        $texto = trim($_POST['texto'] ?? '');
        if (empty($texto)) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => 'O comentário não pode estar vazio.'];
            header('Location: ' . BASE_URL . '/tarefas/' . $comentario['id_tarefa']);
            exit;
        }

        $this->model->atualizar($id, $texto, $uid);
        $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Comentário atualizado!'];
        header('Location: ' . BASE_URL . '/tarefas/' . $comentario['id_tarefa']);
        exit;
    }

    public function destroy(int $id): void
    {
        $comentario = $this->model->buscarPorId($id);
        if (!$comentario) {
            http_response_code(404);
            return;
        }

        $u       = Auth::user();
        $uid     = (int) $u['id'];
        $isAdmin = Auth::can('admin');

        if ($comentario['id_usuario'] !== $uid && !$isAdmin) {
            http_response_code(403);
            die('Acesso negado.');
        }

        $this->model->excluir($id, $uid, $isAdmin);
        $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Comentário excluído!'];
        header('Location: ' . BASE_URL . '/tarefas/' . $comentario['id_tarefa']);
        exit;
    }
}
