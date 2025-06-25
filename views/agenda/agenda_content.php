<!-- Importação das bibliotecas do FullCalendar e CSS permanecem as mesmas -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.js'></script>

<style>
    /* SEUS ESTILOS CSS PERMANECEM AQUI, SEM MUDANÇAS */
    :root {
        --fc-border-color: #e2e8f0;
        --fc-daygrid-event-dot-width: 8px;
        --fc-list-event-dot-width: 10px;
        --fc-event-bg-color: #3788d8;
        --fc-event-border-color: #3788d8;
        --fc-event-text-color: #fff;
        --fc-page-bg-color: #fff;
        --fc-neutral-bg-color: rgba(208, 208, 208, 0.3);
    }

    .fc {
        background-color: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .fc .fc-button-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    .fc .fc-button-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background-color: #eff6ff;
    }
</style>

<!-- O contêiner do calendário permanece o mesmo -->
<div id='calendar'></div>

<!-- Modal para Adicionar/Editar Evento (sua estrutura otimizada) -->
<div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between p-5 border-b">
            <h2 id="modalTitle" class="text-xl font-semibold text-gray-800">Agendar Evento</h2>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600 text-3xl font-light">&times;</button>
        </div>

        <!-- Formulário com corpo rolável -->
        <form id="eventoForm" class="flex flex-col flex-grow overflow-hidden">
            <div class="p-6 overflow-y-auto flex-grow">
                <div id="modalError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>
                <input type="hidden" id="action" name="action" value="agendar">
                <input type="hidden" id="id_evento" name="id_evento" value="">
                <div class="space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700">Título*</label>
                        <input type="text" id="titulo" name="titulo" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_cliente" class="block text-sm font-medium text-gray-700">Cliente</label>
                            <select id="id_cliente" name="id_cliente" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></select>
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
            </div>
            <!-- Rodapé -->
            <div class="flex justify-between items-center p-5 border-t bg-gray-50 rounded-b-xl">
                <button type="button" id="deleteButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden">Excluir</button>
                <div class="space-x-3">
                    <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                    <button type="submit" id="saveButton" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvar Evento</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirmar Exclusão</h3>
        <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir este evento? Esta ação não pode ser desfeita.</p>
        <div class="flex justify-end space-x-4">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirmar</button>
        </div>
    </div>
</div>


<!-- O SCRIPT COM TODA A NOVA LÓGICA DE INTERAÇÃO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Move os modais para serem filhos diretos do <body>.
        document.body.appendChild(document.getElementById('eventoModal'));
        document.body.appendChild(document.getElementById('confirmModal'));

        // ---- 1. REFERÊNCIAS AOS ELEMENTOS DO DOM ----
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventoModal');
        const eventoForm = document.getElementById('eventoForm');
        const modalError = document.getElementById('modalError');
        const modalTitle = document.getElementById('modalTitle');
        const actionInput = document.getElementById('action');
        const idEventoInput = document.getElementById('id_evento');
        const deleteButton = document.getElementById('deleteButton');
        const selectCliente = document.getElementById('id_cliente');
        const confirmModal = document.getElementById('confirmModal');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const cancelDeleteBtn = document.getElementById('cancelDelete');

        let eventoIdParaExcluir = null;

        // ---- 2. FUNÇÕES AUXILIARES ----
        function toLocalISOString(date) {
            if (!date) return '';
            const d = new Date(date);
            d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
            return d.toISOString().slice(0, 16);
        }

        // ---- 3. INICIALIZAÇÃO DO FULLCALENDAR ----
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                // (AJUSTADO) Cria um grupo para Mês/Semana/Dia e deixa Lista separado
                right: 'dayGridMonth,timeGridWeek,timeGridDay listWeek'
            },
            buttonText: { // Garante o texto correto para os botões
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
                list: 'Lista'
            },
            events: '../../controllers/api_eventos.php',
            editable: true,
            selectable: true,
            select: (info) => openModalForCreate(info),
            eventClick: (info) => openModalForEdit(info.event),
            eventDrop: (info) => updateEventDate(info),
            eventResize: (info) => updateEventDate(info)
        });
        calendar.render();

        // ---- 4. FUNÇÕES DE MANIPULAÇÃO DOS MODAIS ----
        function populateClientes(selectedClientId = null) {
            fetch('../../controllers/api_clientes.php')
                .then(res => res.json())
                .then(data => {
                    selectCliente.innerHTML = '<option value="">Selecione um cliente</option>';
                    if (data.success) {
                        data.clientes.forEach(cliente => {
                            const opt = document.createElement('option');
                            opt.value = cliente.id_cliente;
                            opt.textContent = cliente.nome;
                            if (cliente.id_cliente == selectedClientId) opt.selected = true;
                            selectCliente.appendChild(opt);
                        });
                    }
                });
        }

        function openModalForCreate(info) {
            eventoForm.reset();
            modalError.classList.add('hidden');
            modalTitle.textContent = 'Agendar Novo Evento';
            actionInput.value = 'agendar';
            idEventoInput.value = '';
            deleteButton.classList.add('hidden');

            const startDate = info.allDay ? new Date(info.startStr + 'T09:00:00') : new Date(info.start);
            const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
            document.getElementById('data_inicio').value = toLocalISOString(startDate);
            document.getElementById('data_fim').value = toLocalISOString(endDate);

            populateClientes();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openModalForEdit(event) {
            eventoForm.reset();
            modalError.classList.add('hidden');
            modalTitle.textContent = 'Editar Evento';
            actionInput.value = 'atualizar';
            idEventoInput.value = event.id;
            deleteButton.classList.remove('hidden');

            fetch(`../../controllers/AgendaController.php?action=buscar_evento&id=${event.id}`)
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        const evento = res.data;
                        document.getElementById('titulo').value = evento.titulo;
                        document.getElementById('tipo_evento').value = evento.tipo_evento;
                        document.getElementById('data_inicio').value = toLocalISOString(evento.data_inicio);
                        document.getElementById('data_fim').value = toLocalISOString(evento.data_fim);
                        document.getElementById('descricao').value = evento.descricao;
                        document.getElementById('lembrete').checked = evento.lembrete == 1;

                        populateClientes(evento.id_cliente);
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } else {
                        alert('Erro ao buscar dados do evento: ' + res.message);
                    }
                });
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function closeConfirmModal() {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
        }

        // ---- 5. FUNÇÕES DE SUBMISSÃO E ATUALIZAÇÃO (FETCH API) ----
        eventoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(eventoForm);

            fetch('../../controllers/AgendaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        calendar.refetchEvents();
                    } else {
                        modalError.textContent = data.message || 'Ocorreu um erro desconhecido.';
                        modalError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    modalError.textContent = 'Erro de comunicação com o servidor.';
                    modalError.classList.remove('hidden');
                });
        });

        deleteButton.addEventListener('click', function() {
            eventoIdParaExcluir = idEventoInput.value;
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (!eventoIdParaExcluir) return;

            const formData = new FormData();
            formData.append('action', 'excluir');
            formData.append('id_evento', eventoIdParaExcluir);

            fetch('../../controllers/AgendaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeConfirmModal();
                        closeModal();
                        calendar.refetchEvents();
                    } else {
                        alert('Erro ao excluir: ' + data.message);
                        closeConfirmModal();
                    }
                    eventoIdParaExcluir = null;
                });
        });

        function updateEventDate(info) {
            const {
                event
            } = info;
            const formData = new FormData();
            formData.append('action', 'atualizar_data');
            formData.append('id', event.id);
            formData.append('start', event.start.toISOString().slice(0, 19).replace('T', ' '));
            const endDate = event.end ? event.end.toISOString().slice(0, 19).replace('T', ' ') : formData.get('start');
            formData.append('end', endDate);

            fetch('../../controllers/AgendaController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("Erro ao salvar a alteração: " + data.message);
                        info.revert();
                    }
                });
        }

        // ---- 6. LISTENERS DOS BOTÕES ----
        // Usando event delegation para os botões dos modais
        document.addEventListener('click', function(e) {
            if (e.target.closest('#closeModal') || e.target.closest('#cancelModal')) {
                closeModal();
            }
            if (e.target.closest('#cancelDelete')) {
                closeConfirmModal();
            }
        });
    });
</script>