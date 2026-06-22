<?php
// Configurações Globais (Localhost - Seu Computador)
// Ajustado para refletir o nome exato da pasta criada pelo Git
define('BASE_URL', 'http://localhost/gestiq'); 
define('ROOT_PATH', dirname(__DIR__));

// Credenciais do Banco de Dados (Local XAMPP)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'gestiq');
define('DB_USER', 'root');
define('DB_PASS', ''); // ✅ Corrigido: Sem senha para o root no XAMPP local
define('DB_PORT', '3306');

// Configuração de sessão para o login
session_name('gestiq_session');
session_start();