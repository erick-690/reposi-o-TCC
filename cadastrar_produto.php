<?php

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = $_POST["nome_produto"];
    $descricao = $_POST["descricao"] ?? ''; // Usando null coalescing operator para valores opcionais
    $codigo_de_barras = $_POST["codigo_de_barras"] ?? null;
    $data_fabricacao = $_POST["data_fabricacao"] ?? null;
    $data_validade = $_POST["data_validade"] ?? null;
    $icone = $_POST["icone"] ?? null;
    $data_cadastro = $_POST["data_cadastro"] ?? null;
    $lote = $_POST["lote"] ?? null;
    $outras_informacoes = $_POST["outras_informacoes"] ?? '';

    $imagem_url = null;
    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $pasta_destino = "uploads/"; // Crie esta pasta no mesmo diretório dos seus arquivos PHP
        $nome_arquivo = uniqid() . "_" . basename($_FILES["imagem"]["name"]);
        $caminho_completo = $pasta_destino . $nome_arquivo;

        // Move o arquivo para a pasta de destino
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_completo)) {
            $imagem_url = $caminho_completo;
        } else {
            echo "Erro ao fazer o upload da imagem.";
            exit();
        }
    }

    try {
        $stmt = $conexao->prepare("INSERT INTO produtos (nome_produto, descricao, codigo_de_barras, imagem_url, icone, data_fabricacao, data_validade, data_cadastro, lote, outras_informacoes) VALUES (:nome, :descricao, :codigo_barras, :imagem, :icone, :fabricacao, :validade, :cadastro, :lote, :outras_info)");

        $stmt->bindParam(':nome', $nome_produto);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':codigo_barras', $codigo_de_barras);
        $stmt->bindParam(':imagem', $imagem_url);
        $stmt->bindParam(':icone', $icone);
        $stmt->bindParam(':fabricacao', $data_fabricacao);
        $stmt->bindParam(':validade', $data_validade);
        $stmt->bindParam(':cadastro', $data_cadastro);
        $stmt->bindParam(':lote', $lote);
        $stmt->bindParam(':outras_info', $outras_informacoes);

        $stmt->execute();

        echo "<p style='color: green;'>Produto cadastrado com sucesso!</p>";
        echo '<p><a href="lista_de_produtos.html">Voltar ao Cadastro</a> | <a href="produtos_cadastrados.html">Ver Produtos Cadastrados</a></p>';

    } catch(PDOException $e) {
        echo "Erro ao cadastrar o produto: " . $e->getMessage();
    }
} else {
    echo "<p>Acesso inválido.</p>";
}

?>