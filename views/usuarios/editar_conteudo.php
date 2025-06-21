<div class="max-w-4xl mx-auto">
    <?php if ($salvoComSucesso): ?>
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-6 border border-green-200">
            <strong>Sucesso!</strong> Usuário atualizado com sucesso.
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-pen"></i> Editar Usuário
        </h2>
        <a href="listar.php" class="text-sm text-gray-600 hover:text-blue-600">
            <i class="fas fa-arrow-left mr-1"></i> Voltar à Lista
        </a>
    </div>

    <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data" onsubmit="validarFormulario(event)" class="bg-white p-6 rounded-lg shadow">
        <input type="hidden" name="action" value="atualizar" />
        <input type="hidden" name="id_usuario" value="<?= $dados['id_usuario'] ?>" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" id="nome" name="nome" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['nome'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['email'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['cpf'], ENT_QUOTES) ?>" required onkeypress="apenasNumeros(event)" oninput="formatarCPF(this)">
            </div>

            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" id="telefone" name="telefone" maxlength="15" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['telefone'], ENT_QUOTES) ?>" required onkeypress="apenasNumeros(event)" oninput="formatarTelefone(this)">
            </div>

            <div>
                <label for="creci" class="block text-sm font-medium text-gray-700">CRECI</label>
                <input type="text" id="creci" name="creci" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['creci'], ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Foto Atual</label>
                <?php if (!empty($dados['foto'])): ?>
                    <img src="<?= htmlspecialchars($dados['foto'], ENT_QUOTES) ?>" alt="Foto do Usuário" class="w-24 rounded mt-1">
                <?php else: ?>
                    <p class="text-gray-400 italic">Nenhuma foto enviada.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700">Alterar Foto</label>
                <input type="file" id="foto" name="foto" class="mt-1 block w-full text-sm text-gray-600">
            </div>

            <div>
                <label for="permissao" class="block text-sm font-medium text-gray-700">Permissão</label>
                <select id="permissao" name="permissao" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="Admin" <?= $dados['permissao'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Coordenador" <?= $dados['permissao'] === 'Coordenador' ? 'selected' : '' ?>>Coordenador</option>
                    <option value="Corretor" <?= $dados['permissao'] === 'Corretor' ? 'selected' : '' ?>>Corretor</option>
                </select>
            </div>

            <div>
                <label for="id_imobiliaria" class="block text-sm font-medium text-gray-700">Imobiliária</label>
                <select name="id_imobiliaria">
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>" <?= $usuario['id_imobiliaria'] == $imob['id_imobiliaria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="listar.php" class="text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-left mr-1"></i> Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                <i class="fas fa-save mr-1"></i> Salvar
            </button>
        </div>
    </form>
</div>