<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Cargo.php';
require_once ROOT_PATH . '/models/Departamento.php';

class CargoController
{
    private Cargo $model;
    private Departamento $modelDept;

    public function __construct()
    {
        Auth::check();
        $this->model     = new Cargo();
        $this->modelDept = new Departamento();
    }

    public function index(): void
    {
        $filtros = [];
        if (!empty($_GET['busca']))           $filtros['busca']           = $_GET['busca'];
        if (!empty($_GET['id_departamento'])) $filtros['id_departamento'] = (int) $_GET['id_departamento'];

        $cargos        = $this->model->listar($filtros);
        $departamentos = $this->modelDept->listarSimples();

        require ROOT_PATH . '/views/cargos/index.php';
    }

    public function create(): void
    {
        Auth::require('admin', 'gestor');
        $erros         = [];
        $dados         = [];
        $departamentos = $this->modelDept->listarSimples();
        require ROOT_PATH . '/views/cargos/create.php';
    }

    public function store(): void
    {
        Auth::require('admin', 'gestor');
        $dados = [
            'nome'            => $_POST['nome']            ?? '',
            'descricao'       => $_POST['descricao']       ?? '',
            'id_departamento' => $_POST['id_departamento'] ?? '',
        ];

        $erros = $this->model->validar($dados);
        if (!empty($erros)) {
            $departamentos = $this->modelDept->listarSimples();
            require ROOT_PATH . '/views/cargos/create.php';
            return;
        }

        $id = $this->model->criar($dados);
        if ($id) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Cargo criado com sucesso!'];
            header('Location: ' . BASE_URL . '/cargos');
            exit;
        }

        $erros         = ['Erro ao criar cargo. Tente novamente.'];
        $departamentos = $this->modelDept->listarSimples();
        require ROOT_PATH . '/views/cargos/create.php';
    }

    public function show(int $id): void
    {
        $cargo = $this->model->buscarPorId($id);
        if (!$cargo) {
            http_response_code(404);
            echo '<div class="alert alert-danger">Cargo não encontrado.</div>';
            return;
        }

        $usuarios = $this->model->listarUsuarios($id);
        require ROOT_PATH . '/views/cargos/show.php';
    }

    public function edit(int $id): void
    {
        Auth::require('admin', 'gestor');
        $cargo = $this->model->buscarPorId($id);
        if (!$cargo) {
            http_response_code(404);
            return;
        }

        $erros         = [];
        $dados         = $cargo;
        $departamentos = $this->modelDept->listarSimples();
        require ROOT_PATH . '/views/cargos/edit.php';
    }

    public function update(int $id): void
    {
        Auth::require('admin', 'gestor');
        $cargo = $this->model->buscarPorId($id);
        if (!$cargo) {
            http_response_code(404);
            return;
        }

        $dados = [
            'nome'            => $_POST['nome']            ?? '',
            'descricao'       => $_POST['descricao']       ?? '',
            'id_departamento' => $_POST['id_departamento'] ?? '',
        ];

        $erros = $this->model->validar($dados, $id);
        if (!empty($erros)) {
            $departamentos = $this->modelDept->listarSimples();
            require ROOT_PATH . '/views/cargos/edit.php';
            return;
        }

        if ($this->model->atualizar($id, $dados)) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Cargo atualizado com sucesso!'];
            header('Location: ' . BASE_URL . '/cargos/' . $id);
            exit;
        }

        $erros         = ['Erro ao atualizar. Tente novamente.'];
        $departamentos = $this->modelDept->listarSimples();
        require ROOT_PATH . '/views/cargos/edit.php';
    }

    public function destroy(int $id): void
    {
        Auth::require('admin');
        $resultado = $this->model->excluir($id);

        if ($resultado === true) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Cargo excluído com sucesso!'];
        } else {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => is_string($resultado) ? $resultado : 'Erro ao excluir.'];
        }

        header('Location: ' . BASE_URL . '/cargos');
        exit;
    }
}
