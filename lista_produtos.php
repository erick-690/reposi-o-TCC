<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos - Fluxon ERP</title>
    <link rel="stylesheet" href="pagina_principal.css">
    <link rel="stylesheet" href="lista_produtos.css"> 
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="index.html" class="navbar-brand">
                <img src="imagens1/Caixa.png" alt="Ícone Fluxon" class="navbar-icon-img">
                Fluxon
            </a>
            <button class="navbar-toggle" id="navbarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar-menu" id="navbarMenu">
                <ul class="navbar-list">
                    <li class="navbar-item"><a href="index.html" class="navbar-link">Página Inicial</a></li>
                    <li class="navbar-item"><a href="dashboard.html" class="navbar-link">Dashboard</a></li>
                    <li class="navbar-item"><a href="#" class="navbar-link active">Lista de Produtos</a></li>
                    </ul>
            </nav>
        </div>
    </header>

    <main class="content-page">
        <section class="section-container visual-examples-section"> <h2>Produtos Cadastrados</h2>

            <div id="produtos-container" class="examples-grid"> <?php include 'listar_produtos.php'; ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <img src="imagens1/Caixa.png" alt="Ícone Fluxon" class="footer-icon-img">
                Fluxon
            </div>
            <div class="footer-links">
                <h3>Navegação</h3>
                <ul>
                    <li><a href="index.html#home">Início</a></li>
                    <li><a href="index.html#sobre">Sobre</a></li>
                    <li><a href="index.html#recursos">Recursos</a></li>
                    <li><a href="index.html#exemplos">Exemplos Visuais</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contato</h3>
                <p>Email: contato@fluxon.com</p>
                <p>Telefone: (XX) XXXXX-XXXX</p>
                <p>Endereço: Rua da Inovação, 123, Ilhéus - BA</p>
            </div>
            <div class="footer-social">
                <h3>Siga-nos</h3>
                <a href="#" aria-label="LinkedIn"><img src="imagens1/icon_linkedin.png" alt="LinkedIn"></a>
                <a href="#" aria-label="Facebook"><img src="imagens1/icon_facebook.png" alt="Facebook"></a>
                <a href="#" aria-label="Instagram"><img src="imagens1/icon_instagram.png" alt="Instagram"></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Fluxon - Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="js/script.js"></script> </body>
</html>