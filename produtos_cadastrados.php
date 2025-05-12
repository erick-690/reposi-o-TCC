<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos Cadastrados</title>
    <link rel="stylesheet" href="lista_de_produtos.css">

</head>

<body>

    <div class="content">
        <nav class="navbar">
            
        </nav>

     
      <section class="lista-produtos">
    <h1>Produtos Cadastrados</h1>
    <div class="product-list-container">
        <?php include 'listar_produtos.php'; ?>
    </div>

</section>  
    <aside class="sidebar">
        <h1 class="sidebar-title">FLUXON</h1>
        <div class="sidebar-section">
            <h2>Navegação</h2>
            <ul>
                <li><a href="inicio.html">Inicio</a></li>
                <li><a href="produtos_cadastrados.php" class="active">Produtos Cadastrados</a></li>
                <li><a href="lista_de_produtos.html">Cadastrar Produto</a></li>
                <li><a href="index.html">Menu Principal</a></li>
            </ul>
        </div>
    </aside>

    <script src="lista_de_produtos.js"></script>

</body>
</html>