<?php
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/config/database.php';

class AuthController
{
    /* =========================================================
       1. MÉTODOS DE LOGIN PADRÃO 
       ========================================================= */
       
    public function showLogin(): void
    {
        if (!empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        $erro = $_SESSION['login_erro'] ?? null;
        $sucesso = $_SESSION['login_sucesso'] ?? null;
        unset($_SESSION['login_erro'], $_SESSION['login_sucesso']);
        
        require ROOT_PATH . '/views/auth/login.php';
    }

    public function doLogin(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $erro = 'Preencha e-mail e senha.';
            require ROOT_PATH . '/views/auth/login.php';
            return;
        }

        if (Auth::login($email, $senha)) {
            header('Location: ' . BASE_URL . '/dashboard'); // ✅ BUG 1: agora gera URL sem barra dupla
            exit;
        }

        $erro = 'E-mail ou senha incorretos.';
        require ROOT_PATH . '/views/auth/login.php'; // ✅ BUG 3: a view agora exibe $erro
    }

    public function doLogout(): void
    {
        Auth::logout();
    }

    /* =========================================================
       2. MÉTODOS DE CADASTRO
       ========================================================= */

    public function showCadastro(): void
    {
        require ROOT_PATH . '/views/auth/cadastro.php';
    }

    public function doCadastro(): void
    {
        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $erro = 'Todos os campos são obrigatórios.';
            require ROOT_PATH . '/views/auth/cadastro.php';
            return;
        }

        try {
            $pdo = Database::getConnection();
            
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $erro = 'Este e-mail já está cadastrado em nosso sistema.';
                require ROOT_PATH . '/views/auth/cadastro.php';
                return;
            }

            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'senha' => $hashSenha
            ]);

            $_SESSION['login_sucesso'] = 'Conta criada com sucesso! Você já pode fazer o login.';
            header('Location: ' . BASE_URL . '/login');
            exit;

        } catch (PDOException $e) {
            $erro = 'Erro crítico ao tentar cadastrar: ' . $e->getMessage();
            require ROOT_PATH . '/views/auth/cadastro.php';
        }
    }

    /* =========================================================
       3. MÉTODOS DE RECUPERAÇÃO DE SENHA 
       ========================================================= */

    public function showRecuperarSenha(): void
    {
        require ROOT_PATH . '/views/auth/recuperar_senha.php';
    }

    public function processarRecuperacao(): void
    {
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');

        if (empty($email)) {
            $erro = 'Por favor, informe seu e-mail.';
            require ROOT_PATH . '/views/auth/recuperar_senha.php';
            return;
        }

        $sucesso = 'Se este e-mail estiver em nossa base de dados, você receberá as instruções de recuperação em instantes.';
        require ROOT_PATH . '/views/auth/recuperar_senha.php';
    }
}