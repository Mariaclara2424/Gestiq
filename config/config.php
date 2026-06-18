<?php
// Configurações Globais (Localhost - Computador da Faculdade)
// Ajuste o BASE_URL caso sua pasta se chame diferente de "gestiq"
define('BASE_URL', 'http://localhost/gestiq'); // ✅ BUG 1 CORRIGIDO: removida a barra do final
define('ROOT_PATH', dirname(__DIR__));

// Credenciais do Banco de Dados (Local)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'gestiq');
define('DB_USER', 'root');
define('DB_PASS', 'ceub123456'); // Senha do banco da faculdade
define('DB_PORT', '3306');

// Configuração de sessão para o login
session_name('gestiq_session');
session_start();

// ✅ BUG 2 CORRIGIDO: conexão PDO removida daqui.
// A conexão é gerenciada exclusivamente pela classe Database em config/database.php.
// Ter duas conexões abertas causava conflito silencioso no fluxo de autenticação.