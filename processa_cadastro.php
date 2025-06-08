<?php
require_once 'conexao.php'; // Inclui o arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = $_POST['nome'] ?? '';
    $codigo_barras = $_POST['codigo_barras'] ?? NULL;
    $categoria = $_POST['categoria'] ?? '';
    $subcategoria = $_POST['subcategoria'] ?? NULL;
    $marca = $_POST['marca'] ?? NULL;
    $unidade_medida = $_POST['unidade_medida'] ?? '';
    $quantidade = $_POST['quantidade'] ?? 0;
    $preco_custo = $_POST['preco_custo'] ?? NULL;
    $preco_venda = $_POST['preco_venda'] ?? 0;
    $estoque_minimo = $_POST['estoque_minimo'] ?? 0;
    $descricao = $_POST['descricao'] ?? NULL;
    $fornecedor = $_POST['fornecedor'] ?? NULL;
    $ativo = isset($_POST['ativo']) ? 1 : 0; // Checkbox retorna '1' se marcado, senão não retorna nada

    // NOVOS CAMPOS: imagem_nome_arquivo e icone_classe
    $imagem_nome_arquivo = NULL; // Inicializa como nulo, será preenchido se houver upload
    $icone_classe = $_POST['icone_classe'] ?? NULL; // Pega a classe do ícone do formulário

    // --- Lógica para UPLOAD da IMAGEM ---
    // Verifica se um arquivo foi enviado e não houve erros
    if (isset($_FILES['imagem_produto']) && $_FILES['imagem_produto']['error'] === UPLOAD_ERR_OK) {
        $arquivo_tmp = $_FILES['imagem_produto']['tmp_name']; // Caminho temporário do arquivo
        $nome_original = basename($_FILES['imagem_produto']['name']); // Nome original do arquivo

        // Define o diretório onde as imagens serão salvas (a pasta 'uploads' que você criou)
        $diretorio_destino = 'uploads/';

        // Gera um nome único para o arquivo para evitar colisões (ex: prod_60e4c6c2b3e4f.png)
        $extensao = pathinfo($nome_original, PATHINFO_EXTENSION); // Pega a extensão do arquivo
        $novo_nome_arquivo = uniqid('prod_') . '.' . $extensao; // Cria um nome único
        $caminho_completo_destino = $diretorio_destino . $novo_nome_arquivo; // Caminho final para salvar

        // Tenta mover o arquivo temporário para o destino final
        if (move_uploaded_file($arquivo_tmp, $caminho_completo_destino)) {
            $imagem_nome_arquivo = $novo_nome_arquivo; // Se o upload for bem-sucedido, salva o nome do arquivo para o banco
        } else {
            // Se o upload falhar, exibe um alerta e redireciona
            echo "<script>alert('Erro ao fazer upload da imagem. Verifique as permissões da pasta " . $diretorio_destino . "'); window.location.href='cadastrar_produtos.html';</script>";
            exit();
        }
    }
    // --- FIM Lógica para UPLOAD da IMAGEM ---


    // Validação básica (campos obrigatórios)
    if (empty($nome) || empty($categoria) || empty($unidade_medida) || empty($preco_venda)) {
        echo "<script>alert('Erro: Campos obrigatórios (Nome, Categoria, Unidade de Medida, Preço de Venda) não preenchidos!'); window.location.href='cadastrar_produtos.html';</script>";
        exit();
    }

    try {
        // Prepara a query SQL para inserção com TODOS os campos, incluindo os novos de imagem e ícone
        $stmt = $conexao->prepare("INSERT INTO produtos (nome, codigo_barras, categoria, subcategoria, marca, unidade_medida, quantidade, preco_custo, preco_venda, estoque_minimo, descricao, fornecedor, imagem_nome_arquivo, icone_classe, ativo) VALUES (:nome, :codigo_barras, :categoria, :subcategoria, :marca, :unidade_medida, :quantidade, :preco_custo, :preco_venda, :estoque_minimo, :descricao, :fornecedor, :imagem_nome_arquivo, :icone_classe, :ativo)");

        // Bind dos parâmetros (associa as variáveis PHP aos placeholders na query SQL)
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':codigo_barras', $codigo_barras);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':subcategoria', $subcategoria);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':unidade_medida', $unidade_medida);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':preco_custo', $preco_custo);
        $stmt->bindParam(':preco_venda', $preco_venda);
        $stmt->bindParam(':estoque_minimo', $estoque_minimo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':fornecedor', $fornecedor);
        $stmt->bindParam(':imagem_nome_arquivo', $imagem_nome_arquivo); // Bind do nome do arquivo da imagem
        $stmt->bindParam(':icone_classe', $icone_classe); // Bind da classe do ícone
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_BOOL); // PDO::PARAM_BOOL é ideal para booleanos

        // Executa a query
        $stmt->execute();

        echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='dashboard.html';</script>";

    } catch (PDOException $e) {
        // Em caso de erro na inserção (ex: código de barras duplicado)
        if ($e->getCode() == '23000') { // Código de erro SQL para violação de UNIQUE constraint
             echo "<script>alert('Erro: Código de barras já existe ou outro dado único está duplicado. Por favor, insira dados únicos.'); window.location.href='cadastrar_produtos.html';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar produto: " . $e->getMessage() . "'); window.location.href='cadastrar_produtos.html';</script>";
        }
    }
} else {
    // Redireciona se a página for acessada diretamente sem um POST
    header("Location: cadastrar_produtos.html");
    exit();
}
?>