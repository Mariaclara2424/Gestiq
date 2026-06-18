<?php
require_once ROOT_PATH . '/config/database.php';
class Auth {
    public static function login(string $email, string $senha): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id_usuario, nome, email, senha, perfil, ativo FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !$usuario['ativo'] || !password_verify($senha, $usuario['senha'])) { return false; }
        session_regenerate_id(true);
        $_SESSION['usuario'] = ['id' => $usuario['id_usuario'], 'nome' => $usuario['nome'], 'email' => $usuario['email'], 'perfil' => $usuario['perfil']];
        return true;
    }
    public static function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
    public static function check(): void {
        if (empty($_SESSION['usuario'])) { header('Location: ' . BASE_URL . '/login'); exit; }
    }
    public static function can(string ...$perfis): bool {
        return in_array($_SESSION['usuario']['perfil'] ?? '', $perfis, true);
    }
    public static function require(string ...$perfis): void {
        self::check();
        if (!self::can(...$perfis)) { http_response_code(403); die('Acesso negado.'); }
    }
    public static function user(): ?array { return $_SESSION['usuario'] ?? null; }
}
