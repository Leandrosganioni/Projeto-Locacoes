document.addEventListener('DOMContentLoaded', function () {
    
    // --- Script Motivo Outro ---
    const motivoSelect = document.getElementById('motivo');
    const motivoOutroCampo = document.getElementById('campo_motivo_outro');

    // Verifica se os elementos existem na página
    if (motivoSelect && motivoOutroCampo) {
        function toggleMotivoOutro() {
            if (motivoSelect.value === 'outro') {
                motivoOutroCampo.style.display = 'block';
                document.getElementById('motivo_outro').required = true;
            } else {
                motivoOutroCampo.style.display = 'none';
                document.getElementById('motivo_outro').required = false;
            }
        }
        
        // Verifica no carregamento da página (caso tenha old input)
        toggleMotivoOutro();
        
        // Adiciona o listener
        motivoSelect.addEventListener('change', toggleMotivoOutro);
    }

    // --- Script Carregar Pedidos do Cliente ---
    const clienteSelect = document.getElementById('cliente_id');
    const pedidoSelect = document.getElementById('pedido_id');

    // Verifica se os elementos existem na página
    if (clienteSelect && pedidoSelect) {
        
        // **A MUDANÇA PRINCIPAL:**
        // Lemos a URL base do atributo 'data-api-url' que vamos adicionar no HTML
        const urlBase = clienteSelect.dataset.apiUrl;

        if (urlBase) {
            clienteSelect.addEventListener('change', function() {
                const clienteId = this.value;
                
                // Limpa e desabilita o select de pedidos
                pedidoSelect.innerHTML = '<option value="">Carregando...</option>';
                pedidoSelect.disabled = true;

                if (clienteId) {
                    // Se um cliente foi selecionado, busca os pedidos
                    fetch(`${urlBase}/${clienteId}/pedidos`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro ao buscar pedidos');
                            }
                            return response.json();
                        })
                        .then(pedidos => {
                            // Limpa o "Carregando..."
                            pedidoSelect.innerHTML = ''; 

                            if (pedidos.length > 0) {
                                pedidoSelect.disabled = false;
                                pedidoSelect.add(new Option('Selecione um pedido (se aplicável)...', ''));
                                
                                pedidos.forEach(pedido => {
                                    // Usamos 'data_entrega' (como corrigimos anteriormente)
                                    const data = new Date(pedido.data_entrega);
                                    const dataFormatada = data.toLocaleDateString('pt-BR', {timeZone: 'UTC'}); 
                                    
                                    const optionText = `Pedido #${pedido.id} (${dataFormatada})`;
                                    pedidoSelect.add(new Option(optionText, pedido.id));
                                });
                            } else {
                                pedidoSelect.innerHTML = '<option value="">Nenhum pedido encontrado</option>';
                                pedidoSelect.disabled = true;
                            }
                        })
                        .catch(error => {
                            console.error('Erro no fetch:', error);
                            pedidoSelect.innerHTML = '<option value="">Erro ao carregar pedidos</option>';
                            pedidoSelect.disabled = true;
                        });
                } else {
                    // Se "Selecione um cliente" for escolhido
                    pedidoSelect.innerHTML = '<option value="">Selecione um cliente primeiro...</option>';
                    pedidoSelect.disabled = true;
                }
            });
        } else {
            console.error('Atributo data-api-url não encontrado no select #cliente_id');
        }
    }
});