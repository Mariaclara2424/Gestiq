<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Usuario.php';
require_once ROOT_PATH . '/models/Departamento.php';
require_once ROOT_PATH . '/models/Cargo.php';

class UsuarioController
{
    private Usuario $model;
    private Departamento $modelDept;
    private Cargo $modelCargo;

    public function __construct()
    {
        Auth::check();
        $this->model      = new Usuario();
        $this->modelDept  = new Departamento();
        $this->modelCargo = new Cargo();
    }

    public function index(): void
    {
        Auth::require('admin', 'gestor');

        $filtros = [];
        if (!empty($_GET['busca']))           $filtros['busca']           = $_GET['busca'];
        if (!empty($_GET['perfil']))           $filtros['perfil']          = $_GET['perfil'];
        if (!empty($_GET['id_departamento'])) $filtros['id_departamento'] = (int) $_GET['id_departamento'];
        if (isset($_GET['ativo']) && $_GET['ativo'] !== '') $filtros['ativo'] = (int) $_GET['ativo'];

        $usuarios      = $this->model->listar($filtros);
        $departamentos = $this->modelDept->listarSimples();
        $perfis        = Usuario::PERFIS;

        require ROOT_PATH . '/views/usuarios/index.php';
    }

    public function create(): void
    {
        Auth::require('admin');
        $erros         = [];
        $dados         = [];
        $departamentos = $this->modelDept->listarSimples();
        $cargos        = $this->modelCargo->listarSimples();
        $perfis        = Usuario::PERFIS;
        require ROOT_PATH . '/views/usuarios/create.php';
    }

    public function store(): void
    {
        Auth::require('admin');
        $dados = [
            'nome'            => $_POST['nome']            ?? '',
            'email'           => $_POST['email']           ?? '',
            'senha'           => $_POST['senha']           ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'perfil'          => $_POST['perfil']          ?? '',
            'id_departamento' => $_POST['id_departamento'] ?? '',
            'id_cargo'        => $_POST['id_cargo']        ?? '',
            'ativo'           => $_POST['ativo']           ?? 1,
        ];

        $erros = $this->model->validar($dados, 0, true);
        if (!empty($erros)) {
            $departamentos = $this->modelDept->listarSimples();
            $cargos        = $this->modelCargo->listarSimples();
            $perfis        = Usuario::PERFIS;
            require ROOT_PATH . '/views/usuarios/create.php';
            return;
        }

        $id = $this->model->criar($dados);
        if ($id) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Usuário criado com sucesso!'];
            header('Location: ' . BASE_URL . '/usuarios');
            exit;
        }

        $erros         = ['Erro ao criar usuário. Tente novamente.'];
        $departamentos = $this->modelDept->listarSimples();
        $cargos        = $this->modelCargo->listarSimples();
        $perfis        = Usuario::PERFIS;
        require ROOT_PATH . '/views/usuarios/create.php';
    }

    public function show(int $id): void
    {
        Auth::require('admin', 'gestor');
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo '<div class="alert alert-danger">Usuário não encontrado.</div>';
            return;
        }
        require ROOT_PATH . '/views/usuarios/show.php';
    }

    public function edit(int $id): void
    {
        Auth::require('admin');
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            return;
        }

        $erros         = [];
        $dados         = $usuario;
        $departamentos = $this->modelDept->listarSimples();
        $cargos        = $this->modelCargo->listarSimples();
        $perfis        = Usuario::PERFIS;
        require ROOT_PATH . '/views/usuarios/edit.php';
    }

    public function update(int $id): void
    {
        Auth::require('admin');
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            return;
        }

        $dados = [
            'nome'            => $_POST['nome']            ?? '',
            'email'           => $_POST['email']           ?? '',
            'senha'           => $_POST['senha']           ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'perfil'          => $_POST['perfil']          ?? '',
            'id_departamento' => $_POST['id_departamento'] ?? '',
            'id_cargo'        => $_POST['id_cargo']        ?? '',
            'ativo'           => $_POST['ativo']           ?? 1,
        ];

        $erros = $this->model->validar($dados, $id, false);
        if (!empty($erros)) {
            $departamentos = $this->modelDept->listarSimples();
            $cargos        = $this->modelCargo->listarSimples();
            $perfis        = Usuario::PERFIS;
            require ROOT_PATH . '/views/usuarios/edit.php';
            return;
        }

        if ($this->model->atualizar($id, $dados)) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Usuário atualizado com sucesso!'];
            header('Location: ' . BASE_URL . '/usuarios/' . $id);
            exit;
        }

        $erros         = ['Erro ao atualizar. Tente novamente.'];
        $departamentos = $this->modelDept->listarSimples();
        $cargos        = $this->modelCargo->listarSimples();
        $perfis        = Usuario::PERFIS;
        require ROOT_PATH . '/views/usuarios/edit.php';
    }

    public function toggleAtivo(int $id): void
    {
        Auth::require('admin');
        $usuario = $this->model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            return;
        }

        $novoStatus = $usuario['ativo'] ? 0 : 1;
        $this->model->alterarStatus($id, $novoStatus);

        $msg = $novoStatus ? 'Usuário ativado.' : 'Usuário desativado.';
        $_SESSION['flash'] = ['tipo' => 'success', 'msg' => $msg];
        header('Location: ' . BASE_URL . '/usuarios');
        exit;
    }

    public function destroy(int $id): void
    {
        Auth::require('admin');

        // Impede auto-exclusão
        if ($id === (int) Auth::user()['id']) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => 'Você não pode excluir sua própria conta.'];
            header('Location: ' . BASE_URL . '/usuarios');
            exit;
        }

        $resultado = $this->model->excluir($id);

        if ($resultado === true) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Usuário excluído com sucesso!'];
        } else {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => is_string($resultado) ? $resultado : 'Erro ao excluir.'];
        }

        header('Location: ' . BASE_URL . '/usuarios');
        exit;
    }

    public function perfil(): void
    {
        $id      = (int) Auth::user()['id'];
        $usuario = $this->model->buscarPorId($id);
        $erros   = [];
        require ROOT_PATH . '/views/usuarios/perfil.php';
    }

    public function atualizarPerfil(): void
    {
        $id      = (int) Auth::user()['id'];
        $usuario = $this->model->buscarPorId($id);

        $dados = [
            'nome'            => $_POST['nome']            ?? '',
            'email'           => $_POST['email']           ?? '',
            'senha'           => $_POST['senha']           ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'perfil'          => $usuario['perfil'],       // mantém perfil atual
            'id_departamento' => $usuario['id_departamento'],
            'id_cargo'        => $usuario['id_cargo'],
            'ativo'           => 1,
        ];

        $erros = $this->model->validar($dados, $id, false);
        if (empty($erros)) {
            $this->model->atualizar($id, $dados);

            // Atualiza sessão com novo nome/email
            $_SESSION['usuario']['nome']  = trim($dados['nome']);
            $_SESSION['usuario']['email'] = mb_strtolower(trim($dados['email']));

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Perfil atualizado com sucesso!'];
            header('Location: ' . BASE_URL . '/perfil');
            exit;
        }

        require ROOT_PATH . '/views/usuarios/perfil.php';
    }
}
