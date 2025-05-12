<?php

include 'conexao.php';

try {
    $stmt = $conexao->prepare("SELECT id, nome_produto, imagem_url FROM produtos ORDER BY nome_produto");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultados) > 0) {
        foreach ($resultados as $produto) {
            echo '<div class="product-item">';
            echo '<div class="product-image-container">';
            if (!empty($produto['imagem_url'])) {
                echo '<img src="' . htmlspecialchars($produto['imagem_url']) . '" alt="' . htmlspecialchars($produto['nome_produto']) . '" class="product-image">';
            } else {
                echo '<div style="background-color: #333; height: 100%; display: flex; justify-content: center; align-items: center; border-radius: 6px;"><span style="color: #777;">Sem Foto</span></div>';
            }
            echo '</div>';
            echo '<div class="product-name">' . htmlspecialchars($produto['nome_produto']) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>Nenhum produto cadastrado ainda.</p>';
    }

} catch(PDOException $e) {
    echo "Erro ao buscar produtos: " . $e->getMessage();
}

?>