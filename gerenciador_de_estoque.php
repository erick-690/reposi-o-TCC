<?php
// PHP de processamento no topo do arquivo
require_once 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$produto = null; // Inicializa a variável produto
$message = ''; // Para exibir mensagens de sucesso/erro

// Lógica para buscar o produto a ser editado
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produto = $_GET['id'];

    try {
        $stmt = $conexao->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            // Se o produto não for encontrado, redireciona para a lista
            header("Location: lista_produtos.php?status=produto_nao_encontrado");
            exit();
        }

    } catch (PDOException $e) {
        $message = "<p class='message error'>Erro ao carregar produto: " . $e->getMessage() . "</p>";
        // Não sair aqui para que o HTML seja renderizado com a mensagem de erro
    }
} else {
    // Se nenhum ID for fornecido, redireciona ou exibe uma mensagem
    header("Location: lista_produtos.php?status=id_nao_fornecido");
    exit();
}

// Lógica para processar o formulário de atualização (quando o formulário é enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto_hidden'])) {
    $id_para_atualizar = $_POST['id_produto_hidden'];
    $nome = $_POST['nome'];
    $codigo_barras = $_POST['codigo_barras'];
    $categoria = $_POST['categoria'];
    $subcategoria = $_POST['subcategoria'];
    $marca = $_POST['marca'];
    $unidade_medida = $_POST['unidade_medida'];
    $quantidade = $_POST['quantidade'];
    $preco_custo = $_POST['preco_custo'];
    $preco_venda = $_POST['preco_venda'];
    $estoque_minimo = $_POST['estoque_minimo'];
    $descricao = $_POST['descricao'];
    $fornecedor = $_POST['fornecedor'];
    $ativo = isset($_POST['ativo']) ? 1 : 0; // 1 se marcado, 0 se não

    // Lidar com a imagem (se uma nova for enviada)
    $imagem_nome_arquivo = $produto['imagem_nome_arquivo']; // Mantém a imagem atual por padrão
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $timestamp = time();
        $temp = explode(".", $_FILES["imagem"]["name"]);
        $newfilename = $timestamp . '_' . round(microtime(true)) . '.' . end($temp);
        $target_file = $target_dir . $newfilename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validações de imagem (simples)
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $imagem_nome_arquivo = $newfilename;
                // Opcional: Deletar a imagem antiga se uma nova for carregada
                if (!empty($produto['imagem_nome_arquivo']) && file_exists($target_dir . $produto['imagem_nome_arquivo'])) {
                    unlink($target_dir . $produto['imagem_nome_arquivo']);
                }
            } else {
                $message = "<p class='message error'>Erro ao fazer upload da nova imagem.</p>";
            }
        } else {
            $message = "<p class='message error'>O arquivo enviado não é uma imagem válida.</p>";
        }
    } elseif (isset($_POST['remover_imagem']) && $_POST['remover_imagem'] == 'sim') {
        // Lógica para remover a imagem existente
        if (!empty($produto['imagem_nome_arquivo']) && file_exists("uploads/" . $produto['imagem_nome_arquivo'])) {
            unlink("uploads/" . $produto['imagem_nome_arquivo']);
        }
        $imagem_nome_arquivo = null; // Limpa o nome do arquivo no BD
    }

    // Lidar com o ícone (se um novo for selecionado ou removido)
    $icone_classe = $_POST['icone_classe'] ?? null; // Pega o ícone selecionado
    if (isset($_POST['remover_icone']) && $_POST['remover_icone'] == 'sim') {
        $icone_classe = null; // Limpa o ícone no BD
    }

    try {
        $stmt_update = $conexao->prepare("UPDATE produtos SET 
            nome = :nome, 
            codigo_barras = :codigo_barras, 
            categoria = :categoria, 
            subcategoria = :subcategoria, 
            marca = :marca, 
            unidade_medida = :unidade_medida, 
            quantidade = :quantidade, 
            preco_custo = :preco_custo, 
            preco_venda = :preco_venda, 
            estoque_minimo = :estoque_minimo, 
            descricao = :descricao, 
            fornecedor = :fornecedor, 
            imagem_nome_arquivo = :imagem_nome_arquivo,
            icone_classe = :icone_classe,
            ativo = :ativo,
            data_atualizacao = NOW() 
            WHERE id = :id");

        $stmt_update->bindParam(':nome', $nome);
        $stmt_update->bindParam(':codigo_barras', $codigo_barras);
        $stmt_update->bindParam(':categoria', $categoria);
        $stmt_update->bindParam(':subcategoria', $subcategoria);
        $stmt_update->bindParam(':marca', $marca);
        $stmt_update->bindParam(':unidade_medida', $unidade_medida);
        $stmt_update->bindParam(':quantidade', $quantidade);
        $stmt_update->bindParam(':preco_custo', $preco_custo);
        $stmt_update->bindParam(':preco_venda', $preco_venda);
        $stmt_update->bindParam(':estoque_minimo', $estoque_minimo);
        $stmt_update->bindParam(':descricao', $descricao);
        $stmt_update->bindParam(':fornecedor', $fornecedor);
        $stmt_update->bindParam(':imagem_nome_arquivo', $imagem_nome_arquivo);
        $stmt_update->bindParam(':icone_classe', $icone_classe);
        $stmt_update->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        $stmt_update->bindParam(':id', $id_para_atualizar, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            // Atualiza a variável $produto para que o formulário reflita as novas informações
            // Isso é importante para que a página recarregue com os dados corretos após a edição.
            $produto['nome'] = $nome;
            $produto['codigo_barras'] = $codigo_barras;
            $produto['categoria'] = $categoria;
            $produto['subcategoria'] = $subcategoria;
            $produto['marca'] = $marca;
            $produto['unidade_medida'] = $unidade_medida;
            $produto['quantidade'] = $quantidade;
            $produto['preco_custo'] = $preco_custo;
            $produto['preco_venda'] = $preco_venda;
            $produto['estoque_minimo'] = $estoque_minimo;
            $produto['descricao'] = $descricao;
            $produto['fornecedor'] = $fornecedor;
            $produto['imagem_nome_arquivo'] = $imagem_nome_arquivo;
            $produto['icone_classe'] = $icone_classe;
            $produto['ativo'] = $ativo;

            $message = "<p class='message success'>Produto atualizado com sucesso!</p>";
        } else {
            $message = "<p class='message error'>Erro ao atualizar produto.</p>";
        }

    } catch (PDOException $e) {
        $message = "<p class='message error'>Erro de banco de dados ao atualizar: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Fluxon</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="form_style.css">
    <link rel="stylesheet" href="gerenciador_de_estoque.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <h1>Editar Produto</h1>
        <nav>
            <ul>
                <li><a href="lista_produtos.php">Voltar para Produtos</a></li>
                <li><a href="cadastrar_produtos.html">Cadastrar Novo</a></li>
            </ul>
        </nav>
    </header>

    <main class="form-container">
        <?php echo $message; // Exibe mensagens de sucesso/erro ?>

        <?php if ($produto): ?>
            <form action="gerenciador_de_estoque.php?id=<?php echo htmlspecialchars($produto['id']); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_produto_hidden" value="<?php echo htmlspecialchars($produto['id']); ?>">

                <div class="form-section">
                    <h2>Dados do Produto</h2>
                    <div class="form-group">
                        <label for="nome">Nome do Produto:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="codigo_barras">Código de Barras:</label>
                        <input type="text" id="codigo_barras" name="codigo_barras" value="<?php echo htmlspecialchars($produto['codigo_barras']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($produto['categoria']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="subcategoria">Subcategoria:</label>
                        <input type="text" id="subcategoria" name="subcategoria" value="<?php echo htmlspecialchars($produto['subcategoria']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($produto['marca']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="unidade_medida">Unidade de Medida:</label>
                        <input type="text" id="unidade_medida" name="unidade_medida" value="<?php echo htmlspecialchars($produto['unidade_medida']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="quantidade">Quantidade em Estoque:</label>
                        <input type="number" id="quantidade" name="quantidade" value="<?php echo htmlspecialchars($produto['quantidade']); ?>" required min="0" step="any">
                    </div>
                    <div class="form-group">
                        <label for="preco_custo">Preço de Custo:</label>
                        <input type="number" id="preco_custo" name="preco_custo" value="<?php echo htmlspecialchars($produto['preco_custo']); ?>" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="preco_venda">Preço de Venda:</label>
                        <input type="number" id="preco_venda" name="preco_venda" value="<?php echo htmlspecialchars($produto['preco_venda']); ?>" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="estoque_minimo">Estoque Mínimo:</label>
                        <input type="number" id="estoque_minimo" name="estoque_minimo" value="<?php echo htmlspecialchars($produto['estoque_minimo']); ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fornecedor">Fornecedor:</label>
                        <input type="text" id="fornecedor" name="fornecedor" value="<?php echo htmlspecialchars($produto['fornecedor']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ativo">Produto Ativo:</label>
                        <input type="checkbox" id="ativo" name="ativo" value="1" <?php echo ($produto['ativo'] ? 'checked' : ''); ?>>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Mídia (Imagem ou Ícone)</h2>
                    <p>Você pode carregar uma nova imagem, remover a imagem existente, ou selecionar um ícone.</p>

                    <div class="form-group">
                        <label for="imagem">Carregar Nova Imagem:</label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                        <?php if (!empty($produto['imagem_nome_arquivo'])): ?>
                            <div class="current-image-preview">
                                <img src="uploads/<?php echo htmlspecialchars($produto['imagem_nome_arquivo']); ?>" alt="Imagem atual">
                                <div class="remove-option">
                                    <input type="checkbox" id="remover_imagem" name="remover_imagem" value="sim">
                                    <label for="remover_imagem">Remover Imagem Existente</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Ou Selecione um Ícone (apenas se não houver imagem):</label>
                        <div class="icon-selection-grid" id="iconSelectionGrid">
                            <?php
                            $popular_icons = [
                                'fa-box-open', 'fa-seedling', 'fa-utensils', 'fa-book', 'fa-shirt',
                                'fa-tools', 'fa-car', 'fa-paw', 'fa-lightbulb', 'fa-leaf',
                                'fa-pizza-slice', 'fa-headphones', 'fa-gamepad', 'fa-flask', 'fa-heartbeat',
                                'fa-mug-hot', 'fa-toilet-paper', 'fa-battery-full', 'fa-bell', 'fa-camera',
                                'fa-coffee', 'fa-cookie-bite', 'fa-desktop', 'fa-dumbbell', 'fa-earth-americas'
                            ];
                            foreach ($popular_icons as $icon_class) {
                                $selected_class = ($produto['icone_classe'] == $icon_class) ? 'selected' : '';
                                echo '<div class="icon-option ' . $selected_class . '" data-icon="' . $icon_class . '">';
                                echo '<i class="fas ' . $icon_class . '"></i>';
                                echo '<span>' . str_replace(['fa-', '-alt'], ['',''], $icon_class) . '</span>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <input type="hidden" id="icone_classe_hidden" name="icone_classe" value="<?php echo htmlspecialchars($produto['icone_classe'] ?? ''); ?>">
                        <div class="remove-option" style="margin-top: 10px;">
                            <input type="checkbox" id="remover_icone" name="remover_icone" value="sim">
                            <label for="remover_icone">Remover Ícone Existente</label>
                        </div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit">Confirmar Edição</button>
                    <a href="lista_produtos.php" class="btn-back">Voltar para Produtos</a>
                </div>
            </form>
        <?php else: ?>
            <p class="message error">Produto não encontrado ou ID inválido.</p>
            <div class="button-group">
                <a href="lista_produtos.php" class="btn-back">Voltar para Produtos</a>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Lógica JavaScript para seleção de ícones e manipulação de mídia
        document.addEventListener('DOMContentLoaded', function() {
            const iconOptions = document.querySelectorAll('.icon-option');
            const hiddenIconInput = document.getElementById('icone_classe_hidden');
            const removeImageIcon = document.getElementById('remover_imagem');
            const removeIconCheckbox = document.getElementById('remover_icone');
            const imageInput = document.getElementById('imagem');

            iconOptions.forEach(option => {
                option.addEventListener('click', function() {
                    iconOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    hiddenIconInput.value = this.dataset.icon;

                    if (removeImageIcon) removeImageIcon.checked = false;
                    removeIconCheckbox.checked = false;
                    imageInput.value = '';
                });
            });

            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        if (removeImageIcon) removeImageIcon.checked = false;
                        iconOptions.forEach(opt => opt.classList.remove('selected'));
                        hiddenIconInput.value = '';
                        removeIconCheckbox.checked = false;
                    }
                });
            }

            if (removeImageIcon) {
                removeImageIcon.addEventListener('change', function() {
                    if (this.checked) {
                        iconOptions.forEach(opt => opt.classList.remove('selected'));
                        hiddenIconInput.value = '';
                        removeIconCheckbox.checked = false;
                        imageInput.value = '';
                    }
                });
            }

            if (removeIconCheckbox) {
                removeIconCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        iconOptions.forEach(opt => opt.classList.remove('selected'));
                        hiddenIconInput.value = '';
                        if (removeImageIcon) removeImageIcon.checked = false;
                        imageInput.value = '';
                    }
                });
            }
        });
    </script>
</body>
</html>