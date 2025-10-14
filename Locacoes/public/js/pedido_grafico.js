/**
 * Script para montar o gráfico de evolução do pedido utilizando Chart.js.
 * Lê a URL do endpoint a partir do atributo data-url do canvas e busca os dados via fetch.
 * Em seguida, monta um gráfico de linha com o valor acumulado e marca eventos de
 * adição e finalização dos itens.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('graficoPedido');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const url = canvas.dataset.url;
    if (!url) return;

    try {
        const response = await fetch(url);
        const { series, events } = await response.json();

        // Prepara arrays de labels (datas) e valores acumulados
        const labels = series.map(item => item.data);
        const acumulados = series.map(item => item.acumulado);

        // Agrupa eventos por data
        const eventLabelsAdd = {};
        const eventLabelsFin = {};
        events.forEach(ev => {
            const { data, tipo, equipamento } = ev;
            if (tipo === 'Adição') {
                if (!eventLabelsAdd[data]) eventLabelsAdd[data] = [];
                eventLabelsAdd[data].push(equipamento);
            } else if (tipo === 'Finalização') {
                if (!eventLabelsFin[data]) eventLabelsFin[data] = [];
                eventLabelsFin[data].push(equipamento);
            }
        });

        // Para cada data, determina se há evento e obtém o valor acumulado naquela data
        const dataAdd = labels.map(date => {
            return eventLabelsAdd[date] ? acumulados[labels.indexOf(date)] : null;
        });
        const dataFin = labels.map(date => {
            return eventLabelsFin[date] ? acumulados[labels.indexOf(date)] : null;
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Valor acumulado (R$)',
                        data: acumulados,
                        tension: 0.3,
                        fill: true,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        pointRadius: 3,
                    },
                    {
                        label: 'Adições',
                        data: dataAdd,
                        showLine: false,
                        pointStyle: 'triangle',
                        pointRadius: 8,
                        pointBackgroundColor: '#198754',
                    },
                    {
                        label: 'Finalizações',
                        data: dataFin,
                        showLine: false,
                        pointStyle: 'rectRot',
                        pointRadius: 8,
                        pointBackgroundColor: '#dc3545',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Data',
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valor acumulado (R$)',
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const idx = context.dataIndex;
                                const datasetIndex = context.datasetIndex;
                                if (datasetIndex === 0) {
                                    return `R$ ${context.raw.toFixed(2)}`;
                                }
                                const date = labels[idx];
                                if (datasetIndex === 1 && eventLabelsAdd[date]) {
                                    return eventLabelsAdd[date].join(', ');
                                }
                                if (datasetIndex === 2 && eventLabelsFin[date]) {
                                    return eventLabelsFin[date].join(', ');
                                }
                                return '';
                            },
                        },
                    },
                },
            },
        });

    } catch (error) {
        console.error('Erro ao carregar dados do gráfico:', error);
    }
});