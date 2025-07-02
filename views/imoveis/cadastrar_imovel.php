<h2 class="text-2xl font-semibold mb-4">Cadastrar Novo Imóvel</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['erro']);
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="action" value="cadastrar">

    <div>
        <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
        <input type="text" name="titulo" id="titulo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
            <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Selecione</option>
                <option value="venda">Venda</option>
                <option value="locacao">Locação</option>
                <option value="temporada">Temporada</option>
                <option value="lancamento">Lançamento</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Selecione</option>
                <option value="disponivel">Disponível</option>
                <option value="reservado">Reservado</option>
                <option value="vendido">Vendido</option>
                <option value="indisponivel">Indisponível</option>
            </select>
        </div>
    </div>

    <div>
        <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
        <input type="number" step="0.01" name="preco" id="preco" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
        <input type="text" name="endereco" id="endereco" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" id="latitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" id="longitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Imagens</label>
        <input type="file" name="imagens[]" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Vídeos</label>
        <input type="file" name="videos[]" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Documentos</label>
        <input type="file" name="documentos[]" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            Cadastrar Imóvel
        </button>
    </div>
</form>