<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['sucesso']);
        unset($_SESSION['sucesso']); ?>
    </div>
<?php endif; ?>
<form method="GET" class="mb-4 flex flex-wrap gap-4">
    <select name="usuario" class="border border-gray-300 rounded px-3 py-2 text-sm">
        <option value="">Filtrar por Responsável</option>
        <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>" <?= $filtroUsuario == $u['id_usuario'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="cliente" class="border border-gray-300 rounded px-3 py-2 text-sm">
        <option value="">Filtrar por Cliente</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id_cliente'] ?>" <?= $filtroCliente == $c['id_cliente'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        Aplicar Filtro
    </button>
    <a href="listar_tarefa.php" class="text-sm text-gray-600 hover:underline mt-2">
        Limpar
    </a>
</form>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Tarefas</h2>
    <div class="flex gap-2">
        <a href="cadastrar_tarefa.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nova Tarefa</a>
        <button onclick="alternarVisualizacao()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">Alternar Visualização</button>
    </div>
</div>

<!-- TABELA -->
<div id="tabela-view" class="">
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full bg-white text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-xs uppercase font-semibold text-gray-600">
                <tr>
                    <th class="px-4 py-2">Descrição</th>
                    <th class="px-4 py-2">Responsável</th>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Criada em</th>
                    <th class="px-4 py-2 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tarefas)): ?>
                    <?php foreach ($tarefas as $t): ?>
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-2"><?= htmlspecialchars($t['descricao']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($t['nome_usuario']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($t['nome_cliente']) ?></td>
                            <td class="px-4 py-2 capitalize"><?= htmlspecialchars($t['status']) ?></td>
                            <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($t['data_criacao'])) ?></td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="editar_tarefa.php?id=<?= $t['id_tarefa'] ?>" class="text-yellow-500 hover:text-yellow-600" title="Editar"><i class="fas fa-pen"></i></a>
                                    <a href="../../controllers/TarefaController.php?action=excluir&id=<?= $t['id_tarefa'] ?>" class="text-red-500 hover:text-red-600" title="Excluir" onclick="return confirm('Deseja excluir esta tarefa?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">Nenhuma tarefa cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- KANBAN -->
<div id="kanban-view" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $colunas = ['pendente' => 'Pendente', 'em andamento' => 'Em andamento', 'concluida' => 'Concluída'];
        foreach ($colunas as $status => $titulo):
        ?>
            <div class="bg-gray-50 rounded-lg shadow-lg p-4 border border-gray-200" data-status="<?= $status ?>">
                <h3 class="text-xl font-semibold text-center text-gray-700 mb-3"><?= $titulo ?></h3>
                <ul id="coluna-<?= $status ?>" class="min-h-[300px] flex flex-col gap-3">
                    <?php foreach ($tarefas as $t): ?>
                        <?php if ($t['status'] === $status): ?>
                            <li class="bg-white rounded-lg p-4 shadow cursor-pointer border hover:shadow-md transition duration-200" data-id="<?= $t['id_tarefa'] ?>" onclick="abrirModal(this)"
                                data-descricao="<?= htmlspecialchars($t['descricao']) ?>"
                                data-usuario="<?= htmlspecialchars($t['nome_usuario']) ?>"
                                data-cliente="<?= htmlspecialchars($t['nome_cliente']) ?>">
                                <p class="font-medium text-gray-800 text-sm leading-tight mb-1"><?= htmlspecialchars($t['descricao']) ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($t['nome_usuario']) ?> | <?= htmlspecialchars($t['nome_cliente']) ?></p>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MODAL -->
<div id="modal-tarefa" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Detalhes da Tarefa</h3>
            <button onclick="fecharModal()" class="text-gray-500 hover:text-gray-800 text-xl">&times;</button>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-1"><strong>Descrição:</strong> <span id="modal-descricao"></span></p>
            <p class="text-sm text-gray-600 mb-1"><strong>Responsável:</strong> <span id="modal-usuario"></span></p>
            <p class="text-sm text-gray-600"><strong>Cliente:</strong> <span id="modal-cliente"></span></p>
        </div>
        <div class="flex justify-end gap-3">
            <a id="btn-editar" href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">Editar</a>
            <a id="btn-excluir" href="#" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm" onclick="return confirm('Deseja excluir esta tarefa?')">Excluir</a>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function alternarVisualizacao() {
        document.getElementById('tabela-view').classList.toggle('hidden');
        document.getElementById('kanban-view').classList.toggle('hidden');
    }

    function abrirModal(el) {
        document.getElementById('modal-descricao').textContent = el.dataset.descricao;
        document.getElementById('modal-usuario').textContent = el.dataset.usuario;
        document.getElementById('modal-cliente').textContent = el.dataset.cliente;
        document.getElementById('btn-editar').href = `editar_tarefa.php?id=${el.dataset.id}`;
        document.getElementById('btn-excluir').href = `../../controllers/TarefaController.php?action=excluir&id=${el.dataset.id}`;
        document.getElementById('modal-tarefa').classList.remove('hidden');
    }

    function fecharModal() {
        document.getElementById('modal-tarefa').classList.add('hidden');
    }
    ['pendente', 'em andamento', 'concluida'].forEach(status => {
        new Sortable(document.getElementById(`coluna-${status}`), {
            group: 'tarefas',
            animation: 150,
            onAdd: function(evt) {
                const tarefaId = evt.item.dataset.id;
                const novoStatus = evt.to.parentElement.dataset.status;
                fetch('../../controllers/atualizar_status_tarefa.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            id_tarefa: tarefaId,
                            novo_status: novoStatus
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) alert('Erro ao atualizar tarefa!');
                    });
            }
        });
    });
</script>