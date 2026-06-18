<?php

class Database {
    // Tipagem forte: aceita um objeto PDO ou nulo
    private static ?PDO $instance = null;

    // Construtor privado impede que a classe seja instanciada com "new Database()" de fora
    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                // Utiliza as constantes que foram definidas no config.php
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                // Captura o erro graciosamente sem expor a Stack Trace inteira
                die("Erro crítico de conexão: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}