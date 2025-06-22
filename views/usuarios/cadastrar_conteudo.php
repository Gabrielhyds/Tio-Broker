<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
        <i class="fas fa-user-plus text-blue-500"></i> Cadastrar Novo Usuário
    </h2>

    <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="cadastrar">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                <input type="text" name="nome" id="nome" placeholder="Digite o nome completo"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" placeholder="exemplo@email.com"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Crie uma senha segura"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="creci" class="block text-sm font-medium text-gray-700">CRECI</label>
                <input type="text" name="creci" id="creci" placeholder="Número CRECI (opcional)"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700">Foto (opcional)</label>
                <input type="file" name="foto" id="foto"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="permissao" class="block text-sm font-medium text-gray-700">Permissão</label>
                <select name="permissao" id="permissao"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
                    <option disabled selected>Selecione uma permissão</option>
                    <option value="Admin">Admin</option>
                    <option value="Coordenador">Coordenador</option>
                    <option value="Corretor">Corretor</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="id_imobiliaria" class="block text-sm font-medium text-gray-700">Imobiliária</label>
                <select name="id_imobiliaria" id="id_imobiliaria"
                    class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
                    <option disabled selected>Selecione uma imobiliária</option>
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>">
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="listar.php"
                class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <button type="submit"
                class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
                <i class="fas fa-check mr-2"></i> Cadastrar
            </button>
        </div>
    </form>
</div>

<!-- Erro com SweetAlert -->
<?php if (isset($_SESSION['mensagem_erro'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: "<?= addslashes($_SESSION['mensagem_erro']) ?>",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['mensagem_erro']); ?>
<?php endif; ?>

<!-- Máscaras para CPF e Telefone -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('telefone');

        cpfInput.addEventListener('input', function() {
            let value = cpfInput.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfInput.value = value;
        });

        telefoneInput.addEventListener('input', function() {
            let value = telefoneInput.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            telefoneInput.value = value;
        });
    });
</script>