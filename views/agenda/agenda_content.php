<?php
// Garante que a sessão seja iniciada para podermos acessar os dados do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Importação das bibliotecas e estilos -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<!-- O script de locale do FullCalendar será carregado dinamicamente -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
    :root {
        --fc-border-color: #e2e8f0;
        --fc-event-bg-color: #3b82f6;
        --fc-event-border-color: #3b82f6;
        --fc-event-text-color: #fff;
    }

    .view-btn.active {
        background-color: white;
        color: #3b82f6;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }

    .fc-event-past {
        opacity: 0.6;
    }

    /* Esconde o texto que está aguardando tradução para evitar o "pisca-pisca" */
    .translating {
        visibility: hidden;
    }
</style>

<!-- Layout da página (com a classe 'translating') -->
<div class="w-full max-w-7xl mx-auto p-4 font-sans">
    <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
        <div class="flex items-center gap-2">
            <button id="prev-btn" class="p-2 rounded-lg hover:bg-gray-100"><i class="fas fa-chevron-left text-gray-600"></i></button>
            <button id="today-btn" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 translating" data-i18n="buttons.today">Hoje</button>
            <button id="next-btn" class="p-2 rounded-lg hover:bg-gray-100"><i class="fas fa-chevron-right text-gray-600"></i></button>
        </div>
        <h2 id="calendar-title" class="text-xl font-bold text-gray-800 order-first md:order-none"></h2>
        <div class="flex items-center gap-4">
            <div class="flex items-center bg-gray-100 p-1 rounded-lg">
                <button id="month-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md translating" data-i18n="buttons.month">Mês</button>
                <button id="week-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md translating" data-i18n="buttons.week">Semana</button>
                <button id="day-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md translating" data-i18n="buttons.day">Dia</button>
                <button id="list-view-btn" class="view-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md translating" data-i18n="buttons.list">Lista</button>
            </div>
        </div>
    </div>
    <div id='calendar-container' class="bg-white p-2 sm:p-6 rounded-lg shadow-md">
        <div id='calendar'></div>
    </div>
</div>

<!-- Modal de Adicionar/Editar Evento (com a classe 'translating') -->
<div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-5 border-b">
            <h2 id="modalTitle" class="text-xl font-semibold text-gray-800"></h2>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600 text-3xl font-light">&times;</button>
        </div>
        <form id="eventoForm" class="flex flex-col flex-grow overflow-hidden">
            <div class="p-6 overflow-y-auto flex-grow">
                <div id="modalError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>
                <input type="hidden" id="action" name="action" value="agendar">
                <input type="hidden" id="id_evento" name="id_evento" value="">
                <div class="space-y-4">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.title">Título*</label>
                        <input type="text" id="titulo" name="titulo" class="mt-1 w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tipo_evento" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.type">Tipo*</label>
                            <select id="tipo_evento" name="tipo_evento" class="mt-1 w-full px-4 py-2 border rounded-lg" required>
                                <option value="reuniao" class="translating" data-i18n="form.options.meeting">Reunião</option>
                                <option value="visita" class="translating" data-i18n="form.options.visit">Visita</option>
                                <option value="outro" class="translating" data-i18n="form.options.other">Outro</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_cliente" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.client">Cliente</label>
                            <select id="id_cliente" name="id_cliente" class="mt-1 w-full px-4 py-2 border rounded-lg"></select>
                        </div>
                    </div>
                    <div id="imovel-container" class="hidden">
                        <label for="id_imovel" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.property">Imóvel da Visita</label>
                        <select id="id_imovel" name="id_imovel" class="mt-1 w-full px-4 py-2 border rounded-lg"></select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="data_inicio" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.start">Início*</label>
                            <input type="datetime-local" id="data_inicio" name="data_inicio" class="mt-1 w-full px-4 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label for="data_fim" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.end">Fim*</label>
                            <input type="datetime-local" id="data_fim" name="data_fim" class="mt-1 w-full px-4 py-2 border rounded-lg" required>
                        </div>
                    </div>
                    <div>
                        <label for="descricao" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.description">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="3" class="mt-1 w-full px-4 py-2 border rounded-lg"></textarea>
                    </div>
                    <div id="feedback-container" class="hidden">
                        <label for="feedback" class="block text-sm font-medium text-gray-700 translating" data-i18n="form.labels.feedback">Feedback da Visita</label>
                        <textarea id="feedback" name="feedback" rows="3" class="mt-1 w-full px-4 py-2 border rounded-lg" data-i18n-placeholder="form.placeholders.feedback"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="lembrete" name="lembrete" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="lembrete" class="ml-2 block text-sm text-gray-900 translating" data-i18n="form.labels.reminder">Ativar lembrete</label>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center p-5 border-t bg-gray-50 rounded-b-xl">
                <button type="button" id="deleteButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden translating" data-i18n="buttons.delete">Excluir</button>
                <div class="space-x-3">
                    <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 translating" data-i18n="buttons.cancel">Cancelar</button>
                    <button type="submit" id="saveButton" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 translating" data-i18n="buttons.save">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação (com a classe 'translating') -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4 translating" data-i18n="confirmDelete.title"></h3>
        <p class="text-sm text-gray-600 mb-6 translating" data-i18n="confirmDelete.message"></p>
        <div class="flex justify-end space-x-4">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 translating" data-i18n="buttons.cancel">Cancelar</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 translating" data-i18n="buttons.confirm">Confirmar</button>
        </div>
    </div>
</div>


<!-- SCRIPT ATUALIZADO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. REFERÊNCIAS DE DOM E ESTADO GLOBAL ---
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
        const calendarTitleEl = document.getElementById('calendar-title');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const todayBtn = document.getElementById('today-btn');
        const monthViewBtn = document.getElementById('month-view-btn');
        const weekViewBtn = document.getElementById('week-view-btn');
        const dayViewBtn = document.getElementById('day-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const viewButtons = [monthViewBtn, weekViewBtn, dayViewBtn, listViewBtn];
        const tipoEventoSelect = document.getElementById('tipo_evento');
        const imovelContainer = document.getElementById('imovel-container');
        const imovelSelect = document.getElementById('id_imovel');
        const feedbackContainer = document.getElementById('feedback-container');
        const feedbackTextarea = document.getElementById('feedback');

        let calendar;
        let translations = {};
        let eventoIdParaExcluir = null;

        // --- 2. FUNÇÕES PRINCIPAIS DE I18N ---
        async function loadTranslations(module, lang) {
            try {
                const response = await fetch(`../../controllers/TraducaoController.php?modulo=${module}&lang=${lang}`);
                const result = await response.json();
                if (result.success) {
                    translations = result.data;
                }
            } catch (error) {
                console.error(`Could not load translation for module "${module}":`, error);
            }
        }

        function t(key) {
            return key.split('.').reduce((obj, i) => obj && obj[i], translations) || key;
        }

        function applyTranslationsToDOM() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.dataset.i18n;
                const translation = t(key);
                if (translation !== key) {
                    if (el.hasAttribute('placeholder')) {
                        el.placeholder = translation;
                    } else {
                        el.innerText = translation;
                    }
                }
                el.classList.remove('translating');
            });
        }

        function loadCalendarLocaleScript(lang) {
            return new Promise((resolve, reject) => {
                const existingScript = document.getElementById('fc-locale-script');
                if (existingScript) existingScript.remove();
                if (lang === 'en') return resolve();

                const script = document.createElement('script');
                script.id = 'fc-locale-script';
                script.src = `https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/${lang}.global.min.js`;
                script.onload = () => resolve();
                script.onerror = () => reject(new Error(`Failed to load locale: ${lang}`));
                document.head.appendChild(script);
            });
        }

        // --- 3. FUNÇÕES DA APLICAÇÃO (MODAIS, EVENTOS, ETC.) ---

        function toLocalISOString(date) {
            if (!date) return '';
            const d = new Date(date);
            d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
            return d.toISOString().slice(0, 16);
        }

        function populateImoveis(selectedImovelId = null) {
            fetch('../../controllers/api_imoveis.php')
                .then(res => res.json())
                .then(data => {
                    imovelSelect.innerHTML = `<option value="">${t('form.selects.chooseProperty')}</option>`;
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
                    selectCliente.innerHTML = `<option value="">${t('form.selects.chooseClient')}</option>`;
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

        function toggleCustomFields(tipo, dataFim) {
            if (tipo === 'visita') {
                imovelContainer.classList.remove('hidden');
            } else {
                imovelContainer.classList.add('hidden');
                imovelSelect.value = '';
            }
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
            modalTitle.textContent = t('calendar.addEventTitle');
            actionInput.value = 'agendar';
            idEventoInput.value = '';
            deleteButton.classList.add('hidden');
            const startDate = info.allDay ? new Date(info.startStr + 'T09:00:00') : new Date(info.start);
            const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
            document.getElementById('data_inicio').value = toLocalISOString(startDate);
            document.getElementById('data_fim').value = toLocalISOString(endDate);
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
            modalTitle.textContent = t('calendar.editEventTitle');
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
                        populateClientes(evento.id_cliente);
                        populateImoveis(evento.id_imovel);
                        toggleCustomFields(evento.tipo_evento, evento.data_fim);
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } else {
                        alert(t('calendar.fetchError') + res.message);
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
                        alert("Erro: " + data.message);
                        info.revert();
                    }
                });
        }

        // --- 4. INICIALIZAÇÃO DA APLICAÇÃO ---
        async function initializeAgenda(lang) {
            if (calendar) calendar.destroy();

            await Promise.all([
                loadTranslations('agenda', lang),
                loadCalendarLocaleScript(lang)
            ]);

            applyTranslationsToDOM();

            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: lang,
                initialView: 'dayGridMonth',
                headerToolbar: false,
                events: '../../controllers/api_eventos.php',
                editable: true,
                selectable: true,
                select: openModalForCreate,
                eventClick: (info) => openModalForEdit(info.event),
                eventDrop: (info) => updateEventDate(info),
                eventResize: (info) => updateEventDate(info),
                datesSet: (info) => {
                    calendarTitleEl.textContent = info.view.title;
                },
                eventDidMount: (info) => {
                    if (info.event.end && new Date(info.event.end) < new Date()) {
                        info.el.classList.add('fc-event-past');
                    }
                }
            });

            calendar.render();
            calendarTitleEl.textContent = calendar.view.title;
            updateActiveButton(monthViewBtn);
        }

        // --- 5. LISTENERS DE EVENTOS ---
        function updateActiveButton(activeButton) {
            viewButtons.forEach(button => button.classList.remove('active'));
            activeButton.classList.add('active');
        }
        prevBtn.addEventListener('click', () => calendar.prev());
        nextBtn.addEventListener('click', () => calendar.next());
        todayBtn.addEventListener('click', () => calendar.today());
        monthViewBtn.addEventListener('click', () => {
            calendar.changeView('dayGridMonth');
            updateActiveButton(monthViewBtn);
        });
        weekViewBtn.addEventListener('click', () => {
            calendar.changeView('timeGridWeek');
            updateActiveButton(weekViewBtn);
        });
        dayViewBtn.addEventListener('click', () => {
            calendar.changeView('timeGridDay');
            updateActiveButton(dayViewBtn);
        });
        listViewBtn.addEventListener('click', () => {
            calendar.changeView('listWeek');
            updateActiveButton(listViewBtn);
        });

        eventoForm.addEventListener('submit', (e) => {
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
                        modalError.textContent = data.message || t('calendar.defaultError');
                        modalError.classList.remove('hidden');
                    }
                });
        });

        deleteButton.addEventListener('click', () => {
            eventoIdParaExcluir = idEventoInput.value;
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });

        confirmDeleteBtn.addEventListener('click', () => {
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
                        alert(t('calendar.deleteError') + data.message);
                        closeConfirmModal();
                    }
                    eventoIdParaExcluir = null;
                });
        });

        tipoEventoSelect.addEventListener('change', function() {
            const dataFim = document.getElementById('data_fim').value;
            toggleCustomFields(this.value, dataFim);
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('#closeModal') || e.target.closest('#cancelModal')) closeModal();
            if (e.target.closest('#cancelDelete')) closeConfirmModal();
        });

        // --- 6. INÍCIO DA EXECUÇÃO ---
        function getInitialLang() {
            const langFromSession = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>";
            const langFromStorage = localStorage.getItem('calendarLang');

            if (langFromSession) {
                if (langFromStorage !== langFromSession) {
                    localStorage.setItem('calendarLang', langFromSession);
                }
                return langFromSession;
            }
            if (langFromStorage) return langFromStorage;

            const browserLang = navigator.language.toLowerCase();
            if (browserLang.startsWith('es')) return 'es';
            if (browserLang.startsWith('en')) return 'en';
            return 'pt-br';
        }

        const initialLang = getInitialLang();
        initializeAgenda(initialLang);
    });
</script>