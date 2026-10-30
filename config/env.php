<?php
/**
 * Carrega as variáveis do arquivo .env (na raiz do projeto) uma única vez.
 * Uso: env('DB_HOST', '127.0.0.1')
 */
if (!function_exists('env')) {
    function env(string $chave, $padrao = null)
    {
        static $valores = null;

        if ($valores === null) {
            $valores = [];
            $arquivo = dirname(__DIR__) . '/.env';
            if (is_readable($arquivo)) {
                foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
                    $linha = trim($linha);
                    if ($linha === '' || $linha[0] === '#' || strpos($linha, '=') === false) {
                        continue;
                    }
                    [$nome, $valor] = array_map('trim', explode('=', $linha, 2));
                    $valores[$nome] = trim($valor, "\"'");
                }
            }
        }

        $valor = getenv($chave);
        if ($valor !== false) {
            return $valor;
        }
        return $valores[$chave] ?? $padrao;
    }
}
