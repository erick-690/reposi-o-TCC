<?php
include 'conexao.php'; // Inclui o arquivo de conexão PDO adaptado para o Render

try {
    // 1. Prepara a consulta
    $sql = "SELECT * FROM produtos ORDER BY id DESC";
    $stmt = $pdo->query($sql);

    // 2. Verifica se encontrou produtos
    if ($stmt->rowCount() > 0) {
        // 3. Itera sobre os resultados
        while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            // --- INÍCIO DA CORREÇÃO ---
            
            // Tratamento de data_cadastro
            $data_cadastro_formatada = "N/A";
            if (!empty($produto['data_cadastro'])) {
                $data_cadastro_formatada = (new DateTime($produto['data_cadastro']))->format('d/m/Y H:i:s');
            }

            // LINHA 51: Tratamento de data_atualizacao (Causa do Deprecated)
            $data_atualizacao_formatada = "N/A";
            if (!empty($produto['data_atualizacao'])) {
                // new DateTime() só é chamado se o valor NÃO for NULL/vazio
                $data_atualizacao_formatada = (new DateTime($produto['data_atualizacao']))->format('d/m/Y H:i:s');
            }
            
            // --- FIM DA CORREÇÃO ---
            
            // Início do Card/Exibição HTML
            echo '<div class="example-card">';
            
            // Ícone do Produto (se houver a coluna icone_classe)
            $icone = !empty($produto['icone_classe']) ? $produto['icone_classe'] : 'fas fa-box';
            echo '<i class="' . htmlspecialchars($icone) . ' card-icon"></i>';
            
            echo '<h3>' . htmlspecialchars($produto['nome']) . '</h3>';
            echo '<p><strong>ID:</strong> ' . htmlspecialchars($produto['id']) . '</p>';
            echo '<p><strong>Código de Barras:</strong> ' . htmlspecialchars($produto['codigo_barras']) . '</p>';
            echo '<p><strong>Marca:</strong> ' . htmlspecialchars($produto['marca']) . '</p>';
            echo '<p><strong>Categoria:</strong> ' . htmlspecialchars($produto['categoria']) . '</p>';
            echo '<p><strong>Quantidade:</strong> ' . htmlspecialchars($produto['quantidade']) . ' ' . htmlspecialchars($produto['unidade_medida']) . '</p>';
            echo '<p><strong>Preço de Venda:</strong> R$ ' . number_format($produto['preco_venda'], 2, ',', '.') . '</p>';
            echo '<p><strong>Estoque Mínimo:</strong> ' . htmlspecialchars($produto['estoque_minimo']) . '</p>';
            
            // Exibindo as datas formatadas corrigidas
            echo '<p><strong>Cadastrado em:</strong> ' . $data_cadastro_formatada . '</p>';
            echo '<p><strong>Última Atualização:</strong> ' . $data_atualizacao_formatada . '</p>'; // A linha que era a 51 agora usa a variável corrigida
            
            // Status Ativo
            $status = $produto['ativo'] ? 'Ativo' : 'Inativo';
            $status_class = $produto['ativo'] ? 'status-ativo' : 'status-inativo';
            echo '<p><strong>Status:</strong> <span class="' . $status_class . '">' . $status . '</span></p>';
            
            // Botões de Ação
            echo '<div class="card-actions">';
            echo '<a href="editar_produto.php?id=' . $produto['id'] . '" class="btn btn-editar">Editar</a>';
            echo '<a href="excluir_produto.php?id=' . $produto['id'] . '" class="btn btn-excluir" onclick="return confirm(\'Tem certeza que deseja excluir este produto?\')">Excluir</a>';
            echo '</div>';
            
            echo '</div>'; // Fim do example-card
        }
    } else {
        echo '<p class="no-products">Nenhum produto cadastrado ainda.</p>';
    }

} catch (PDOException $e) {
    // Exibe erro de conexão, se houver
    echo "Erro ao listar produtos: " . $e->getMessage();
}
?>