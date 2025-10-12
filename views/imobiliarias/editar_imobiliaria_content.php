
<!-- Título da página de edição. -->
<h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
    <i class="fas fa-pen text-blue-600"></i> Editar Imobiliária
</h2>

<!-- Primeiro card: Formulário para editar os dados da imobiliária. -->
<div class="bg-white p-6 rounded-xl shadow mb-6">
    <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
        <!-- Campos ocultos para enviar a ação, o ID e o tipo de pessoa. -->
        <input type="hidden" name="action" value="atualizar">
        <input type="hidden" name="id" value="<?= $dadosImob['id_imobiliaria'] ?>">
        <input type="hidden" name="tipo_pessoa" value="<?= $dadosImob['tipo_pessoa'] ?>">

        <!-- Grid para organizar os campos do formulário. -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Campo Nome -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome / Razão Social</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($dadosImob['nome']) ?>" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Campo Tipo de Pessoa (Apenas exibição) -->
            <div>
                <label for="tipo_pessoa" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pessoa</label>
                <select id="tipo_pessoa" class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" disabled>
                    <option value="J" <?= $dadosImob['tipo_pessoa'] == 'J' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                    <option value="F" <?= $dadosImob['tipo_pessoa'] == 'F' ? 'selected' : '' ?>>Pessoa Física</option>
                </select>
            </div>

            <!-- Campo Documento (CPF ou CNPJ) -->
            <div class="md:col-span-2">
                <label id="label_documento" for="documento" class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                <input type="text" name="documento" id="documento" required 
                    onkeypress="apenasNumeros(event)" oninput="formatarDocumento(this)"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        <!-- Botões de ação do formulário. -->
        <div class="mt-6 flex justify-end gap-2">
            <a href="listar_imobiliaria.php" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                <i class="fas fa-times mr-1"></i> Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

<!-- Segundo card: Formulário para adicionar um usuário existente a esta imobiliária. -->
<div class="bg-white p-6 rounded-xl shadow mb-6">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <i class="fas fa-user-plus text-green-600"></i> Adicionar Usuário à Imobiliária
    </h3>
    <?php if (!empty($usuariosDisponiveis)): ?>
        <form action="../../controllers/UsuarioController.php" method="GET" class="grid md:grid-cols-3 gap-4 items-end">
            <input type="hidden" name="idImobiliaria" value="<?= $idImobiliaria ?>">
            <div class="md:col-span-2">
                <label for="selectUsuario" class="block text-sm font-medium text-gray-700 mb-1">Selecione um usuário</label>
                <select id="selectUsuario" name="incluirUsuario" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="" disabled selected>Escolha um usuário...</option>
                    <?php foreach ($usuariosDisponiveis as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
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
        <i class="fas fa-users text-gray-600"></i> Usuários Vinculados
    </h3>
    <?php if (!empty($usuarios)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border">
                <thead class="bg-gray-100 text-sm">
                    <tr>
                        <th class="text-left px-4 py-2 border">ID</th>
                        <th class="text-left px-4 py-2 border">Nome</th>
                        <th class="text-left px-4 py-2 border">Email</th>
                        <th class="text-center px-4 py-2 border">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= $user['id_usuario'] ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($user['nome']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="../usuarios/editar.php?id=<?= $user['id_usuario'] ?>" class="px-2 py-1 text-yellow-600 hover:text-yellow-800" title="Editar Usuário">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="../../controllers/UsuarioController.php?removerImobiliaria=<?= $user['id_usuario'] ?>&idImobiliaria=<?= $idImobiliaria ?>" class="px-2 py-1 text-red-600 hover:text-red-800 btn-remover-usuario" title="Remover da Imobiliária">
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

<!-- Scripts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configura o formulário inicial com os dados do PHP
    configurarFormulario();

    // Adiciona o evento de confirmação para os botões de remover usuário
    const botoesRemover = document.querySelectorAll('.btn-remover-usuario');
    botoesRemover.forEach(function(botao) {
        botao.addEventListener('click', function(event) {
            event.preventDefault();
            const urlParaRemover = this.href;
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja realmente remover este usuário da imobiliária?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlParaRemover;
                }
            });
        });
    });
});

function configurarFormulario() {
    // Pega os dados do PHP embutidos no HTML
    const tipoPessoa = '<?= $dadosImob['tipo_pessoa'] ?>';
    const documento = '<?= $dadosImob['tipo_pessoa'] == 'F' ? htmlspecialchars($dadosImob['cpf']) : htmlspecialchars($dadosImob['cnpj']) ?>';
    
    const label = document.getElementById('label_documento');
    const input = document.getElementById('documento');

    if (tipoPessoa === 'F') {
        label.textContent = 'CPF';
        input.placeholder = '000.000.000-00';
        input.maxLength = 14;
    } else {
        label.textContent = 'CNPJ';
        input.placeholder = '00.000.000/0000-00';
        input.maxLength = 18;
    }
    input.value = documento;
    formatarDocumento(input); // Aplica a máscara inicial
}

function apenasNumeros(event) {
    if (!/\d/.test(event.key)) event.preventDefault();
}

function formatarDocumento(campo) {
    const tipo = '<?= $dadosImob['tipo_pessoa'] ?>';
    let valor = campo.value.replace(/\D/g, '');

    if (tipo === 'F') { // CPF
        valor = valor.slice(0, 11);
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else { // CNPJ
        valor = valor.slice(0, 14);
        valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        valor = valor.replace(/\.(\d{3})(\d)/, '.$1/$2');
        valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
    }
    campo.value = valor;
}

function validarFormulario(event) {
    const tipo = '<?= $dadosImob['tipo_pessoa'] ?>';
    const doc = document.getElementById('documento').value.replace(/\D/g, '');
    let erro = '';

    if (tipo === 'F' && doc.length !== 11) {
        erro = 'O CPF deve conter 11 dígitos.';
    } else if (tipo === 'J' && doc.length !== 14) {
        erro = 'O CNPJ deve conter 14 dígitos.';
    }

    if (erro) {
        event.preventDefault(); // Impede o envio
        Swal.fire({
            icon: 'error',
            title: 'Documento Inválido',
            text: erro
        });
    }
}
</script>