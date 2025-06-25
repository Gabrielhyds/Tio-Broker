<div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Minhas Tarefas</h2>
</div>

<a href="cadastrar_tarefa.php" class="inline-block mb-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
    <i class="fas fa-plus-circle mr-2"></i> Nova Tarefa
</a>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left font-medium text-gray-600">Descrição</th>
                <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                <th class="px-4 py-2 text-left font-medium text-gray-600">Criada em</th>
                <th class="px-4 py-2 text-left font-medium text-gray-600">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tarefas as $t): ?>
                <tr class="border-t">
                    <td class="px-4 py-2"><?= htmlspecialchars($t['descricao']) ?></td>
                    <td class="px-4 py-2"><?= ucfirst($t['status']) ?></td>
                    <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($t['data_criacao'])) ?></td>
                    <td class="px-4 py-2">
                        <a href="editar_tarefa.php?id_tarefa=<?= $t['id_tarefa'] ?>" class="text-blue-600 hover:underline mr-3"><i class="fas fa-edit"></i> Editar</a>
                        <a href="../../index.php?controller=tarefa&action=excluir&id_tarefa=<?= $t['id_tarefa'] ?>" onclick="return confirm('Deseja excluir esta tarefa?')" class="text-red-600 hover:underline"><i class="fas fa-trash-alt"></i> Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>