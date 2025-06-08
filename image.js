// image.js

document.addEventListener('DOMContentLoaded', function() {
    const images = [
        'imagens1/rp1.jpg', // Primeira imagem para o carrossel
        'imagens1/rp2.jpg',
        'imagens1/rp3.jpg',
        'imagens1/rp4.png',
        'imagens1/rp5.png',
        // Adicione mais caminhos para suas imagens de fundo aqui
    ];

    const carouselTrack = document.querySelector('.carousel-track');
    const carouselNavDots = document.querySelector('.carousel-nav-dots');

    let currentIndex = 0;
    let autoSlideInterval;
    const slideDuration = 8000; // Tempo em milissegundos para cada slide (8 segundos)

    // 1. Criar e adicionar as imagens ao carrossel
    function createCarouselImages() {
        images.forEach((imageUrl, index) => {
            const imgDiv = document.createElement('div');
            imgDiv.classList.add('carousel-image');
            imgDiv.style.backgroundImage = `url('${imageUrl}')`;
            carouselTrack.appendChild(imgDiv);

            // Criar e adicionar os pontos de navegação
            const dot = document.createElement('span');
            dot.classList.add('dot');
            dot.dataset.index = index;
            dot.addEventListener('click', () => {
                clearInterval(autoSlideInterval); // Limpa o intervalo ao clicar no ponto
                goToSlide(index);
                startAutoSlide(); // Reinicia o intervalo
            });
            carouselNavDots.appendChild(dot);
        });
    }

    // 2. Mudar para um slide específico
    function goToSlide(index) {
        // Ajusta o índice para garantir o loop
        if (index >= images.length) {
            index = 0;
        } else if (index < 0) {
            index = images.length - 1;
        }
        currentIndex = index;

        const offset = -currentIndex * 100; // Calcula o deslocamento em %
        carouselTrack.style.transform = `translateX(${offset}%)`;

        // Atualiza a classe 'active' para os pontos
        document.querySelectorAll('.dot').forEach((dot, idx) => {
            if (idx === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        // Reinicia a animação de zoom/pan para a imagem atual
        document.querySelectorAll('.carousel-image').forEach((img, idx) => {
            if (idx === currentIndex) {
                img.style.animation = 'none'; // Reseta a animação
                void img.offsetWidth; // Força um reflow
                img.style.animation = `zoomPan 20s infinite alternate ease-in-out`; // Aplica novamente
            } else {
                img.style.animation = 'none'; // Remove animação de imagens não ativas
            }
        });
    }

    // 3. Função para avançar para o próximo slide
    function showNextSlide() {
        goToSlide(currentIndex + 1);
    }

    // 4. Iniciar o Carrossel Automático
    function startAutoSlide() {
        autoSlideInterval = setInterval(showNextSlide, slideDuration);
    }

    // --- Lógica para o Menu Mobile ---
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');

    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', () => {
            navbarMenu.classList.toggle('active');
            navbarToggle.classList.toggle('active');
        });

        // Fechar menu ao clicar em um link (se o menu estiver ativo)
        document.querySelectorAll('.navbar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (navbarMenu.classList.contains('active')) {
                    navbarMenu.classList.remove('active');
                    navbarToggle.classList.remove('active');
                }
            });
        });
    }

    // --- Inicialização do Carrossel ---
    // Garante que o carrossel só inicialize se os elementos existirem
    if (carouselTrack && carouselNavDots && images.length > 0) {
        createCarouselImages(); // Cria as imagens e pontos
        goToSlide(0);           // Define a primeira imagem como ativa
        startAutoSlide();       // Inicia a troca automática
    } else {
        console.warn("Elementos do carrossel não encontrados ou array de imagens vazio. Carrossel não inicializado.");
    }
});