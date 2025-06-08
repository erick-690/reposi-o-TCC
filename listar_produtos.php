<?php
// ESTE ARQUIVO NAO DEVE TER NENHUMA TAG HTML ALEM DOS CARDS DOS PRODUTOS.
// Ele é incluído por lista_produtos.php, que já tem a estrutura HTML da página.

require_once 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

try {
    // Seleciona todas as colunas, incluindo as novas para imagem e ícone
    $stmt = $conexao->prepare("SELECT id, nome, codigo_barras, categoria, subcategoria, marca, unidade_medida, quantidade, preco_custo, preco_venda, estoque_minimo, descricao, fornecedor, imagem_nome_arquivo, icone_classe, data_cadastro, data_atualizacao, ativo FROM produtos ORDER BY data_cadastro DESC");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Busca todos os resultados como um array associativo

    if (count($produtos) > 0) {
        // Itera sobre cada produto e gera o HTML do card
        foreach ($produtos as $produto) {
            $data_cadastro_formatada = new DateTime($produto['data_cadastro']);
            $data_cadastro_formatada = $data_cadastro_formatada->format('d/m/Y H:i');

            // Lógica para exibir IMAGEM ou ÍCONE
            $card_media_html = '';
            if (!empty($produto['imagem_nome_arquivo'])) {
                // Se houver nome de arquivo de imagem, exibe a imagem
                $caminho_imagem = 'uploads/' . htmlspecialchars($produto['imagem_nome_arquivo']);
                $card_media_html = '<img src="' . $caminho_imagem . '" alt="' . htmlspecialchars($produto['nome']) . '">';
            } elseif (!empty($produto['icone_classe'])) {
                // Se não houver imagem, mas houver classe de ícone, exibe o ícone
                $card_media_html = '<div class="example-card-icon"><i class="' . htmlspecialchars($produto['icone_classe']) . '"></i></div>';
            } else {
                // Se não houver imagem nem ícone, exibe um ícone padrão
                $card_media_html = '<div class="example-card-icon"><i class="fas fa-box-open"></i></div>'; // Ícone padrão
            }

            echo '
            <div class="example-card">
                ' . $card_media_html . '
                <h3>' . htmlspecialchars($produto['nome']) . '</h3>
                
                <div class="card-details-scroll"> <p class="description-short">' . htmlspecialchars($produto['descricao'] ?? 'Sem descrição.') . '</p>
                    <p><strong>Código de Barras:</strong> ' . htmlspecialchars($produto['codigo_barras'] ?? 'N/A') . '</p>
                    <p><strong>Categoria:</strong> ' . htmlspecialchars($produto['categoria'] ?? 'N/A') . '</p>
                    <p><strong>Subcategoria:</strong> ' . htmlspecialchars($produto['subcategoria'] ?? 'N/A') . '</p>
                    <p><strong>Marca:</strong> ' . htmlspecialchars($produto['marca'] ?? 'N/A') . '</p>
                    <p><strong>Unidade/Qtde:</strong> ' . htmlspecialchars($produto['quantidade'] ?? 'N/A') . ' ' . htmlspecialchars($produto['unidade_medida'] ?? '') . '</p>
                    <p><strong>Preço Venda:</strong> R$ ' . number_format($produto['preco_venda'], 2, ',', '.') . '</p>
                    <p><strong>Preço Custo:</strong> R$ ' . number_format($produto['preco_custo'], 2, ',', '.') . '</p>
                    <p><strong>Estoque Mínimo:</strong> ' . htmlspecialchars($produto['estoque_minimo'] ?? 'N/A') . '</p>
                    <p><strong>Fornecedor:</strong> ' . htmlspecialchars($produto['fornecedor'] ?? 'N/A') . '</p>
                    <p><strong>Status:</strong> ' . ($produto['ativo'] ? 'Ativo' : 'Inativo') . '</p>
                    <p><strong>Cadastrado em:</strong> ' . $data_cadastro_formatada . '</p>
                    <p><strong>Última Atualização:</strong> ' . (new DateTime($produto['data_atualizacao']))->format('d/m/Y H:i') . '</p>
                </div>
                
                <div class="card-actions"> <form action="apagar_produto.php" method="POST" onsubmit="return confirm(\'Tem certeza que deseja APAGAR o produto \\\'' . htmlspecialchars($produto['nome']) . '\\\'? Esta ação é irreversível!\');">
                        <input type="hidden" name="id_produto" value="' . htmlspecialchars($produto['id']) . '">
                        <button type="submit" class="btn-deletar"><i class="fas fa-trash-alt"></i> Deletar</button>
                    </form>
                    <a href="gerenciador_de_estoque.php?id=' . htmlspecialchars($produto['id']) . '" class="btn-editar"><i class="fas fa-edit"></i> Editar</a>
                </div>
            </div>';
        }
    } else {
        // Mensagem se não houver produtos cadastrados
        echo '<p class="no-products-message">Nenhum produto cadastrado ainda. <a href="cadastrar_produtos.html">Cadastre um agora!</a></p>';
    }

} catch (PDOException $e) {
    // Em caso de erro na consulta, exibe a mensagem de erro
    echo '<p class="error-message">Erro ao carregar produtos: ' . $e->getMessage() . '</p>';
}
?>