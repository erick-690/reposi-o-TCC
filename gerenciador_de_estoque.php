<?php
require_once 'conexao.php';

$produto = null;
$message = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produto = $_GET['id'];

    try {
        $stmt = $conexao->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            header("Location: lista_produtos.php?status=produto_nao_encontrado");
            exit();
        }

    } catch (PDOException $e) {
        $message = "<p class='message error'>Erro ao carregar produto: " . $e->getMessage() . "</p>";
    }
} else {
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
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    // Imagem
    $imagem_nome_arquivo = $produto['imagem_nome_arquivo'];
    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $timestamp = time();
        $temp = explode(".", $_FILES["imagem_produto"]["name"]);
        $newfilename = $timestamp . '_' . round(microtime(true)) . '.' . end($temp);
        $target_file = $target_dir . $newfilename;

        $check = getimagesize($_FILES["imagem_produto"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["imagem_produto"]["tmp_name"], $target_file)) {
                $imagem_nome_arquivo = $newfilename;
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
        if (!empty($produto['imagem_nome_arquivo']) && file_exists("uploads/" . $produto['imagem_nome_arquivo'])) {
            unlink("uploads/" . $produto['imagem_nome_arquivo']);
        }
        $imagem_nome_arquivo = null;
    }

    // Ícone
    $icone_classe = $_POST['icone_classe'] ?? null;
    if (isset($_POST['remover_icone']) && $_POST['remover_icone'] == 'sim') {
        $icone_classe = null;
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
    <title>Editar Produto - Fluxon ERP</title>
    <link rel="stylesheet" href="pagina_principal.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="form_cadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="index.html" class="navbar-brand">
                <img src="imagens1/Caixa.png" alt="Fluxon Logo" class="navbar-icon-img">
                Fluxon
            </a>
            <button class="navbar-toggle" id="navbarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar-menu" id="navbarMenu">
                <ul class="navbar-list">
                    <li class="navbar-item"><a href="index.html" class="navbar-link">Página Inicial</a></li>
                    <li class="navbar-item"><a href="dashboard.html" class="navbar-link">Dashboard</a></li>
                    <li class="navbar-item"><a href="cadastrar_produtos.html" class="navbar-link">Cadastrar Produto</a></li>
                    <li class="navbar-item"><a href="lista_produtos.php" class="navbar-link">Voltar para Produtos</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="content-page">
        <section class="section-container form-section">
            <h2>Editar Produto</h2>
            <?php echo $message; ?>
            <?php if ($produto): ?>
            <form action="gerenciador_de_estoque.php?id=<?php echo htmlspecialchars($produto['id']); ?>" method="POST" class="cadastro-form" enctype="multipart/form-data">
                <input type="hidden" name="id_produto_hidden" value="<?php echo htmlspecialchars($produto['id']); ?>">

                <div class="form-category-title">Nome e Informações do Produto</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome do Produto:</label>
                        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($produto['nome']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="codigo_barras">Código de Barras:</label>
                        <input type="text" id="codigo_barras" name="codigo_barras" value="<?php echo htmlspecialchars($produto['codigo_barras']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <input type="text" id="categoria" name="categoria" required value="<?php echo htmlspecialchars($produto['categoria']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="subcategoria">Subcategoria:</label>
                        <input type="text" id="subcategoria" name="subcategoria" value="<?php echo htmlspecialchars($produto['subcategoria']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($produto['marca']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="unidade_medida">Unidade de Medida:</label>
                        <input type="text" id="unidade_medida" name="unidade_medida" required value="<?php echo htmlspecialchars($produto['unidade_medida']); ?>">
                    </div>
                </div>

                <div class="form-category-title">Detalhes Comerciais</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" step="0.001" id="quantidade" name="quantidade" required value="<?php echo htmlspecialchars($produto['quantidade']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="preco_custo">Preço de Custo:</label>
                        <input type="number" step="0.01" id="preco_custo" name="preco_custo" value="<?php echo htmlspecialchars($produto['preco_custo']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="preco_venda">Preço de Venda:</label>
                        <input type="number" step="0.01" id="preco_venda" name="preco_venda" required value="<?php echo htmlspecialchars($produto['preco_venda']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="estoque_minimo">Estoque Mínimo:</label>
                        <input type="number" id="estoque_minimo" name="estoque_minimo" value="<?php echo htmlspecialchars($produto['estoque_minimo']); ?>">
                    </div>
                </div>

                <div class="form-category-title">Descrição e Estoque</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fornecedor">Fornecedor:</label>
                        <input type="text" id="fornecedor" name="fornecedor" value="<?php echo htmlspecialchars($produto['fornecedor']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ativo">
                            <input type="checkbox" id="ativo" name="ativo" value="1" <?php echo ($produto['ativo'] ? 'checked' : ''); ?>>
                            Produto Ativo
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="imagem_produto">Imagem do Produto (Upload):</label>
                        <input type="file" id="imagem_produto" name="imagem_produto" accept="image/*">
                        <?php if (!empty($produto['imagem_nome_arquivo'])): ?>
                            <div style="margin-top: 8px;">
                                <img src="uploads/<?php echo htmlspecialchars($produto['imagem_nome_arquivo']); ?>" alt="Imagem atual" style="max-width:80px; border-radius:6px;">
                                <label style="margin-left:10px;">
                                    <input type="checkbox" name="remover_imagem" value="sim"> Remover imagem existente
                                </label>
                            </div>
                        <?php endif; ?>
                        <small>Selecione uma imagem do seu computador (JPG, PNG, GIF). Uma imagem terá prioridade na exibição.</small>
                    </div>
                    <div class="form-group">
                        <label for="icone_classe">Ícone (Classe Font Awesome, ex: fas fa-box-open):</label>
                        <input type="text" id="icone_classe" name="icone_classe" value="<?php echo htmlspecialchars($produto['icone_classe'] ?? ''); ?>" placeholder="Ex: fas fa-shopping-basket">
                        <?php if (!empty($produto['icone_classe'])): ?>
                            <div style="margin-top:8px;">
                                <i class="<?php echo htmlspecialchars($produto['icone_classe']); ?>" style="font-size:1.7em"></i>
                                <label style="margin-left:10px;">
                                    <input type="checkbox" name="remover_icone" value="sim"> Remover ícone existente
                                </label>
                            </div>
                        <?php endif; ?>
                        <small>Se não enviar imagem, este ícone será exibido.</small>
                    </div>
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="btn-primary">Salvar Alterações</button>
                </div>
            </form>
            <?php else: ?>
                <p class="message error">Produto não encontrado ou ID inválido.</p>
                <div class="button-wrapper">
                    <a href="lista_produtos.php" class="btn-primary">Voltar para Produtos</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer"></footer>
    <script src="js/script.js"></script>
</body>
</html>