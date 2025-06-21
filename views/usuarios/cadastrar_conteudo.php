<div class="max-w-4xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
        <i class="fas fa-user-plus text-blue-500"></i> Cadastrar Novo Usuário
    </h2>

    <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="cadastrar">

        <div>
            <label for="nome" class="block font-medium mb-1">Nome</label>
            <input type="text" name="nome" id="nome" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="email" class="block font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="cpf" class="block font-medium mb-1">CPF</label>
            <input type="text" name="cpf" id="cpf" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="telefone" class="block font-medium mb-1">Telefone</label>
            <input type="text" name="telefone" id="telefone" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="senha" class="block font-medium mb-1">Senha</label>
            <input type="password" name="senha" id="senha" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div>
            <label for="creci" class="block font-medium mb-1">CRECI</label>
            <input type="text" name="creci" id="creci" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="foto" class="block font-medium mb-1">Foto (opcional)</label>
            <input type="file" name="foto" id="foto" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div>
            <label for="permissao" class="block font-medium mb-1">Permissão</label>
            <select name="permissao" id="permissao" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
                <option value="Admin">Admin</option>
                <option value="Coordenador">Coordenador</option>
                <option value="Corretor">Corretor</option>
            </select>
        </div>

        <div>
            <label for="id_imobiliaria" class="block font-medium mb-1">Imobiliária</label>
            <select name="id_imobiliaria" id="id_imobiliaria" class="w-full border rounded px-3 py-2">
                <?php foreach ($listaImobiliarias as $imob): ?>
                    <option value="<?= $imob['id_imobiliaria'] ?>">
                        <?= htmlspecialchars($imob['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex justify-between mt-6">
            <a href="listar.php" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                <i class="fas fa-check mr-2"></i> Cadastrar
            </button>
        </div>
    </form>
</div>