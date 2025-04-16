// Definir as variáveis globais
let estoqueAtual = 20; // Estoque inicial
const totalEstoque = 20; // Estoque máximo
const quantidadeElement = document.getElementById('quantidade'); // Elemento que exibe a quantidade de estoque
const reguaElement = document.getElementById('regua'); // A régua de estoque
const reguaColorida = document.createElement('div'); // A régua colorida (dinâmica)
const caixasElement = document.getElementById('caixas'); // O container das caixas
const alertaElement = document.getElementById('alerta'); // Alerta de estoque acabado

// Inicializa a régua colorida e a adiciona à régua
reguaColorida.classList.add('regua-colorida');
reguaElement.appendChild(reguaColorida);

// Função para atualizar a régua e a cor
function atualizarRegua() {
    const porcentagem = (estoqueAtual / totalEstoque) * 100;
    
    // Atualiza a altura da régua colorida
    reguaColorida.style.height = `${porcentagem}%`;

    // Ajusta a cor da régua de acordo com a quantidade de estoque
    if (porcentagem >= 66) {
        reguaColorida.style.background = 'green'; // Verde
        // Atualiza o status do estoque
        statusEstoque('Estoque Máximo', 'piscarVerde');
    } else if (porcentagem >= 33) {
        reguaColorida.style.background = 'yellow'; // Amarelo
        // Atualiza o status do estoque
        statusEstoque('Estoque Acabando', 'piscarAmarelo');
    } else if (porcentagem > 0) {
        reguaColorida.style.background = 'red'; // Vermelho
        // Atualiza o status do estoque
        statusEstoque('Últimos Estoques', 'piscarVermelho');
    } else {
        reguaColorida.style.background = 'red'; // Vermelho
        // Exibe a mensagem "Estoque Acabou" quando o estoque atingir 0
        statusEstoque('Estoque Acabou', 'piscarVermelho');
    }

    // Atualiza o texto do estoque
    quantidadeElement.textContent = estoqueAtual;

    // Exibe alerta quando o estoque acaba
    if (estoqueAtual === 0) {
        alertaElement.style.display = 'block';
    } else {
        alertaElement.style.display = 'none';
    }
}

// Função para mostrar o status do estoque
function statusEstoque(mensagem, classe) {
    const statusElement = document.getElementById('statusEstoque');
    statusElement.textContent = mensagem;
    statusElement.className = classe;  // Adiciona a classe para piscar
    statusElement.style.display = 'block'; // Garante que o status seja visível
}


function atualizarCaixas() {
    // Limpa as caixas atuais
    caixasElement.innerHTML = '';

    // Cria uma nova caixa para cada unidade de estoque
    for (let i = 0; i < estoqueAtual; i++) {
        const caixa = document.createElement('div');
        caixa.classList.add('caixa');
        caixasElement.appendChild(caixa);
    }

    // Atualiza a régua
    atualizarRegua();
}

// Função para diminuir o estoque automaticamente
function diminuirEstoque() {
    if (estoqueAtual > 0) {
        estoqueAtual--; // Diminui o estoque

        // Seleciona as caixas e adiciona a classe 'removida' nas últimas caixas
        const caixas = document.querySelectorAll('.caixa');
        caixas[caixas.length - 1].classList.add('removida'); // Adiciona a classe 'removida' à última caixa

        // Atualiza as caixas e a régua
        setTimeout(atualizarCaixas, 500); // Espera a animação terminar antes de atualizar as caixas
    } else {
        // Se o estoque chegar a 0, reinicia o estoque
        setTimeout(reiniciarEstoque, 1000); // Espera 1 segundo antes de reiniciar
    }
}

// Função para reiniciar o estoque
function reiniciarEstoque() {
    estoqueAtual = totalEstoque; // Reseta o estoque para o valor máximo

    // Atualiza as caixas e a régua
    atualizarCaixas();

    // Reinicia o temporizador de diminuição do estoque (somente uma vez)
    if (!intervaloDeDiminuição) {
        intervaloDeDiminuição = setInterval(diminuirEstoque, 1000); // Reinicia a contagem
    }
}


// Função para reiniciar o estoque
function reiniciarEstoque() {
    estoqueAtual = totalEstoque; // Reseta o estoque para o valor máximo

    // Atualiza as caixas e a régua
    atualizarCaixas();

    // Reinicia o temporizador de diminuição do estoque (somente uma vez)
    if (!intervaloDeDiminuição) {
        intervaloDeDiminuição = setInterval(diminuirEstoque, 1000); // Reinicia a contagem
    }
}

// Inicializa as caixas de estoque ao carregar a página
window.onload = atualizarCaixas;

// Variável para controlar o intervalo global de diminuição do estoque
let intervaloDeDiminuição = setInterval(diminuirEstoque, 1000); // Inicia o ciclo de diminuição do estoque

