<h2 class="text-2xl font-semibold text-gray-800 mb-6">Cadastrar Nova Tarefa</h2>
<form method="POST" action="../../index.php?controller=tarefa&action=cadastrar" class="bg-white p-6 rounded-lg shadow space-y-6">

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" id="descricao" class="w-full border rounded p-2" required></textarea>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" class="w-full border rounded p-2">
            <option value="pendente">Pendente</option>
            <option value="em andamento">Em Andamento</option>
            <option value="concluida">Concluída</option>
        </select>
    </div>

    <div>
        <label for="id_cliente" class="block text-sm font-medium text-gray-700">ID do Cliente</label>
        <input type="number" name="id_cliente" id="id_cliente" class="w-full border rounded p-2" required>
    </div>

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        <i class="fas fa-save mr-1"></i> Salvar
    </button>
</form>