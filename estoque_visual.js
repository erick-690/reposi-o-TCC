// Variáveis globais
let estoqueMax = 20;
let estoqueAtual = 20;

// Elementos da interface
const quantidadeElement = document.getElementById('quantidade');
const reguaElement = document.getElementById('regua');
const caixasElement = document.getElementById('caixas');
const statusElement = document.getElementById('statusEstoque');

// Gráfico animado (se existir na página)
let graficoEstoque = null;
let historicoEstoque = [18, 14, 12, 14, 17, 20, 19, estoqueAtual];
let historicoDatas = [];
(function gerarDatas() {
    const agora = new Date();
    for (let i = historicoEstoque.length - 1; i >= 0; i--) {
        const d = new Date(agora);
        d.setDate(d.getDate() - (historicoEstoque.length - 1 - i));
        historicoDatas.push(d.toLocaleDateString("pt-BR", { day: "2-digit", month: "2-digit" }));
    }
})();

// REGUA COLORIDA
const reguaColorida = document.createElement('div');
reguaColorida.classList.add('regua-colorida');
reguaElement.appendChild(reguaColorida);

// Atualiza visual do estoque
function atualizarEstoqueVisual() {
    // Atualiza caixas
    caixasElement.innerHTML = '';
    for (let i = 0; i < estoqueAtual; i++) {
        const caixa = document.createElement("div");
        caixa.className = "caixa";
        caixasElement.appendChild(caixa);
    }
    // Atualiza régua
    atualizarRegua();
    // Atualiza status
    atualizarStatus();
    // Atualiza gráfico (se houver)
    atualizarGrafico();
}

// Atualiza régua colorida e status
function atualizarRegua() {
    const porcentagem = (estoqueAtual / estoqueMax) * 100;
    reguaColorida.style.height = `${porcentagem}%`;
    if (porcentagem >= 66) {
        reguaColorida.style.background = 'green';
    } else if (porcentagem >= 33) {
        reguaColorida.style.background = 'yellow';
    } else {
        reguaColorida.style.background = 'red';
    }
    quantidadeElement.textContent = estoqueAtual;
}

// Atualiza status animado
function atualizarStatus() {
    statusElement.style.display = "block";
    statusElement.className = "";
    if (estoqueAtual >= estoqueMax * 0.7) {
        statusElement.textContent = "Estoque saudável";
        statusElement.classList.add("piscarVerde");
    } else if (estoqueAtual >= estoqueMax * 0.4) {
        statusElement.textContent = "Atenção: Estoque mediano";
        statusElement.classList.add("piscarAmarelo");
    } else if (estoqueAtual > 0) {
        statusElement.textContent = "Crítico: Estoque baixo";
        statusElement.classList.add("piscarVermelho");
    } else {
        statusElement.textContent = "Estoque Acabou";
        statusElement.classList.add("piscarVermelho");
    }
}

// Gráfico Chart.js
function atualizarGrafico() {
    if (!graficoEstoque) return;
    graficoEstoque.data.labels = [...historicoDatas];
    graficoEstoque.data.datasets[0].data = [...historicoEstoque];
    graficoEstoque.update();
}

// Função para diminuir o estoque automaticamente
function diminuirEstoque() {
    if (estoqueAtual > 0) {
        estoqueAtual--;
        historicoEstoque.push(estoqueAtual);
        historicoEstoque = historicoEstoque.slice(-8);
        let hoje = new Date();
        historicoDatas.push(hoje.toLocaleDateString("pt-BR", { day: "2-digit", month: "2-digit" }));
        historicoDatas = historicoDatas.slice(-8);

        // Animação de remoção da última caixa
        const caixas = document.querySelectorAll('.caixa');
        if (caixas.length > 0) {
            caixas[caixas.length - 1].classList.add('removida');
        }
        setTimeout(atualizarEstoqueVisual, 400);
    } else {
        setTimeout(reiniciarEstoque, 1200);
    }
}

// Função para reiniciar o estoque
function reiniciarEstoque() {
    estoqueAtual = estoqueMax;
    historicoEstoque.push(estoqueAtual);
    historicoEstoque = historicoEstoque.slice(-8);
    let hoje = new Date();
    historicoDatas.push(hoje.toLocaleDateString("pt-BR", { day: "2-digit", month: "2-digit" }));
    historicoDatas = historicoDatas.slice(-8);
    atualizarEstoqueVisual();
}

// Inicializa as caixas de estoque ao carregar a página
window.onload = function() {
    atualizarEstoqueVisual();

    // Inicializa gráfico se existir
    const chartCanvas = document.getElementById('graficoEstoque');
    if (chartCanvas && window.Chart) {
        graficoEstoque = new Chart(chartCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: historicoDatas,
                datasets: [{
                    label: 'Estoque',
                    data: historicoEstoque,
                    backgroundColor: 'rgba(29,185,84,0.15)',
                    borderColor: '#1db954',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1db954',
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    tension: 0.38,
                    fill: true,
                }]
            },
            options: {
                animation: { duration: 900, easing: 'easeOutQuart' },
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#222',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#1db954',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: "#444" } },
                    y: {
                        beginAtZero: true,
                        suggestedMax: estoqueMax + 2,
                        grid: { color: "#e0e0e0" },
                        ticks: { color: "#444", stepSize: 2 }
                    }
                }
            }
        });
    }
};

// Variável para controlar o intervalo global
let intervaloDeDiminuição = setInterval(diminuirEstoque, 2000); // Diminui a cada 2 segundos