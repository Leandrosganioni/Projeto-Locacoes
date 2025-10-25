/**
 * Inicializa o gráfico de barras empilhadas para o relatório de estoque.
 *
 * Este script espera que a biblioteca Chart.js (v4+) já esteja carregada.
 * Ele lê os dados diretamente dos atributos 'data-*' do elemento canvas.
 *
 * @param {string} elementId - O ID do elemento <canvas> onde o gráfico será renderizado.
 */
function initEstoqueChart(elementId) {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js não foi carregado. Este script precisa do Chart.js para funcionar.');
        return;
    }

    const ctxCanvas = document.getElementById(elementId);
    if (!ctxCanvas) {
        console.error(`Elemento canvas com ID "${elementId}" não foi encontrado.`);
        return;
    }


    let labels, dataLocada, dataDisponivel;
    try {
        labels = JSON.parse(ctxCanvas.dataset.labels);
        dataLocada = JSON.parse(ctxCanvas.dataset.locada);
        dataDisponivel = JSON.parse(ctxCanvas.dataset.disponivel);
    } catch (e) {
        console.error('Falha ao processar dados (JSON) dos atributos data-* do canvas.', e);
        return;
    }
    

    new Chart(ctxCanvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,         
            datasets: [
                {
                    label: 'Qtd. Locada',
                    data: dataLocada,    
                    backgroundColor: 'rgba(255, 99, 132, 0.7)', 
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Qtd. Disponível',
                    data: dataDisponivel,  
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribuição de Itens Locados vs. Disponíveis'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Equipamentos'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantidade'
                    }
                }
            }
        }
    });
}