<h2 class="text-2xl font-semibold text-gray-800 mb-6">Editar Tarefa</h2>
<form method="POST" action="../../index.php?controller=tarefa&action=editar&id_tarefa=<?= $tarefa['id_tarefa'] ?>" class="bg-white p-6 rounded-lg shadow space-y-6">

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" id="descricao" class="w-full border rounded p-2" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" class="w-full border rounded p-2">
            <option value="pendente" <?= $tarefa['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="em andamento" <?= $tarefa['status'] === 'em andamento' ? 'selected' : '' ?>>Em Andamento</option>
            <option value="concluida" <?= $tarefa['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        <i class="fas fa-sync-alt mr-1"></i> Atualizar
    </button>
</form>