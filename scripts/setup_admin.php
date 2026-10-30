<?php
/**
 * Cria (ou redefine) o usuário administrador do GESTIQ.
 *
 * Roda só pelo terminal, nunca pelo navegador:
 *   php scripts/setup_admin.php
 *
 * O e-mail e a senha vêm do .env (ADMIN_EMAIL e ADMIN_PASSWORD).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/env.php';

$email = env('ADMIN_EMAIL');
$senha = env('ADMIN_PASSWORD');
$nome  = env('ADMIN_NAME', 'Administrador');

if (!$email || !$senha) {
    fwrite(STDERR, "Defina ADMIN_EMAIL e ADMIN_PASSWORD no arquivo .env antes de rodar este script.\n");
    exit(1);
}

try {
    $dsn = 'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', '3306')
         . ';dbname=' . env('DB_NAME', 'gestiq') . ';charset=utf8mb4';
    $pdo = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, 'Falha na conexão com o banco: ' . $e->getMessage() . "\n");
    exit(1);
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE email = :email');
$stmt->execute([':email' => $email]);

if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdo->prepare("UPDATE usuarios SET senha = :senha, perfil = 'admin', ativo = 1 WHERE email = :email")
        ->execute([':senha' => $hash, ':email' => $email]);
    echo "Senha do administrador {$email} redefinida.\n";
} else {
    $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, 'admin', 1)")
        ->execute([':nome' => $nome, ':email' => $email, ':senha' => $hash]);
    echo "Administrador {$email} criado.\n";
}
