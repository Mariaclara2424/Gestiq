<?php
// Configurações globais. Os valores vêm do arquivo .env (veja .env.example).
require_once __DIR__ . '/env.php';

define('BASE_URL', env('BASE_URL', 'http://localhost/gestiq'));
define('ROOT_PATH', dirname(__DIR__));

// Credenciais do banco de dados
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_NAME', env('DB_NAME', 'gestiq'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_PORT', env('DB_PORT', '3306'));

// Configuração de sessão para o login
session_name('gestiq_session');
session_start();