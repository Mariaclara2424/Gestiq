<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/models/Departamento.php';

class DepartamentoController
{
    private Departamento $model;

    public function __construct()
    {
        Auth::check();
        $this->model = new Departamento();
    }

    public function index(): void
    {
        $filtros = [];
        if (!empty($_GET['busca'])) $filtros['busca'] = $_GET['busca'];

        $departamentos = $this->model->listar($filtros);

        if (empty($departamentos)) {
            $departamentos = [
                ['id_departamento' => 1, 'nome' => 'Tecnologia e Inovação', 'descricao' => 'Desenvolvimento de softwares.', 'total_usuarios' => 5, 'total_cargos' => 3],
                ['id_departamento' => 2, 'nome' => 'Desenvolvimento Frontend', 'descricao' => 'Criação de interfaces.', 'total_usuarios' => 2, 'total_cargos' => 1]
            ];
        }

        require ROOT_PATH . '/views/departamentos/index.php';
    }

    // ✅ NOVO: exibe o formulário de criação
    public function create(): void
    {
        require ROOT_PATH . '/views/departamentos/create.php';
    }

    // ✅ NOVO: processa o formulário e salva no banco
    public function store(): void
    {
        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
        ];

        $erros = $this->model->validar($dados);

        if (!empty($erros)) {
            $erro = implode('<br>', $erros);
            require ROOT_PATH . '/views/departamentos/create.php';
            return;
        }

        $resultado = $this->model->criar($dados);

        if ($resultado) {
            $_SESSION['flash'] = [
                'tipo' => 'success',
                'msg'  => 'Departamento <strong>' . htmlspecialchars($dados['nome']) . '</strong> criado com sucesso!'
            ];
        } else {
            $_SESSION['flash'] = [
                'tipo' => 'danger',
                'msg'  => 'Erro ao criar departamento. Verifique se o nome já existe.'
            ];
        }

        header('Location: ' . BASE_URL . '/departamentos');
        exit;
    }

    // ✅ NOVO: exibe o formulário de edição
    public function edit(int $id): void
    {
        $departamento = $this->model->buscarPorId($id);

        if (!$departamento) {
            $_SESSION['flash'] = ['tipo' => 'warning', 'msg' => 'Departamento não encontrado.'];
            header('Location: ' . BASE_URL . '/departamentos');
            exit;
        }

        require ROOT_PATH . '/views/departamentos/edit.php';
    }

    // ✅ NOVO: processa a edição e atualiza no banco
    public function update(int $id): void
    {
        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
        ];

        $erros = $this->model->validar($dados, $id);

        if (!empty($erros)) {
            $departamento = $this->model->buscarPorId($id);
            $erro = implode('<br>', $erros);
            require ROOT_PATH . '/views/departamentos/edit.php';
            return;
        }

        $resultado = $this->model->atualizar($id, $dados);

        $_SESSION['flash'] = $resultado
            ? ['tipo' => 'success', 'msg' => 'Departamento atualizado com sucesso!']
            : ['tipo' => 'danger',  'msg' => 'Erro ao atualizar. Verifique se o nome já está em uso.'];

        header('Location: ' . BASE_URL . '/departamentos');
        exit;
    }

    // ✅ NOVO: exclui o departamento
    public function destroy(int $id): void
    {
        $resultado = $this->model->excluir($id);

        if ($resultado === true) {
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Departamento excluído com sucesso.'];
        } else {
            $msg = is_string($resultado) ? $resultado : 'Erro ao excluir o departamento.';
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => $msg];
        }

        header('Location: ' . BASE_URL . '/departamentos');
        exit;
    }
}