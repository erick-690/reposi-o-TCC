<?php
require_once 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_produto']) && !empty($_POST['id_produto'])) {
        $id_produto = $_POST['id_produto'];

        try {
            // Prepara a query para deletar o produto com base no ID
            $stmt = $conexao->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Produto deletado com sucesso, redireciona de volta para a lista
                header("Location: lista_produtos.php?status=deletado_sucesso");
                exit();
            } else {
                // Erro ao executar a exclusão
                header("Location: lista_produtos.php?status=erro_deletar");
                exit();
            }
        } catch (PDOException $e) {
            // Erro de banco de dados
            error_log("Erro ao deletar produto: " . $e->getMessage()); // Registra o erro no log
            header("Location: lista_produtos.php?status=erro_bd");
            exit();
        }
    } else {
        // ID do produto não fornecido
        header("Location: lista_produtos.php?status=id_nao_fornecido");
        exit();
    }
} else {
    // Tentativa de acesso direto sem método POST
    header("Location: lista_produtos.php");
    exit();
}
?>