document.addEventListener('DOMContentLoaded', function() {
    const images = [
        'imagens1/rp1.jpg',
        'imagens1/rp2.jpg',
        'imagens1/rp3.jpg',
        'imagens1/rp4.png',
        'imagens1/rp5.png',
        // Adicione mais caminhos para suas imagens aqui
    ];
    const backgroundContainer = document.createElement('div');
    backgroundContainer.classList.add('background-container');
    document.body.prepend(backgroundContainer); // Adiciona o container de fundo no início do body

    let currentIndex = 0;

    function createBackgroundImage(imageUrl) {
        const img = document.createElement('div');
        img.classList.add('background-image');
        img.style.backgroundImage = `url('${imageUrl}')`;
        return img;
    }

    const backgroundImagesElements = images.map(imageUrl => {
        const imgElement = createBackgroundImage(imageUrl);
        backgroundContainer.appendChild(imgElement);
        return imgElement;
    });

    function changeBackground() {
        // Remove a classe 'active' da imagem atual
        backgroundImagesElements.forEach(img => img.classList.remove('active'));

        // Atualiza o índice para a próxima imagem
        currentIndex = (currentIndex + 1) % images.length;

        // Adiciona a classe 'active' à nova imagem
        backgroundImagesElements[currentIndex].classList.add('active');
    }

    // Define a primeira imagem como ativa inicialmente
    if (backgroundImagesElements.length > 0) {
        backgroundImagesElements[0].classList.add('active');
    }

    // Inicia o intervalo para trocar as imagens a cada 10 segundos (10000 milissegundos)
    setInterval(changeBackground, 10000);
});
