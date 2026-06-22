<?php

class IcsGenerator {
    
    /**
     * Gera e força o download de um arquivo .ics
     */
    public static function download($title, $dateStart, $dateEnd, $description) {
        // Formata as datas para o padrão ICS (AAAAMMDDTHHMMSS)
        // Usar DateTime ajuda a evitar erros de string
        $start = (new DateTime($dateStart))->format('Ymd\THis');
        $end = (new DateTime($dateEnd))->format('Ymd\THis');
        $stamp = (new DateTime())->format('Ymd\THis');
        
        // Gera um ID único para o evento não bugar caso seja atualizado depois
        $uid = uniqid('gestiq_') . '@localhost';

        // Monta a string do arquivo .ics
        $icsContent = "BEGIN:VCALENDAR\r\n" .
                      "VERSION:2.0\r\n" .
                      "PRODID:-//Gestiq//Agenda MVP//PT-BR\r\n" .
                      "CALSCALE:GREGORIAN\r\n" .
                      "BEGIN:VEVENT\r\n" .
                      "UID:{$uid}\r\n" .
                      "DTSTAMP:{$stamp}\r\n" .
                      "DTSTART:{$start}\r\n" .
                      "DTEND:{$end}\r\n" .
                      "SUMMARY:{$title}\r\n" .
                      "DESCRIPTION:{$description}\r\n" .
                      "END:VEVENT\r\n" .
                      "END:VCALENDAR";

        // Define os headers HTTP para forçar o download
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="compromisso_gestiq.ics"');
        
        // Imprime o conteúdo e encerra a execução para não sujar o arquivo com HTML
        echo $icsContent;
        exit;
    }
}