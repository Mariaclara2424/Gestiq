<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'AgendaService.php';

$agenda = new AgendaService();
$urlAutenticacao = $agenda->getAuthUrl();

// Redireciona o usuário diretamente para a tela de login do Google
header('Location: ' . $urlAutenticacao);
exit;