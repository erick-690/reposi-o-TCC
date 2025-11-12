<?php
// Tenta obter a DATABASE_URL do ambiente (usada no Render)
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // === CONEXÃO PARA POSTGRESQL (RENDER) ===
    $url_parts = parse_url($database_url);
    $host = $url_parts['host'];
    $port = $url_parts['port'];
    $user = $url_parts['user'];
    $pass = $url_parts['pass'];
    $dbname = ltrim($url_parts['path'], '/'); 
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
} else {
    // === CONEXÃO PARA MYSQL (LOCAL/DESENVOLVIMENTO) ===
    // Use estas credenciais se rodar localmente sem a DATABASE_URL
    $host = 'localhost'; 
    $db = 'produto_mercado'; 
    $user = 'root'; 
    $pass = ''; 
    
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
}

try {
    // ATENÇÃO: Renomeado de $conexao para $pdo
    $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (PDOException $e) {
    // Se falhar, exibe o erro e interrompe o script
    die("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
}
// Se a conexão for bem-sucedida, o $pdo agora está definido e pronto para uso.
?>