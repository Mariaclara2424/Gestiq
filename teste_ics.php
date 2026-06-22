<?php
// Exibe erros na tela para facilitar o debug local
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'IcsGenerator.php';

// Simulando dados que viriam do seu banco de dados via PDO
$titulo = "Reunião de Alinhamento - Gestiq";
$inicio = "2026-06-25 14:00:00"; // Formato do banco (Y-m-d H:i:s)
$fim    = "2026-06-25 15:30:00";
$desc   = "Discussão sobre a integração definitiva da API do Google Calendar.";

// Chama o método estático que gera o download
IcsGenerator::download($titulo, $inicio, $fim, $desc);