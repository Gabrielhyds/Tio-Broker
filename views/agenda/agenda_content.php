<!-- Importação do FullCalendar via CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales/pt-br.global.js'></script>

<style>
    /* Estilos (sem alterações) */
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
        background-color: white; padding: 1.5rem; border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .fc .fc-toolbar-title { font-size: 1.5rem; font-weight: 600; }
    .fc .fc-button-primary { background-color: #3b82f6; border-color: #3b82f6; }
    .fc .fc-button-primary:hover { background-color: #2563eb; border-color: #2563eb; }
    .fc .fc-daygrid-day.fc-day-today { background-color: #eff6ff; }
</style>

<!-- Elemento onde o calendário será renderizado -->
<div id='calendar'></div>

<!-- Modal para Adicionar/Editar Evento -->
<div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all -translate-y-10">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800">Agendar Evento</h2>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div id="modalError" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>
        <div id="modalSuccess" class="hidden mb-4 p-3 bg-green-100 text-green-700 rounded-lg"></div>

        <form id="eventoForm">
            <input type="hidden" name="action" value="agendar">
            <div class="space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título*</label>
                    <input type="text" id="titulo" name="titulo" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="id_cliente" class="block text-sm font-medium text-gray-700">Cliente (ID)</label>
                        <input type="number" id="id_cliente" name="id_cliente" class="mt-1 w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
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
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvar Evento</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const modal = document.getElementById('eventoModal');
        const eventoForm = document.getElementById('eventoForm');
        const modalError = document.getElementById('modalError');
        const modalSuccess = document.getElementById('modalSuccess');

        function toLocalISOString(date) {
            const tzoffset = (new Date()).getTimezoneOffset() * 60000;
            const localISOTime = (new Date(date - tzoffset)).toISOString().slice(0, 16);
            return localISOTime;
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            dayHeaderFormat: { weekday: 'short', day: 'numeric', month: 'numeric', omitCommas: true },
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
            events: '../../controllers/api_eventos.php',
            editable: true,
            selectable: true,
            select: function(info) { openModal(info); },
            eventClick: function(info) {
                const start = info.event.start.toLocaleString('pt-BR');
                const end = info.event.end ? info.event.end.toLocaleString('pt-BR') : 'Sem data final';
                alert(
                    `Evento: ${info.event.title}\n` +
                    `Cliente: ${info.event.extendedProps.cliente}\n` +
                    `Tipo: ${info.event.extendedProps.tipo}\n` +
                    `Início: ${start}\n` +
                    `Fim: ${end}`
                );
            }
        });
        calendar.render();

        function openModal(info) {
            eventoForm.reset();
            modalError.classList.add('hidden');
            modalSuccess.classList.add('hidden');
            const startDate = info.allDay ? new Date(info.startStr + 'T09:00:00') : new Date(info.start);
            const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
            document.getElementById('data_inicio').value = toLocalISOString(startDate);
            document.getElementById('data_fim').value = toLocalISOString(endDate);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelModal').addEventListener('click', closeModal);

        eventoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            modalError.classList.add('hidden');
            modalSuccess.classList.add('hidden');

            const dataFim = document.getElementById('data_fim').value;
            const dataInicio = document.getElementById('data_inicio').value;
            if (dataFim && dataFim < dataInicio) {
                modalError.textContent = 'A data final não pode ser anterior à data inicial.';
                modalError.classList.remove('hidden');
                return;
            }

            const formData = new FormData(eventoForm);
            
            fetch('../../controllers/AgendaController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Espera uma resposta JSON do servidor
            .then(data => {
                if (data.success) {
                    // Sucesso! Atualiza o calendário e fecha o modal.
                    calendar.refetchEvents();
                    closeModal();
                    // Opcional: mostrar uma notificação de sucesso "toast"
                } else {
                    // Erro! Mostra a mensagem de erro vinda do PHP.
                    modalError.textContent = data.message || 'Ocorreu um erro desconhecido.';
                    modalError.classList.remove('hidden');
                }
            })
            .catch(error => {
                modalError.textContent = 'Erro de comunicação com o servidor. Verifique o console (F12).';
                modalError.classList.remove('hidden');
                console.error('Fetch Error:', error);
            });
        });
    });
</script>
