<!-- Importação da biblioteca principal do FullCalendar via CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<!-- Importação do pacote de localização para português do Brasil -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.js'></script>

<style>
    /* Define variáveis CSS para customização fácil do tema do calendário */
    :root {
        --fc-border-color: #e2e8f0;
        /* Cor da borda das células */
        --fc-daygrid-event-dot-width: 8px;
        /* Tamanho do ponto para eventos na visão de mês */
        --fc-list-event-dot-width: 10px;
        /* Tamanho do ponto para eventos na visão de lista */
        --fc-event-bg-color: #3788d8;
        /* Cor de fundo padrão dos eventos */
        --fc-event-border-color: #3788d8;
        /* Cor da borda padrão dos eventos */
        --fc-event-text-color: #fff;
        /* Cor do texto padrão dos eventos */
        --fc-page-bg-color: #fff;
        /* Cor de fundo da página do calendário */
        --fc-neutral-bg-color: rgba(208, 208, 208, 0.3);
        /* Cor de fundo para dias não pertencentes ao mês atual */
    }

    /* Estilo principal para o contêiner do calendário */
    .fc {
        background-color: white;
        /* Fundo branco */
        padding: 1.5rem;
        /* Espaçamento interno */
        border-radius: 0.75rem;
        /* Bordas arredondadas */
        /* Sombra sutil para dar profundidade */
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    /* Estilo para o título do calendário (ex: "Junho 2024") */
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        /* Tamanho da fonte */
        font-weight: 600;
        /* Peso da fonte (semi-bold) */
    }

    /* Estilo para os botões primários (ex: "hoje", "mês", "semana") */
    .fc .fc-button-primary {
        background-color: #3b82f6;
        /* Cor de fundo azul */
        border-color: #3b82f6;
        /* Cor da borda azul */
    }

    /* Efeito ao passar o mouse sobre os botões primários */
    .fc .fc-button-primary:hover {
        background-color: #2563eb;
        /* Tom de azul mais escuro */
        border-color: #2563eb;
        /* Cor da borda correspondente */
    }

    /* Estilo para destacar o dia atual no calendário */
    .fc .fc-daygrid-day.fc-day-today {
        background-color: #eff6ff;
        /* Fundo azul bem claro */
    }
</style>

<!-- Elemento HTML onde o calendário será renderizado pelo JavaScript -->
<div id='calendar'></div>

<!-- Modal para Adicionar/Editar Evento (inicialmente oculto) -->
<div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center">
    <!-- Contêiner do modal com estilo de cartão -->
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all -translate-y-10">
        <!-- Cabeçalho do modal -->
        <div class="flex items-center justify-between mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800">Agendar Evento</h2>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i> <!-- Ícone de fechar (requer Font Awesome) -->
            </button>
        </div>

        <!-- Div para exibir mensagens de erro -->
        <div id="modalError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>
        <!-- Div para exibir mensagens de sucesso -->
        <div id="modalSuccess" class="hidden mb-4 p-3 bg-green-100 text-green-700 rounded-lg"></div>

        <!-- Formulário para criar ou editar um evento -->
        <form id="eventoForm">
            <!-- Campo oculto para definir a ação a ser enviada ao backend -->
            <input type="hidden" name="action" value="agendar">
            <div class="space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título*</label>
                    <input type="text" id="titulo" name="titulo" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="id_cliente" class="block text-sm font-medium text-gray-700">Cliente</label>
                        <!-- O select de clientes será populado dinamicamente via JavaScript -->
                        <select id="id_cliente" name="id_cliente" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="">Selecione um cliente</option>
                        </select>
                    </div>
                    <div>
                        <label for="tipo_evento" class="block text-sm font-medium text-gray-700">Tipo*</label>
                        <select id="tipo_evento" name="tipo_evento" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="reuniao">Reunião</option>
                            <option value="visita">Visita</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="data_inicio" class="block text-sm font-medium text-gray-700">Início*</label>
                        <input type="datetime-local" id="data_inicio" name="data_inicio" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                    <div>
                        <label for="data_fim" class="block text-sm font-medium text-gray-700">Fim*</label>
                        <input type="datetime-local" id="data_fim" name="data_fim" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                </div>
                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="3" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="lembrete" name="lembrete" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="lembrete" class="ml-2 block text-sm text-gray-900">Ativar lembrete</label>
                </div>
            </div>
            <!-- Botões de ação do formulário -->
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvar Evento</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Executa o script quando o conteúdo do DOM (a página) estiver totalmente carregado.
    document.addEventListener('DOMContentLoaded', function() {
        // Obtém referências para os principais elementos HTML.
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventoModal');
        const eventoForm = document.getElementById('eventoForm');
        const modalError = document.getElementById('modalError');
        const modalSuccess = document.getElementById('modalSuccess');

        // Função auxiliar para formatar datas para o formato esperado pelo input 'datetime-local'.
        // Isso corrige problemas de fuso horário.
        function toLocalISOString(date) {
            const tzoffset = (new Date()).getTimezoneOffset() * 60000;
            const localISOTime = (new Date(date - tzoffset)).toISOString().slice(0, 16);
            return localISOTime;
        }

        // Cria uma nova instância do FullCalendar.
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Visão inicial do calendário (mês).
            locale: 'pt-br', // Define o idioma para português do Brasil.
            headerToolbar: { // Configura os botões e o título do cabeçalho.
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            // Formatação customizada para o cabeçalho dos dias.
            dayHeaderFormat: {
                weekday: 'short',
                day: 'numeric',
                month: 'numeric',
                omitCommas: true
            },
            // Formatação para a hora dos eventos.
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            events: '../../controllers/api_eventos.php', // URL para buscar os eventos do backend.
            editable: true, // Permite arrastar e redimensionar eventos.
            selectable: true, // Permite selecionar datas/horários.
            // Função chamada quando uma data é selecionada no calendário.
            select: function(info) {
                openModal(info); // Abre o modal para adicionar um novo evento.
            },
            // Função chamada quando um evento existente é clicado.
            eventClick: function(info) {
                // Formata as datas de início e fim para exibição.
                const start = info.event.start.toLocaleString('pt-BR');
                const end = info.event.end ? info.event.end.toLocaleString('pt-BR') : 'Sem data final';
                // Exibe um alerta com os detalhes do evento.
                alert(
                    `Evento: ${info.event.title}\n` +
                    `Cliente: ${info.event.extendedProps.cliente}\n` +
                    `Tipo: ${info.event.extendedProps.tipo}\n` +
                    `Início: ${start}\n` +
                    `Fim: ${end}`
                );
            }
        });
        // Renderiza o calendário na tela.
        calendar.render();

        // Função para abrir o modal de evento.
        function openModal(info) {
            eventoForm.reset(); // Limpa o formulário.
            modalError.classList.add('hidden'); // Oculta a mensagem de erro.
            modalSuccess.classList.add('hidden'); // Oculta a mensagem de sucesso.

            // Busca a lista de clientes no backend para preencher o select.
            fetch('../../controllers/api_clientes.php')
                .then(res => res.json()) // Converte a resposta para JSON.
                .then(data => {
                    const selectCliente = document.getElementById('id_cliente');
                    selectCliente.innerHTML = '<option value="">Selecione um cliente</option>'; // Limpa opções antigas.
                    if (data.success) {
                        // Se a busca for bem-sucedida, cria uma <option> para cada cliente.
                        data.clientes.forEach(cliente => {
                            const opt = document.createElement('option');
                            opt.value = cliente.id_cliente;
                            opt.textContent = cliente.nome;
                            selectCliente.appendChild(opt);
                        });
                    } else {
                        // Se falhar, exibe uma mensagem de erro no select.
                        const opt = document.createElement('option');
                        opt.value = "";
                        opt.textContent = "Erro ao carregar clientes";
                        selectCliente.appendChild(opt);
                    }
                });

            // Pré-preenche as datas no formulário com base na seleção do usuário.
            const startDate = info.allDay ? new Date(info.startStr + 'T09:00:00') : new Date(info.start);
            const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // Define o fim como 1h após o início.
            document.getElementById('data_inicio').value = toLocalISOString(startDate);
            document.getElementById('data_fim').value = toLocalISOString(endDate);
            // Exibe o modal.
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Função para fechar o modal.
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Adiciona ouvintes de evento para os botões de fechar e cancelar o modal.
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelModal').addEventListener('click', closeModal);

        // Adiciona um ouvinte de evento para o envio do formulário.
        eventoForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio padrão do formulário (recarregar a página).
            modalError.classList.add('hidden'); // Oculta mensagens de erro anteriores.
            modalSuccess.classList.add('hidden');

            // Validação simples: a data final não pode ser anterior à data inicial.
            const dataFim = document.getElementById('data_fim').value;
            const dataInicio = document.getElementById('data_inicio').value;
            if (dataFim && dataFim < dataInicio) {
                modalError.textContent = 'A data final não pode ser anterior à data inicial.';
                modalError.classList.remove('hidden');
                return; // Interrompe o envio.
            }

            // Cria um objeto FormData com os dados do formulário.
            const formData = new FormData(eventoForm);

            // Envia os dados para o controller PHP via POST.
            fetch('../../controllers/AgendaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Espera uma resposta em formato JSON do backend.
                .then(data => {
                    if (data.success) {
                        // Se o backend retornar sucesso, atualiza os eventos no calendário.
                        calendar.refetchEvents();
                        // Fecha o modal.
                        closeModal();
                    } else {
                        // Se o backend retornar erro, exibe a mensagem de erro no modal.
                        modalError.textContent = data.message || 'Ocorreu um erro desconhecido.';
                        modalError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    // Trata erros de comunicação com o servidor.
                    modalError.textContent = 'Erro de comunicação com o servidor. Verifique o console (F12).';
                    modalError.classList.remove('hidden');
                    console.error('Fetch Error:', error);
                });
        });
    });
</script>