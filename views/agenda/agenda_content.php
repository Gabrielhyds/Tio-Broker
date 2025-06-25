<!-- Importação das bibliotecas e estilos permanecem os mesmos -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.js'></script>
<style>
    :root {
        --fc-border-color: #e2e8f0;
        --fc-event-bg-color: #3b82f6;
        --fc-event-border-color: #3b82f6;
        --fc-event-text-color: #fff;
    }
    .view-btn.active {
        background-color: white; color: #3b82f6;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }
    /* (NOVO) Estilo para eventos que já aconteceram */
    .fc-event-past {
        opacity: 0.6;
    }
</style>

<!-- Layout da página (sem alterações) -->
<div class="w-full max-w-7xl mx-auto p-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
        <div class="flex items-center gap-2">
            <button id="prev-btn" class="p-2 rounded-lg hover:bg-gray-100"><i class="fas fa-chevron-left text-gray-600"></i></button>
            <button id="today-btn" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Hoje</button>
            <button id="next-btn" class="p-2 rounded-lg hover:bg-gray-100"><i class="fas fa-chevron-right text-gray-600"></i></button>
        </div>
        <h2 id="calendar-title" class="text-xl font-bold text-gray-800 order-first md:order-none"></h2>
        <div class="flex items-center bg-gray-100 p-1 rounded-lg">
             <button id="month-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md">Mês</button>
             <button id="week-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md">Semana</button>
             <button id="day-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md">Dia</button>
             <button id="list-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md">Lista</button>
        </div>
    </div>
    <div id='calendar-container' class="bg-white p-2 sm:p-6 rounded-lg shadow-md">
        <div id='calendar'></div>
    </div>
</div>

<!-- Modal de Adicionar/Editar Evento (COM NOVOS CAMPOS) -->
<div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between p-5 border-b">
      <h2 id="modalTitle" class="text-xl font-semibold text-gray-800">Agendar Evento</h2>
      <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600 text-3xl font-light">&times;</button>
    </div>
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
                  <label for="tipo_evento" class="block text-sm font-medium text-gray-700">Tipo*</label>
                  <select id="tipo_evento" name="tipo_evento" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                      <option value="reuniao">Reunião</option>
                      <option value="visita">Visita</option>
                      <option value="outro">Outro</option>
                  </select>
              </div>
              <div>
                  <label for="id_cliente" class="block text-sm font-medium text-gray-700">Cliente</label>
                  <select id="id_cliente" name="id_cliente" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></select>
              </div>
            </div>
            <!-- (NOVO) Campo para selecionar o imóvel, escondido por padrão -->
            <div id="imovel-container" class="hidden">
                <label for="id_imovel" class="block text-sm font-medium text-gray-700">Imóvel da Visita</label>
                <select id="id_imovel" name="id_imovel" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></select>
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
            <!-- (NOVO) Campo para feedback da visita, escondido por padrão -->
            <div id="feedback-container" class="hidden">
                <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback da Visita</label>
                <textarea id="feedback" name="feedback" rows="3" placeholder="O que o cliente achou do imóvel?" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
            </div>
            <div class="flex items-center">
              <input type="checkbox" id="lembrete" name="lembrete" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
              <label for="lembrete" class="ml-2 block text-sm text-gray-900">Ativar lembrete</label>
            </div>
        </div>
      </div>
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

<!-- Modal de Confirmação (sem alterações) -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirmar Exclusão</h3>
        <p class="text-sm text-gray-600 mb-6">Tem certeza que deseja excluir este evento?</p>
        <div class="flex justify-end space-x-4">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Confirmar</button>
        </div>
    </div>
</div>


<!-- SCRIPT ATUALIZADO COM A LÓGICA DO DIFERENCIAL -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('eventoModal'));
    document.body.appendChild(document.getElementById('confirmModal'));

    // ---- 1. REFERÊNCIAS AO DOM (com novos elementos) ----
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
    const calendarTitleEl = document.getElementById('calendar-title');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const todayBtn = document.getElementById('today-btn');
    const monthViewBtn = document.getElementById('month-view-btn');
    const weekViewBtn = document.getElementById('week-view-btn');
    const dayViewBtn = document.getElementById('day-view-btn');
    const listViewBtn = document.getElementById('list-view-btn');
    const viewButtons = [monthViewBtn, weekViewBtn, dayViewBtn, listViewBtn];

    // (NOVO) Referências aos novos campos
    const tipoEventoSelect = document.getElementById('tipo_evento');
    const imovelContainer = document.getElementById('imovel-container');
    const imovelSelect = document.getElementById('id_imovel');
    const feedbackContainer = document.getElementById('feedback-container');
    const feedbackTextarea = document.getElementById('feedback');

    let eventoIdParaExcluir = null;

    // ---- 2. FUNÇÕES AUXILIARES ----
    function toLocalISOString(date) {
        if (!date) return '';
        const d = new Date(date);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().slice(0, 16);
    }
    
    // ---- 3. INICIALIZAÇÃO DO FULLCALENDAR (com novidades) ----
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: false,
        events: '../../controllers/api_eventos.php',
        editable: true,
        selectable: true,
        select: (info) => openModalForCreate(info),
        eventClick: (info) => openModalForEdit(info.event),
        eventDrop: (info) => updateEventDate(info),
        eventResize: (info) => updateEventDate(info),
        datesSet: function(info) {
            calendarTitleEl.textContent = info.view.title;
        },
        /**
         * (NOVO) Callback executado para cada evento renderizado.
         * Usamos para adicionar uma classe a eventos passados, dando um feedback visual.
         */
        eventDidMount: function(info) {
            if (info.event.end && new Date(info.event.end) < new Date()) {
                info.el.classList.add('fc-event-past');
            }
        }
    });
    calendar.render();
    calendarTitleEl.textContent = calendar.view.title;
    updateActiveButton(monthViewBtn);

    // ---- 4. FUNÇÕES DE MANIPULAÇÃO DOS MODAIS (com novidades) ----

    /**
     * (NOVO) Busca a lista de imóveis da nossa API simulada e popula o dropdown.
     */
    function populateImoveis(selectedImovelId = null) {
        fetch('../../controllers/api_imoveis.php')
            .then(res => res.json())
            .then(data => {
                imovelSelect.innerHTML = '<option value="">Selecione um imóvel</option>';
                if (data.success) {
                    data.imoveis.forEach(imovel => {
                        const opt = document.createElement('option');
                        opt.value = imovel.id_imovel;
                        opt.textContent = imovel.titulo_imovel;
                        if (imovel.id_imovel == selectedImovelId) opt.selected = true;
                        imovelSelect.appendChild(opt);
                    });
                }
            });
    }

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

    /**
     * (NOVO) Gerencia a visibilidade dos campos de imóvel e feedback com base no tipo e data do evento.
     */
    function toggleCustomFields(tipo, dataFim) {
        // Mostra/esconde o campo de imóvel
        if (tipo === 'visita') {
            imovelContainer.classList.remove('hidden');
        } else {
            imovelContainer.classList.add('hidden');
            imovelSelect.value = ''; // Limpa a seleção se não for visita
        }

        // Mostra/esconde o campo de feedback
        const agora = new Date();
        const fimEvento = new Date(dataFim);
        if (tipo === 'visita' && fimEvento < agora) {
            feedbackContainer.classList.remove('hidden');
        } else {
            feedbackContainer.classList.add('hidden');
        }
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
        
        // Esconde os campos customizados ao criar
        imovelContainer.classList.add('hidden');
        feedbackContainer.classList.add('hidden');

        populateClientes();
        populateImoveis();
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
                    tipoEventoSelect.value = evento.tipo_evento;
                    document.getElementById('data_inicio').value = toLocalISOString(evento.data_inicio);
                    document.getElementById('data_fim').value = toLocalISOString(evento.data_fim);
                    document.getElementById('descricao').value = evento.descricao;
                    feedbackTextarea.value = evento.feedback;
                    document.getElementById('lembrete').checked = evento.lembrete == 1;

                    // Popula e depois gerencia a visibilidade dos campos
                    populateClientes(evento.id_cliente);
                    populateImoveis(evento.id_imovel);
                    toggleCustomFields(evento.tipo_evento, evento.data_fim);

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    alert('Erro ao buscar dados do evento: ' + res.message);
                }
            });
    }
    
    // As demais funções de fechar modais e de CRUD permanecem iguais
    function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    function closeConfirmModal() { confirmModal.classList.add('hidden'); confirmModal.classList.remove('flex'); }
    
    // ---- 5. FUNÇÕES DE SUBMISSÃO E ATUALIZAÇÃO (FETCH API) ----
    eventoForm.addEventListener('submit', function(e) { /* ...código sem alteração... */
        e.preventDefault();
        const formData = new FormData(eventoForm);
        fetch('../../controllers/AgendaController.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                calendar.refetchEvents();
            } else {
                modalError.textContent = data.message || 'Ocorreu um erro desconhecido.';
                modalError.classList.remove('hidden');
            }
        });
    });
    deleteButton.addEventListener('click', function() { /* ...código sem alteração... */
        eventoIdParaExcluir = idEventoInput.value;
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
    });
    confirmDeleteBtn.addEventListener('click', function() { /* ...código sem alteração... */
        if (!eventoIdParaExcluir) return;
        const formData = new FormData();
        formData.append('action', 'excluir');
        formData.append('id_evento', eventoIdParaExcluir);
        fetch('../../controllers/AgendaController.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeConfirmModal(); closeModal(); calendar.refetchEvents();
            } else {
                alert('Erro ao excluir: ' + data.message); closeConfirmModal();
            }
            eventoIdParaExcluir = null;
        });
    });
    function updateEventDate(info) { /* ...código sem alteração... */
        const { event } = info;
        const formData = new FormData();
        formData.append('action', 'atualizar_data');
        formData.append('id', event.id);
        formData.append('start', event.start.toISOString().slice(0, 19).replace('T', ' '));
        const endDate = event.end ? event.end.toISOString().slice(0, 19).replace('T', ' ') : formData.get('start');
        formData.append('end', endDate);
        fetch('../../controllers/AgendaController.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (!data.success) { alert("Erro: " + data.message); info.revert(); }
        });
    }
    function updateActiveButton(activeButton) { /* ...código sem alteração... */
        viewButtons.forEach(button => button.classList.remove('active'));
        activeButton.classList.add('active');
    }

    // ---- 6. LISTENERS DOS BOTÕES ----
    prevBtn.addEventListener('click', () => calendar.prev());
    nextBtn.addEventListener('click', () => calendar.next());
    todayBtn.addEventListener('click', () => calendar.today());
    monthViewBtn.addEventListener('click', () => { calendar.changeView('dayGridMonth'); updateActiveButton(monthViewBtn); });
    weekViewBtn.addEventListener('click', () => { calendar.changeView('timeGridWeek'); updateActiveButton(weekViewBtn); });
    dayViewBtn.addEventListener('click', () => { calendar.changeView('timeGridDay'); updateActiveButton(dayViewBtn); });
    listViewBtn.addEventListener('click', () => { calendar.changeView('listWeek'); updateActiveButton(listViewBtn); });

    /**
     * (NOVO) Listener para o campo 'Tipo de Evento'.
     * Quando o tipo muda, chama a função para mostrar ou esconder os campos de imóvel e feedback.
     */
    tipoEventoSelect.addEventListener('change', function() {
        const dataFim = document.getElementById('data_fim').value;
        toggleCustomFields(this.value, dataFim);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('#closeModal') || e.target.closest('#cancelModal')) { closeModal(); }
        if (e.target.closest('#cancelDelete')) { closeConfirmModal(); }
    });
});
</script>
