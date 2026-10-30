<?php
require_once 'AgendaService.php';

// Mostra erros na tela só quando APP_DEBUG=true no .env
$debug = filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

$agenda = new AgendaService();
$urlAutenticacao = $agenda->getAuthUrl();

// Redireciona o usuário diretamente para a tela de login do Google
header('Location: ' . $urlAutenticacao);
exit;