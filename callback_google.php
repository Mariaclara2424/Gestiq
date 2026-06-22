<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'AgendaService.php';

// Se tiver um ficheiro central de conexão à base de dados (ex: config.php ou conexao.php),
// pode incluí-lo aqui. Caso contrário, usamos o PDO direto abaixo.

if (isset($_GET['code'])) {
    
    $agenda = new AgendaService();
    
    // Troca o código temporário pelo Token completo do Google
    $token = $agenda->autenticarComCodigo($_GET['code']);
    
    // Verifica se a resposta não contém erros
    if (!isset($token['error'])) {
        
        // 1. Definição do usuário (Nesta fase de testes, fixamos o ID 1. 
        // No futuro, este valor virá de $_SESSION['usuario_id'])
        $usuario_id = 1; 
        
        // 2. Extração das chaves do array do Google
        $accessTokenString = $token['access_token'];
        // O refresh_token só é enviado na primeira vez que o usuário autoriza o app
        $refreshTokenString = $token['refresh_token'] ?? null; 
        
        try {
            // 3. Conexão com o MySQL do XAMPP
            $pdo = new PDO("mysql:host=localhost;dbname=gestiq;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 4. Verifica se este usuário já tem algum token antigo gravado
            $checkSql = "SELECT id, refresh_token FROM google_tokens WHERE usuario_id = :usuario_id";
            $stmtCheck = $pdo->prepare($checkSql);
            $stmtCheck->execute([':usuario_id' => $usuario_id]);
            $tokenAntigo = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($tokenAntigo) {
                // Se já existe, atualiza o access_token. 
                // Mantém o refresh_token antigo se o Google não tiver enviado um novo nesta requisição.
                $finalRefreshToken = $refreshTokenString ?? $tokenAntigo['refresh_token'];
                
                $updateSql = "UPDATE google_tokens 
                              SET access_token = :access_token, refresh_token = :refresh_token 
                              WHERE usuario_id = :usuario_id";
                $stmtUpdate = $pdo->prepare($updateSql);
                $stmtUpdate->execute([
                    ':access_token'  => $accessTokenString,
                    ':refresh_token' => $finalRefreshToken,
                    ':usuario_id'    => $usuario_id
                ]);
            } else {
                // Se for a primeira vez, faz o INSERT normal
                $insertSql = "INSERT INTO google_tokens (usuario_id, access_token, refresh_token) 
                              VALUES (:usuario_id, :access_token, :refresh_token)";
                $stmtInsert = $pdo->prepare($insertSql);
                $stmtInsert->execute([
                    ':usuario_id'    => $usuario_id,
                    ':access_token'  => $accessTokenString,
                    ':refresh_token' => $refreshTokenString
                ]);
            }
            
            echo "<h1>Integração Concluída com Sucesso! 🚀</h1>";
            echo "<p>As credenciais da agenda do Google foram guardadas na base de dados com segurança.</p>";
            echo "<a href='index.php'>Voltar para o Painel</a>";
            
        } catch (PDOException $e) {
            echo "<h1>Erro ao gravar na base de dados:</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<h1>Erro na autenticação do Google</h1>";
        echo "<pre>";
        print_r($token);
        echo "</pre>";
    }
    
} else {
    echo "<h1>Acesso Inválido</h1>";
}
?>