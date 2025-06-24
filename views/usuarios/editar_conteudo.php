<!-- Importa a biblioteca SweetAlert2 para exibir alertas mais bonitos. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Contêiner principal com largura máxima e centralizado. -->
<div class="max-w-4xl mx-auto">
    <!-- Bloco PHP para exibir uma mensagem de sucesso, se houver. -->
    <?php if ($salvoComSucesso): ?>
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-6 border border-green-200">
            <strong>Sucesso!</strong> Usuário atualizado com sucesso.
        </div>
    <?php endif; ?>

    <!-- Cabeçalho da página com título e link para voltar. -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-pen"></i> Editar Usuário
        </h2>
        <a href="listar.php" class="text-sm text-gray-600 hover:text-blue-600">
            <i class="fas fa-arrow-left mr-1"></i> Voltar à Lista
        </a>
    </div>

    <!-- Formulário para editar os dados do usuário. `enctype="multipart/form-data"` é necessário para upload de arquivos. -->
    <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data" onsubmit="validarFormulario(event)" class="bg-white p-6 rounded-lg shadow">
        <!-- Campos ocultos para enviar a ação a ser executada e o ID do usuário. -->
        <input type="hidden" name="action" value="atualizar" />
        <input type="hidden" name="id_usuario" value="<?= $dados['id_usuario'] ?>" />

        <!-- Grid para organizar os campos do formulário. -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                <!-- O valor do campo é preenchido com os dados atuais do usuário. -->
                <input type="text" id="nome" name="nome" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['nome'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['email'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" placeholder="000.000.000-00"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    value="<?= htmlspecialchars($dados['cpf'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" id="telefone" name="telefone" maxlength="15" placeholder="(00) 00000-0000"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    value="<?= htmlspecialchars($dados['telefone'], ENT_QUOTES) ?>" required>
            </div>

            <div>
                <label for="creci" class="block text-sm font-medium text-gray-700">CRECI</label>
                <input type="text" id="creci" name="creci" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?= htmlspecialchars($dados['creci'], ENT_QUOTES) ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Foto Atual</label>
                <!-- Exibe a foto atual do usuário, se existir. -->
                <?php if (!empty($dados['foto'])): ?>
                    <img src="<?= htmlspecialchars($dados['foto'], ENT_QUOTES) ?>" alt="Foto do Usuário" class="w-24 rounded mt-1">
                <?php else: ?>
                    <p class="text-gray-400 italic">Nenhuma foto enviada.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-gray-700">Alterar Foto</label>
                <!-- Campo para fazer upload de uma nova foto. -->
                <input type="file" id="foto" name="foto" class="mt-1 block w-full text-sm text-gray-600">
            </div>

            <div>
                <label for="permissao" class="block text-sm font-medium text-gray-700">Permissão</label>
                <!-- Select para alterar o nível de permissão do usuário. -->
                <select id="permissao" name="permissao" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="Admin" <?= $dados['permissao'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Coordenador" <?= $dados['permissao'] === 'Coordenador' ? 'selected' : '' ?>>Coordenador</option>
                    <option value="Corretor" <?= $dados['permissao'] === 'Corretor' ? 'selected' : '' ?>>Corretor</option>
                </select>
            </div>

            <div>
                <label for="id_imobiliaria" class="block text-sm font-medium text-gray-700">Imobiliária</label>
                <!-- Select para alterar a imobiliária do usuário. -->
                <select name="id_imobiliaria">
                    <!-- Loop PHP para popular o select com as imobiliárias disponíveis. -->
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>" <?= $usuario['id_imobiliaria'] == $imob['id_imobiliaria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Botões de ação do formulário. -->
        <div class="mt-6 flex justify-between">
            <a href="listar.php" class="text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-left mr-1"></i> Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                <i class="fas fa-save mr-1"></i> Salvar
            </button>
        </div>
    </form>
</div>
<!-- Bloco para exibir um alerta de erro do SweetAlert, se houver uma mensagem de erro na sessão. -->
<?php if (isset($_SESSION['mensagem_erro'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: "<?= addslashes($_SESSION['mensagem_erro']) ?>", // `addslashes` evita que aspas na mensagem quebrem o JS.
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php unset($_SESSION['mensagem_erro']); // Limpa a mensagem da sessão após exibi-la. 
    ?>
<?php endif; ?>

<!-- Script para aplicar máscaras de formatação nos campos de CPF e Telefone. -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('telefone');

        // Adiciona um ouvinte de evento para o campo de CPF.
        cpfInput.addEventListener('input', function() {
            let value = cpfInput.value.replace(/\D/g, ''); // Remove tudo que não for dígito.
            if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos.
            // Aplica a máscara de CPF (000.000.000-00) usando expressões regulares.
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfInput.value = value;
        });

        // Adiciona um ouvinte de evento para o campo de telefone.
        telefoneInput.addEventListener('input', function() {
            let value = telefoneInput.value.replace(/\D/g, ''); // Remove tudo que não for dígito.
            if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos.
            // Aplica a máscara de telefone ( (00) 00000-0000 ) usando expressões regulares.
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            telefoneInput.value = value;
        });
    });
</script>