// Função para gráfico de Pizza/Rosca (para motivos)
function initPieChart(canvasId) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Lê os dados dos atributos data-*
    const labels = JSON.parse(ctx.dataset.labels || '[]');
    const data = JSON.parse(ctx.dataset.data || '[]');

    if (data.length === 0) {
        const context = ctx.getContext('2d');
        context.textAlign = 'center';
        context.fillStyle = '#6c757d'; // Cor cinza
        context.fillText("Sem dados para exibir no período.", ctx.width / 2, ctx.height / 2);
        return;
    }

    new Chart(ctx.getContext('2d'), {
        type: 'pie', // Pode ser 'doughnut' também
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantidade',
                data: data,
                backgroundColor: [ // Cores variadas
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                 borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

// Função para gráfico de Linha (para tempo)
function initLineChart(canvasId) {
     const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Lê os dados dos atributos data-*
    const labels = JSON.parse(ctx.dataset.labels || '[]');
    const data = JSON.parse(ctx.dataset.data || '[]');

     if (data.length === 0) {
        const context = ctx.getContext('2d');
        context.textAlign = 'center';
        context.fillStyle = '#6c757d';
        context.fillText("Sem dados para exibir no período.", ctx.width / 2, ctx.height / 2);
        return;
    }

    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: labels, // Datas
            datasets: [{
                label: 'Nº de Ocorrências',
                data: data, // Quantidade de ocorrências por data
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
             plugins: {
                legend: { display: false }
            }
        }
    });
}


$(document).ready(function() {

    const quebrasTable = $('#quebrasTable');

    if (quebrasTable.length) {
        // Lê a configuração de paginação do atributo data-*
        const paginationEnabled = quebrasTable.data('pagination-enabled') === true;

        // Inicializa o DataTable na tabela de quebras
        quebrasTable.DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
            // Configuração do DOM e Botões igual ao vendas.blade.php
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-end'B>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5',   className: 'btn btn-sm btn-secondary', text: '<i class="bi bi-clipboard"></i> Copiar' },
                { extend: 'csvHtml5',    className: 'btn btn-sm btn-success',   text: '<i class="bi bi-filetype-csv"></i> CSV' },
                { extend: 'excelHtml5',  className: 'btn btn-sm btn-success',   text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
                { extend: 'pdfHtml5',    className: 'btn btn-sm btn-danger',    text: '<i class="bi bi-file-earmark-pdf"></i> PDF' },
                { extend: 'print',       className: 'btn btn-sm btn-primary',   text: '<i class="bi bi-printer"></i> Imprimir' }
            ],
             // Ordena pela coluna de Data/Hora (índice 0) decrescente por padrão
            order: [[0, 'desc']],
            
            // Usa a variável lida do atributo data-*
            paging: paginationEnabled,
            info: paginationEnabled,
            lengthChange: paginationEnabled,
        });
    }

    // Inicializa os gráficos (se os elementos existirem)
    initPieChart('motivosChart');
    initLineChart('tempoChart');
});