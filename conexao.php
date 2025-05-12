<?php

$host = "localhost"; // Geralmente é localhost
$dbname = "produto_mercado"; // Substitua pelo nome do seu banco de dados
$usuario = "root"; // Substitua pelo seu nome de usuário do banco de dados
$senha = ""; // Substitua pela sua senha do banco de dados

try {
    $conexao = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    // Defina o modo de erro do PDO para exceções
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão com o banco de dados realizada com sucesso!";
} catch(PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

// Você pode fechar a conexão no final do seu script, se necessário:
// $conexao = null;

?>