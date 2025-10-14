<?php
// session_start() deve ser chamado no início do seu script, antes de qualquer output.
// session_start();

/**
 * -------------------------------------------------------------------------
 * NOTA: As variáveis abaixo ($usuarios, $clientes, $tarefas, etc.)
 * devem ser populadas pelo seu Controller.
 * -------------------------------------------------------------------------
 */

if (!isset($filtroUsuario)) $filtroUsuario = $_GET['usuario'] ?? '';
if (!isset($filtroCliente)) $filtroCliente = $_GET['cliente'] ?? '';
if (!isset($filtroPrioridade)) $filtroPrioridade = $_GET['prioridade'] ?? '';


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

        <!-- CABEÇALHO E FILTROS -->
        <header class="bg-white p-4 rounded-lg shadow-sm mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-3xl font-bold text-slate-800">Tarefas</h1>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="cadastrar_tarefa.php" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Nova Tarefa
                    </a>
                    <button id="btn-toggle-view" class="inline-flex items-center gap-2 bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 transition-colors"></button>
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
                <select name="prioridade" class="w-full sm:w-auto border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Filtrar por Prioridade</option>
                    <option value="baixa" <?= $filtroPrioridade == 'baixa' ? 'selected' : '' ?>>Baixa</option>
                    <option value="media" <?= $filtroPrioridade == 'media' ? 'selected' : '' ?>>Média</option>
                    <option value="alta" <?= $filtroPrioridade == 'alta' ? 'selected' : '' ?>>Alta</option>
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
                            <th class="px-6 py-3">Prioridade</th>
                            <th class="px-6 py-3">Criada em</th>
                            <th class="px-6 py-3">Prazo</th>
                            <th class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php if (!empty($tarefas)): ?>
                            <?php foreach ($tarefas as $t): ?>
                                <tr class="hover:bg-slate-50 transition-colors" data-id="<?= $t['id_tarefa'] ?>">
                                    <td class="px-6 py-4 font-medium text-slate-800"><?= htmlspecialchars($t['descricao']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($t['nome_usuario']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($t['nome_cliente']) ?></td>
                                    <td class="px-6 py-4" data-cell="status">
                                        <?php
                                        $statusClasses = ['pendente' => 'bg-yellow-100 text-yellow-800', 'em andamento' => 'bg-blue-100 text-blue-800', 'concluida' => 'bg-green-100 text-green-800'];
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$t['status']] ?? 'bg-slate-100 text-slate-800' ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $t['status']))) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $prioridadeClass = 'bg-slate-100 text-slate-800'; // Default
                                        switch ($t['prioridade']) {
                                            case 'alta': $prioridadeClass = 'bg-red-100 text-red-800'; break;
                                            case 'media': $prioridadeClass = 'bg-yellow-100 text-yellow-800'; break;
                                            case 'baixa': $prioridadeClass = 'bg-sky-100 text-sky-800'; break;
                                        }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $prioridadeClass ?>">
                                            <?= htmlspecialchars(ucfirst($t['prioridade'] ?? 'N/A')) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500"><?= date('d/m/Y H:i', strtotime($t['data_criacao'])) ?></td>
                                    <td class="px-6 py-4" data-cell="prazo">
                                        <?php if (!empty($t['prazo'])):
                                            $prazo = new DateTime($t['prazo']);
                                            $hoje = new DateTime(date('Y-m-d'));
                                            $atrasada = $prazo < $hoje && $t['status'] !== 'concluida';
                                        ?>
                                            <span class="flex items-center gap-2 <?= $atrasada ? 'text-red-600 font-semibold' : 'text-slate-500' ?>">
                                                <?php if($atrasada): ?>
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                                <?php endif; ?>
                                                <?= $prazo->format('d/m/Y') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center gap-4">
                                            <a href="editar_tarefa.php?id=<?= $t['id_tarefa'] ?>" class="text-slate-500 hover:text-indigo-600" title="Editar"><svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg></a>
                                            <a href="../../controllers/TarefaController.php?action=excluir&id=<?= $t['id_tarefa'] ?>" class="text-slate-500 hover:text-red-600 btn-excluir" title="Excluir"><svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.067-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
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
                $colunas = ['pendente' => ['titulo' => 'Pendente', 'cor' => 'yellow'], 'em andamento' => ['titulo' => 'Em Andamento', 'cor' => 'blue'], 'concluida' => ['titulo' => 'Concluída', 'cor' => 'green']];
                foreach ($colunas as $status => $coluna):
                    $tarefasDaColuna = $tarefasPorStatus[$status];
                ?>
                    <div class="bg-slate-100 rounded-xl">
                        <div class="p-4 border-b-4 rounded-t-lg border-<?= $coluna['cor'] ?>-500">
                            <h3 class="text-lg font-bold text-slate-800 flex justify-between items-center">
                                <?= $coluna['titulo'] ?>
                                <span id="count-<?= $status ?>" class="text-sm font-semibold bg-<?= $coluna['cor'] ?>-200 text-<?= $coluna['cor'] ?>-800 px-2.5 py-0.5 rounded-full"><?= count($tarefasDaColuna) ?></span>
                            </h3>
                        </div>
                        <ul id="coluna-<?= $status ?>" data-status="<?= $status ?>" class="p-4 min-h-[400px] flex flex-col gap-4">
                            <?php if (empty($tarefasDaColuna)): ?>
                                <li class="text-center text-slate-400 text-sm mt-4 kanban-placeholder">Nenhuma tarefa aqui.</li>
                            <?php else: ?>
                                <?php foreach ($tarefasDaColuna as $t): ?>
                                    <?php
                                    $prioridadeBorder = 'border-slate-200'; // Default
                                    switch ($t['prioridade']) {
                                        case 'alta': $prioridadeBorder = 'border-red-500'; break;
                                        case 'media': $prioridadeBorder = 'border-yellow-500'; break;
                                        case 'baixa': $prioridadeBorder = 'border-sky-500'; break;
                                    }
                                    ?>
                                    <li class="bg-white rounded-lg p-4 shadow-sm cursor-grab border-l-4 <?= $prioridadeBorder ?> hover:shadow-md transition-all duration-200"
                                        data-id="<?= $t['id_tarefa'] ?>"
                                        data-descricao="<?= htmlspecialchars($t['descricao']) ?>"
                                        data-usuario="<?= htmlspecialchars($t['nome_usuario']) ?>"
                                        data-cliente="<?= htmlspecialchars($t['nome_cliente']) ?>"
                                        data-prioridade="<?= htmlspecialchars($t['prioridade']) ?>"
                                        data-prazo="<?= htmlspecialchars($t['prazo'] ?? '') ?>"
                                        data-status="<?= htmlspecialchars($t['status']) ?>"
                                        onclick="abrirModal(this)">
                                        <p class="font-semibold text-slate-800 text-sm leading-tight mb-2"><?= htmlspecialchars($t['descricao']) ?></p>
                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                            <span><?= htmlspecialchars($t['nome_usuario']) ?></span>
                                            <span class="font-medium"><?= htmlspecialchars($t['nome_cliente']) ?></span>
                                        </div>
                                        <?php if (!empty($t['prazo'])):
                                            $prazo = new DateTime($t['prazo']);
                                            $hoje = new DateTime(date('Y-m-d'));
                                            $atrasada = $prazo < $hoje && $t['status'] !== 'concluida';
                                        ?>
                                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center gap-2 text-xs <?= $atrasada ? 'text-red-600 font-semibold' : 'text-slate-500' ?>">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h22.5" /></svg>
                                            Prazo: <?= $prazo->format('d/m/Y') ?>
                                            <?php if ($atrasada): ?>
                                                <span class="ml-auto text-red-600" title="Tarefa Atrasada">
                                                   <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALHES (NOVO DESIGN) -->
    <div id="modal-tarefa" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 hidden transition-opacity duration-300">
        <div class="bg-slate-50 rounded-xl shadow-xl max-w-lg w-full m-4 transform transition-all duration-300 scale-95">
            
            <!-- Header -->
            <div class="bg-white p-5 rounded-t-xl border-b border-slate-200 flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Detalhes da Tarefa</h3>
                    <p class="text-sm text-slate-500">Informações completas da tarefa selecionada.</p>
                </div>
                <button onclick="fecharModal()" class="text-slate-400 hover:text-slate-600 text-3xl leading-none">&times;</button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">
                <!-- Descrição -->
                <div class="bg-white p-4 rounded-lg border border-slate-200">
                    <h4 class="text-sm font-semibold text-slate-500 mb-1">Descrição</h4>
                    <p id="modal-descricao" class="text-slate-800 text-base leading-relaxed"></p>
                </div>

                <!-- Detalhes em Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Responsável -->
                    <div class="flex items-start gap-3">
                        <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </div>
                        <div>
                            <strong class="text-sm text-slate-500 block">Responsável</strong>
                            <p id="modal-usuario" class="text-slate-800 font-semibold"></p>
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div class="flex items-start gap-3">
                        <div class="bg-sky-100 text-sky-600 p-2 rounded-lg">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.928A9.095 9.095 0 0112 5.25c2.628 0 4.935.94 6.75 2.501m-13.5 0A9.095 9.095 0 0112 5.25c2.628 0 4.935.94 6.75 2.501m-13.5 0a3 3 0 11-4.682 2.72 9.094 9.094 0 013.741.479m-4.5-2.928a3 3 0 010-5.456m13.5 5.456a3 3 0 000-5.456m-13.5 0a3 3 0 010-5.456" /></svg>
                        </div>
                        <div>
                            <strong class="text-sm text-slate-500 block">Cliente</strong>
                            <p id="modal-cliente" class="text-slate-800 font-semibold"></p>
                        </div>
                    </div>

                    <!-- Prioridade -->
                    <div class="flex items-start gap-3">
                        <div class="bg-amber-100 text-amber-600 p-2 rounded-lg">
                           <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>
                        </div>
                        <div>
                            <strong class="text-sm text-slate-500 block">Prioridade</strong>
                            <div id="modal-prioridade"></div>
                        </div>
                    </div>

                    <!-- Prazo -->
                    <div id="modal-prazo-container" class="flex items-start gap-3">
                        <div class="bg-red-100 text-red-600 p-2 rounded-lg">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h22.5" /></svg>
                        </div>
                        <div>
                            <strong class="text-sm text-slate-500 block">Prazo</strong>
                            <div id="modal-prazo"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer / Actions -->
            <div class="p-5 bg-slate-100 rounded-b-xl flex justify-end gap-3">
                <button onclick="fecharModal()" class="bg-white hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors border border-slate-300">Fechar</button>
                <a id="btn-editar" href="#" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                   <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                    Editar
                </a>
                <a id="btn-excluir" href="#" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors btn-excluir">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.067-2.09 1.02-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    Excluir
                </a>
            </div>
        </div>
    </div>

    <!-- Bloco de classes para garantir a compilação do Tailwind JIT -->
    <div class="hidden">
        <span class="bg-red-100 text-red-800"></span>
        <span class="bg-yellow-100 text-yellow-800"></span>
        <span class="bg-sky-100 text-sky-800"></span>
        <div class="border-red-500"></div>
        <div class="border-yellow-500"></div>
        <div class="border-sky-500"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabelaView = document.getElementById('tabela-view');
            const kanbanView = document.getElementById('kanban-view');
            const btnToggleView = document.getElementById('btn-toggle-view');
            const modal = document.getElementById('modal-tarefa');
            const modalContent = modal.querySelector('.transform');

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
            
            const getPriorityBadge = (priority) => {
                let className = 'bg-slate-100 text-slate-800'; // Default
                switch (priority) {
                    case 'alta': className = 'bg-red-100 text-red-800'; break;
                    case 'media': className = 'bg-yellow-100 text-yellow-800'; break;
                    case 'baixa': className = 'bg-sky-100 text-sky-800'; break;
                }
                const priorityText = priority ? priority.charAt(0).toUpperCase() + priority.slice(1) : 'N/A';
                return `<span class="px-2 py-1 text-xs font-semibold rounded-full ${className}">${priorityText}</span>`;
            };

            window.abrirModal = function(el) {
                document.getElementById('modal-descricao').textContent = el.dataset.descricao;
                document.getElementById('modal-usuario').textContent = el.dataset.usuario;
                document.getElementById('modal-cliente').textContent = el.dataset.cliente;
                document.getElementById('modal-prioridade').innerHTML = getPriorityBadge(el.dataset.prioridade);
                document.getElementById('btn-editar').href = `editar_tarefa.php?id=${el.dataset.id}`;
                document.getElementById('btn-excluir').href = `../../controllers/TarefaController.php?action=excluir&id=${el.dataset.id}`;

                const prazo = el.dataset.prazo;
                const status = el.dataset.status;
                const modalPrazoContainer = document.getElementById('modal-prazo-container');
                const modalPrazo = document.getElementById('modal-prazo');

                if (prazo) {
                    const [year, month, day] = prazo.split('-');
                    const prazoFormatted = `${day}/${month}/${year}`;
                    
                    const prazoDate = new Date(prazo + 'T00:00:00');
                    const hoje = new Date();
                    hoje.setHours(0, 0, 0, 0);

                    let prazoHTML = `<p class="text-slate-800 font-semibold">${prazoFormatted}</p>`;
                    if (prazoDate < hoje && status !== 'concluida') {
                        prazoHTML = `<p class="flex items-center gap-2 text-red-600 font-semibold">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                            ${prazoFormatted} (Atrasada)
                        </p>`;
                    }
                    modalPrazo.innerHTML = prazoHTML;
                    modalPrazoContainer.style.display = 'flex';
                } else {
                     modalPrazoContainer.style.display = 'none';
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    if(modalContent) modalContent.classList.remove('scale-95');
                    if(modalContent) modalContent.classList.add('scale-100');
                }, 10);
            }

            window.fecharModal = function() {
                modal.classList.remove('opacity-100');
                if(modalContent) modalContent.classList.add('scale-95');
                if(modalContent) modalContent.classList.remove('scale-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) fecharModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) fecharModal();
            });
            
            const botoesExcluir = document.querySelectorAll('.btn-excluir');
            botoesExcluir.forEach(function(botao) {
                botao.addEventListener('click', function(event) {
                    event.preventDefault();
                    const urlParaExcluir = this.href;
                    Swal.fire({
                        title: 'Você tem certeza?',
                        text: "Esta ação não poderá ser revertida!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = urlParaExcluir;
                        }
                    });
                });
            });

            // --- LÓGICA KANBAN ---
            const statuses = ['pendente', 'em andamento', 'concluida'];
            
            function updateCardStyle(cardElement) {
                const prazo = cardElement.dataset.prazo;
                const status = cardElement.dataset.status;
                if (!prazo) return;

                const prazoDiv = cardElement.querySelector('div.mt-3');
                if (!prazoDiv) return;

                const prazoDate = new Date(prazo + 'T00:00:00');
                const hoje = new Date();
                hoje.setHours(0, 0, 0, 0);

                const isOverdue = prazoDate < hoje && status !== 'concluida';

                if (isOverdue) {
                    prazoDiv.classList.remove('text-slate-500');
                    prazoDiv.classList.add('text-red-600', 'font-semibold');
                } else {
                    prazoDiv.classList.add('text-slate-500');
                    prazoDiv.classList.remove('text-red-600', 'font-semibold');
                }
            }

            function updateColumnCount(status) {
                const column = document.getElementById(`coluna-${status}`);
                const countElement = document.getElementById(`count-${status}`);
                if (column && countElement) {
                    const count = column.querySelectorAll('li[data-id]').length;
                    countElement.textContent = count;
                }
            }
            
            function togglePlaceholder(column) {
                if (!column) return;
                const placeholder = column.querySelector('.kanban-placeholder');
                const taskItems = column.querySelectorAll('li[data-id]');

                if (taskItems.length === 0) {
                    if (!placeholder) {
                        const newPlaceholder = document.createElement('li');
                        newPlaceholder.className = 'text-center text-slate-400 text-sm mt-4 kanban-placeholder';
                        newPlaceholder.textContent = 'Nenhuma tarefa aqui.';
                        column.appendChild(newPlaceholder);
                    }
                } else {
                    if (placeholder) {
                        placeholder.remove();
                    }
                }
            }

            // ✅ NOVA FUNÇÃO: Sincroniza a linha da tabela com as mudanças do Kanban
            function syncTableRow(tarefaId, novoStatus) {
                const tableRow = document.querySelector(`#tabela-view tr[data-id="${tarefaId}"]`);
                if (!tableRow) return;

                // 1. Atualizar a célula de Status
                const statusCell = tableRow.querySelector('td[data-cell="status"]');
                if (statusCell) {
                    const statusClasses = {
                        'pendente': 'bg-yellow-100 text-yellow-800',
                        'em andamento': 'bg-blue-100 text-blue-800',
                        'concluida': 'bg-green-100 text-green-800'
                    };
                    const statusText = (novoStatus.charAt(0).toUpperCase() + novoStatus.slice(1)).replace('_', ' ');
                    
                    statusCell.innerHTML = `
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClasses[novoStatus] || 'bg-slate-100 text-slate-800'}">
                            ${statusText}
                        </span>`;
                }

                // 2. Atualizar a célula de Prazo
                const prazoCell = tableRow.querySelector('td[data-cell="prazo"]');
                const kanbanCard = document.querySelector(`#kanban-view li[data-id="${tarefaId}"]`);
                if (prazoCell && kanbanCard && kanbanCard.dataset.prazo) {
                    const prazo = kanbanCard.dataset.prazo;
                    const prazoDate = new Date(prazo + 'T00:00:00');
                    const hoje = new Date();
                    hoje.setHours(0, 0, 0, 0);

                    const isOverdue = prazoDate < hoje && novoStatus !== 'concluida';
                    const prazoSpan = prazoCell.querySelector('span');
                    const warningIcon = prazoSpan.querySelector('svg');

                    if (isOverdue) {
                        prazoSpan.classList.remove('text-slate-500');
                        prazoSpan.classList.add('text-red-600', 'font-semibold');
                        if (!warningIcon) {
                            prazoSpan.insertAdjacentHTML('afterbegin', `
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                            `);
                        }
                    } else {
                        prazoSpan.classList.add('text-slate-500');
                        prazoSpan.classList.remove('text-red-600', 'font-semibold');
                        if (warningIcon) {
                            warningIcon.remove();
                        }
                    }
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
                        filter: '.kanban-placeholder',
                        onEnd: function(evt) {
                            const fromColumn = evt.from;
                            const toColumn = evt.to;
                            const item = evt.item;
                            
                            updateColumnCount(fromColumn.dataset.status);
                            togglePlaceholder(fromColumn);
                            if (fromColumn !== toColumn) {
                                updateColumnCount(toColumn.dataset.status);
                                togglePlaceholder(toColumn);
                            }

                            if (fromColumn !== toColumn) {
                                const tarefaId = item.dataset.id;
                                const novoStatus = toColumn.dataset.status;

                                fetch('../../controllers/atualizar_status_tarefa.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: new URLSearchParams({ id_tarefa: tarefaId, novo_status: novoStatus })
                                })
                                .then(res => res.ok ? res.json() : Promise.reject('Erro na resposta.'))
                                .then(data => {
                                    if (!data.success) {
                                        return Promise.reject(data.message || 'Falha ao atualizar.');
                                    }
                                    item.dataset.status = novoStatus;
                                    updateCardStyle(item);
                                    // ✅ SINCRONIZA A TABELA
                                    syncTableRow(tarefaId, novoStatus);
                                })
                                .catch(error => {
                                    // Reverte a ação no front-end em caso de erro
                                    fromColumn.appendChild(item);
                                    updateColumnCount(fromColumn.dataset.status);
                                    updateColumnCount(toColumn.dataset.status);
                                    togglePlaceholder(fromColumn);
                                    togglePlaceholder(toColumn);
                                    updateCardStyle(item);
                                    // Reverte também na tabela
                                    syncTableRow(tarefaId, fromColumn.dataset.status);
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

