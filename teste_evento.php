<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'AgendaService.php';

try {
    $agenda = new AgendaService();
    
    $usuario_id = 1; // ID que salvamos na tabela
    $titulo = "🚀 Reunião de Alinhamento - Gestiq";
    $descricao = "Este é um evento de teste criado 100% via código PHP!";
    
    // Formata a data no padrão exigido pelo Google (ISO 8601) para amanhã às 14:00
    $dataInicio = date('Y-m-d\T14:00:00', strtotime('+1 day'));
    $dataFim    = date('Y-m-d\T15:00:00', strtotime('+1 day'));

    echo "Tentando criar o evento no Google Calendar...<br><br>";

    $linkDoEvento = $agenda->criarEvento($usuario_id, $titulo, $descricao, $dataInicio, $dataFim);

    echo "<h2>🎉 Evento criado com Sucesso!</h2>";
    echo "<p><a href='{$linkDoEvento}' target='_blank'>Clique aqui para ver o evento no seu Google Agenda</a></p>";

} catch (Exception $e) {
    echo "<h2>Erro ao criar evento:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>