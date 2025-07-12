<!-- Bloco PHP para exibir uma mensagem de sucesso se um usuário for removido. -->
<?php if ($usuarioRemovido): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
        <i class="fas fa-check-circle mr-2"></i>
        Usuário removido da imobiliária com sucesso!
    </div>
<?php endif; ?>

<!-- Bloco PHP para exibir uma mensagem de sucesso se um usuário for vinculado. -->
<?php if ($usuarioIncluidoEscolhido): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
        <i class="fas fa-check-circle mr-2"></i>
        Usuário vinculado à imobiliária com sucesso!
    </div>
<?php endif; ?>

<!-- Título da página de edição. -->
<h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
    <i class="fas fa-pen"></i> Editar Imobiliária
</h2>

<!-- Primeiro card: Formulário para editar os dados da imobiliária. -->
<div class="bg-white p-6 rounded-xl shadow mb-6">
    <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
        <!-- Campos ocultos para enviar a ação e o ID da imobiliária. -->
        <input type="hidden" name="action" value="atualizar">
        <input type="hidden" name="id" value="<?= $dadosImob['id_imobiliaria'] ?>">

        <!-- Grid para organizar os campos do formulário. -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Imobiliária</label>
                <!-- O valor é preenchido com o nome atual da imobiliária. -->
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($dadosImob['nome']) ?>" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                <!-- Campo de CNPJ com máscara e validação via JavaScript. -->
                <input type="text" name="cnpj" id="cnpj" value="<?= htmlspecialchars($dadosImob['cnpj']) ?>" required maxlength="18"
                    onkeypress="apenasNumeros(event)" oninput="formatarCNPJ(this)"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        <!-- Botões de ação do formulário. -->
        <div class="mt-6 flex justify-end gap-2">
            <a href="listar_imobiliaria.php" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-1"></i> Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Salvar
            </button>
        </div>
    </form>
</div>

<!-- Segundo card: Formulário para adicionar um usuário existente a esta imobiliária. -->
<div class="bg-white p-6 rounded-xl shadow mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Adicionar Usuário à Imobiliária
    </h3>
    <!-- Verifica se há usuários disponíveis para vincular. -->
    <?php if (!empty($usuariosDisponiveis)): ?>
        <form action="../../controllers/UsuarioController.php" method="GET" class="grid md:grid-cols-3 gap-4 items-end">
            <input type="hidden" name="idImobiliaria" value="<?= $idImobiliaria ?>">
            <div class="md:col-span-2">
                <label for="selectUsuario" class="block text-sm font-medium text-gray-700 mb-1">Selecione um usuário</label>
                <!-- O select é populado com usuários que não estão nesta imobiliária. -->
                <select id="selectUsuario" name="incluirUsuario"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="" disabled selected>Escolha um usuário...</option>
                    <?php foreach ($usuariosDisponiveis as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Adicionar
                </button>
            </div>
        </form>
    <?php else: ?>
        <p class="text-gray-500">Nenhum usuário disponível para vincular.</p>
    <?php endif; ?>
</div>

<!-- Terceiro card: Lista de usuários que já estão vinculados a esta imobiliária. -->
<div class="bg-white p-6 rounded-xl shadow">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-users"></i> Usuários vinculados à Imobiliária
    </h3>
    <!-- Verifica se já existem usuários vinculados. -->
    <?php if (!empty($usuarios)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left px-4 py-2 border">ID</th>
                        <th class="text-left px-4 py-2 border">Nome</th>
                        <th class="text-left px-4 py-2 border">Email</th>
                        <th class="text-center px-4 py-2 border">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop para exibir cada usuário vinculado. -->
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= $user['id_usuario'] ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($user['nome']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Link para editar o usuário. -->
                                    <a href="../usuarios/editar.php?id=<?= $user['id_usuario'] ?>"
                                        class="px-2 py-1 text-yellow-600 hover:text-yellow-800">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <!-- Link para remover o vínculo do usuário com esta imobiliária. -->
                                    <a href="../../controllers/UsuarioController.php?removerImobiliaria=<?= $user['id_usuario'] ?>&idImobiliaria=<?= $idImobiliaria ?>"
                                        onclick="return confirm('Deseja realmente remover este usuário da imobiliária?')"
                                        class="px-2 py-1 text-red-600 hover:text-red-800">
                                        <i class="fas fa-user-minus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Nenhum usuário vinculado.</p>
    <?php endif; ?>
</div>

<!-- Scripts JavaScript para validação e formatação de campos. -->
<script>
    // Função para permitir apenas a digitação de números.
    function apenasNumeros(event) {
        if (!/\d/.test(event.key)) event.preventDefault();
    }

    // Função para aplicar a máscara de CNPJ enquanto o usuário digita.
    function formatarCNPJ(campo) {
        let cnpj = campo.value.replace(/\D/g, '').slice(0, 14); // Remove não-dígitos e limita a 14.
        cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
        cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
        cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");
        campo.value = cnpj;
    }

    // Função para validar o CNPJ antes de submeter o formulário.
    function validarFormulario(event) {
        const cnpj = document.getElementById('cnpj').value.replace(/\D/g, '');
        if (cnpj.length !== 14) {
            event.preventDefault(); // Impede o envio do formulário.
            alert("CNPJ inválido! Deve conter 14 dígitos.");
        }
    }
</script>