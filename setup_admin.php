<?php
// ============================================================
//  GestIQ — Setup do Administrador
//  Salve este arquivo em: C:/xampp/htdocs/gestiq/setup_admin.php
//  Acesse em:            http://localhost/gestiq/setup_admin.php
//  ⚠️  APAGUE este arquivo após usar!
// ============================================================

// Credenciais do banco (iguais ao seu config.php)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'gestiq');
define('DB_USER', 'root');
define('DB_PASS', 'ceub123456');

$ADMIN_EMAIL = 'admin@gestiq.com';
$ADMIN_NOME  = 'Administrador';
$ADMIN_SENHA = '123456';

// --- Conexão ---
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(renderCard('danger', '❌ Falha na conexão', $e->getMessage()));
}

// --- Gera o hash ---
$hash = password_hash($ADMIN_SENHA, PASSWORD_DEFAULT);

// --- Verifica se já existe ---
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $ADMIN_EMAIL]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Atualiza a senha
    $upd = $pdo->prepare("UPDATE usuarios SET senha = :senha, perfil = 'admin', ativo = 1 WHERE email = :email");
    $upd->execute([':senha' => $hash, ':email' => $ADMIN_EMAIL]);
    $acao = 'Senha atualizada';
} else {
    // Insere o usuário
    $ins = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, 'admin', 1)");
    $ins->execute([':nome' => $ADMIN_NOME, ':email' => $ADMIN_EMAIL, ':senha' => $hash]);
    $acao = 'Usuário criado';
}

echo renderCard('success', '✅ Administrador configurado com sucesso!', "
    <p><strong>E-mail:</strong> {$ADMIN_EMAIL}</p>
    <p><strong>Senha:</strong> {$ADMIN_SENHA}</p>
    <p><strong>Ação:</strong> {$acao}</p>
    <hr>
    <p class='text-danger fw-bold'>⚠️ Apague este arquivo agora: <code>gestiq/setup_admin.php</code></p>
    <a href='http://localhost/gestiq/login' class='btn btn-primary mt-2'>Ir para o Login →</a>
");

// --- Helper de layout ---
function renderCard(string $tipo, string $titulo, string $corpo): string {
    $cor = $tipo === 'success' ? '#198754' : '#dc3545';
    return <<<HTML
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
      <meta charset="UTF-8">
      <title>GestIQ Setup</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
      <div class="card shadow-lg border-0" style="max-width:480px;width:100%;border-top:5px solid {$cor}!important;border-radius:1rem;overflow:hidden">
        <div class="card-body p-5">
          <h4 class="fw-bold mb-4">{$titulo}</h4>
          {$corpo}
        </div>
      </div>
    </body>
    </html>
    HTML;
}