<?php
// session_start() deve ser chamado no início do seu script, antes de qualquer output.
// session_start();

/**
 * -------------------------------------------------------------------------
 * NOTA: As variáveis abaixo ($usuarios, $clientes, $tarefas, etc.)
 * devem ser populadas pelo seu Controller ou pela lógica de busca de dados
 * do seu sistema. O bloco de "Mock de Dados" foi removido.
 * -------------------------------------------------------------------------
 */

// Exemplo de como as variáveis devem ser recebidas do seu controller
// $usuarios = $seuUsuarioController->listar();
// $clientes = $seuClienteController->listar();
// $tarefas = $suaTarefaController->listar($filtroUsuario, $filtroCliente);
// $filtroUsuario = $_GET['usuario'] ?? '';
// $filtroCliente = $_GET['cliente'] ?? '';

if (!isset($filtroUsuario)) $filtroUsuario = $_GET['usuario'] ?? '';
if (!isset($filtroCliente)) $filtroCliente = $_GET['cliente'] ?? '';
// --- Fim do Mock de Dados ---


// Lógica para agrupar tarefas por status para o Kanban
$tarefasPorStatus = [
    'pendente' => [],
    'em andamento' => [],
    'concluida' => []
];
foreach ($tarefas as $tarefa) {
    if (isset($tarefasPorStatus[$tarefa['status']])) {
        $tarefasPorStatus[$tarefa['status']][] = $tarefa;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Tarefas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        .toast-enter {
            animation: slideInUp 0.3s ease-out forwards;
        }

        .toast-exit {
            animation: slideOutDown 0.3s ease-in forwards;
        }

        .sortable-ghost {
            background: #e0e7ff;
            opacity: 0.7;
            border: 2px dashed #6366f1;
        }

        .sortable-drag {
            opacity: 1 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="text-slate-700">

    <div class="container mx-auto p-4 md:p-6 lg:p-8">

        <!-- MENSAGEM DE SUCESSO -->
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div id="success-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-md relative mb-6 shadow-sm" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="w-6 h-6 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Sucesso!</p>
                        <p class="text-sm"><?= htmlspecialchars($_SESSION['sucesso']); ?></p>
                    </div>
                </div>
                <?php unset($_SESSION['sucesso']); ?>
            </div>
        <?php endif; ?>

        <!-- CABEÇALHO E FILTROS -->
        <header class="bg-white p-4 rounded-lg shadow-sm mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-3xl font-bold text-slate-800">Tarefas</h1>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="cadastrar_tarefa.php" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nova Tarefa
                    </a>
                    <button id="btn-toggle-view" class="inline-flex items-center gap-2 bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 transition-colors">
                        <!-- O conteúdo (ícone e texto) será trocado via JS -->
                    </button>
                </div>
            </div>
            <hr class="my-4 border-slate-200">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-center">
                <select name="usuario" class="w-full sm:w-auto border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Filtrar por Responsável</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>" <?= $filtroUsuario == $u['id_usuario'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="cliente" class="w-full sm:w-auto border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Filtrar por Cliente</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id_cliente'] ?>" <?= $filtroCliente == $c['id_cliente'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="w-full sm:w-auto bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-600 transition-colors">Aplicar Filtro</button>
                <a href="listar_tarefa.php" class="w-full sm:w-auto text-center text-sm text-slate-500 hover:text-indigo-600 hover:underline py-2">Limpar</a>
            </form>
        </header>

        <!-- VISUALIZAÇÃO EM TABELA -->
        <div id="tabela-view">
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full bg-white text-sm text-left text-slate-700">
                    <thead class="bg-slate-100 text-xs uppercase font-semibold text-slate-600">
                        <tr>
                            <th class="px-6 py-3">Descrição</th>
                            <th class="px-6 py-3">Responsável</th>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Criada em</th>
                            <th class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php if (!empty($tarefas)): ?>
                            <?php foreach ($tarefas as $t): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-800"><?= htmlspecialchars($t['descricao']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($t['nome_usuario']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($t['nome_cliente']) ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $statusClasses = [
                                            'pendente' => 'bg-yellow-100 text-yellow-800',
                                            'em andamento' => 'bg-blue-100 text-blue-800',
                                            'concluida' => 'bg-green-100 text-green-800'
                                        ];
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$t['status']] ?? 'bg-slate-100 text-slate-800' ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $t['status']))) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500"><?= date('d/m/Y H:i', strtotime($t['data_criacao'])) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-4">
                                            <a href="editar_tarefa.php?id=<?= $t['id_tarefa'] ?>" class="text-slate-500 hover:text-indigo-600" title="Editar">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>
                                            <a href="../../controllers/TarefaController.php?action=excluir&id=<?= $t['id_tarefa'] ?>" class="text-slate-500 hover:text-red-600" title="Excluir" onclick="return confirm('Deseja realmente excluir esta tarefa?')">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.067-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                        <p>Nenhuma tarefa encontrada.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VISUALIZAÇÃO KANBAN -->
        <div id="kanban-view" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $colunas = [
                    'pendente' => ['titulo' => 'Pendente', 'cor' => 'yellow'],
                    'em andamento' => ['titulo' => 'Em Andamento', 'cor' => 'blue'],
                    'concluida' => ['titulo' => 'Concluída', 'cor' => 'green']
                ];
                foreach ($colunas as $status => $coluna):
                    $tarefasDaColuna = $tarefasPorStatus[$status];
                ?>
                    <div class="bg-slate-100 rounded-xl">
                        <div class="p-4 border-b-4 rounded-t-lg border-<?= $coluna['cor'] ?>-500">
                            <h3 class="text-lg font-bold text-slate-800 flex justify-between items-center">
                                <?= $coluna['titulo'] ?>
                                <span id="count-<?= $status ?>" class="text-sm font-semibold bg-<?= $coluna['cor'] ?>-200 text-<?= $coluna['cor'] ?>-800 px-2.5 py-0.5 rounded-full">
                                    <?= count($tarefasDaColuna) ?>
                                </span>
                            </h3>
                        </div>
                        <ul id="coluna-<?= $status ?>" data-status="<?= $status ?>" class="p-4 min-h-[400px] flex flex-col gap-4">
                            <?php if (empty($tarefasDaColuna)): ?>
                                <li class="text-center text-slate-400 text-sm mt-4">Nenhuma tarefa aqui.</li>
                            <?php else: ?>
                                <?php foreach ($tarefasDaColuna as $t): ?>
                                    <li class="bg-white rounded-lg p-4 shadow-sm cursor-grab border-l-4 border-slate-200 hover:shadow-md hover:border-indigo-500 transition-all duration-200"
                                        data-id="<?= $t['id_tarefa'] ?>"
                                        data-descricao="<?= htmlspecialchars($t['descricao']) ?>"
                                        data-usuario="<?= htmlspecialchars($t['nome_usuario']) ?>"
                                        data-cliente="<?= htmlspecialchars($t['nome_cliente']) ?>"
                                        onclick="abrirModal(this)">
                                        <p class="font-semibold text-slate-800 text-sm leading-tight mb-2"><?= htmlspecialchars($t['descricao']) ?></p>
                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                            <span><?= htmlspecialchars($t['nome_usuario']) ?></span>
                                            <span class="font-medium"><?= htmlspecialchars($t['nome_cliente']) ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALHES -->
    <div id="modal-tarefa" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 hidden transition-opacity duration-300">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4 p-6 transform transition-all duration-300 scale-95">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-200">
                <h3 class="text-xl font-bold text-slate-800">Detalhes da Tarefa</h3>
                <button onclick="fecharModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="space-y-3 mb-6">
                <div><strong class="text-sm text-slate-500 block">Descrição:</strong>
                    <p id="modal-descricao" class="text-slate-700"></p>
                </div>
                <div><strong class="text-sm text-slate-500 block">Responsável:</strong>
                    <p id="modal-usuario" class="text-slate-700"></p>
                </div>
                <div><strong class="text-sm text-slate-500 block">Cliente:</strong>
                    <p id="modal-cliente" class="text-slate-700"></p>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a id="btn-editar" href="#" class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Editar</a>
                <a id="btn-excluir" href="#" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="return confirm('Deseja realmente excluir esta tarefa?')">Excluir</a>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white hidden z-50">
        <p id="toast-message"></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabelaView = document.getElementById('tabela-view');
            const kanbanView = document.getElementById('kanban-view');
            const btnToggleView = document.getElementById('btn-toggle-view');
            const modal = document.getElementById('modal-tarefa');
            const modalContent = modal.querySelector('.transform');
            const successAlert = document.getElementById('success-alert');

            const kanbanIcon = `<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg><span>Kanban</span>`;
            const listIcon = `<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-0.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg><span>Lista</span>`;

            let isKanbanView = localStorage.getItem('viewMode') === 'kanban';

            function updateView() {
                if (isKanbanView) {
                    tabelaView.classList.add('hidden');
                    kanbanView.classList.remove('hidden');
                    btnToggleView.innerHTML = listIcon;
                    localStorage.setItem('viewMode', 'kanban');
                } else {
                    tabelaView.classList.remove('hidden');
                    kanbanView.classList.add('hidden');
                    btnToggleView.innerHTML = kanbanIcon;
                    localStorage.setItem('viewMode', 'table');
                }
            }
            updateView();

            btnToggleView.addEventListener('click', () => {
                isKanbanView = !isKanbanView;
                updateView();
            });

            window.abrirModal = function(el) {
                document.getElementById('modal-descricao').textContent = el.dataset.descricao;
                document.getElementById('modal-usuario').textContent = el.dataset.usuario;
                document.getElementById('modal-cliente').textContent = el.dataset.cliente;
                document.getElementById('btn-editar').href = `editar_tarefa.php?id=${el.dataset.id}`;
                document.getElementById('btn-excluir').href = `../../controllers/TarefaController.php?action=excluir&id=${el.dataset.id}`;

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }, 10);
            }

            window.fecharModal = function() {
                modal.classList.remove('opacity-100');
                modalContent.classList.add('scale-95');
                modalContent.classList.remove('scale-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) fecharModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) fecharModal();
            });

            let toastTimeout;
            window.showToast = function(message, type = 'success') {
                const toast = document.getElementById('toast');
                const toastMessage = document.getElementById('toast-message');
                toastMessage.textContent = message;
                toast.className = 'fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white z-50';
                toast.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
                clearTimeout(toastTimeout);
                toast.classList.remove('hidden', 'toast-exit');
                toast.classList.add('toast-enter');
                toastTimeout = setTimeout(() => {
                    toast.classList.remove('toast-enter');
                    toast.classList.add('toast-exit');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 4000);
            }

            const statuses = ['pendente', 'em andamento', 'concluida'];

            function updateColumnCount(status) {
                const column = document.getElementById(`coluna-${status}`);
                const countElement = document.getElementById(`count-${status}`);
                if (column && countElement) {
                    const count = column.querySelectorAll('li[data-id]').length;
                    countElement.textContent = count;
                }
            }

            statuses.forEach(status => {
                const columnEl = document.getElementById(`coluna-${status}`);
                if (columnEl) {
                    new Sortable(columnEl, {
                        group: 'tarefas',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        onAdd: function(evt) {
                            const tarefaId = evt.item.dataset.id;
                            const novoStatus = evt.to.dataset.status;
                            const antigoStatus = evt.from.dataset.status;

                            updateColumnCount(novoStatus);
                            updateColumnCount(antigoStatus);

                            fetch('../../controllers/atualizar_status_tarefa.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: new URLSearchParams({
                                        id_tarefa: tarefaId,
                                        novo_status: novoStatus
                                    })
                                })
                                .then(res => res.ok ? res.json() : Promise.reject('Erro na resposta do servidor.'))
                                .then(data => {
                                    if (data.success) {
                                        showToast('Status da tarefa atualizado!', 'success');
                                    } else {
                                        return Promise.reject(data.message || 'Falha ao atualizar o status.');
                                    }
                                })
                                .catch(error => {
                                    showToast(error.toString(), 'error');
                                    // Reverte a ação no front-end em caso de erro
                                    evt.from.appendChild(evt.item);
                                    updateColumnCount(novoStatus);
                                    updateColumnCount(antigoStatus);
                                });
                        }
                    });
                }
            });

            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.5s ease';
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>