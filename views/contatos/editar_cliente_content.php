<?php
if (!isset($cliente) || empty($cliente)) {
    echo "<div class='alert alert-danger'>Não foi possível carregar os dados do cliente.</div>";
    return;
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container mx-auto max-w-5xl bg-white p-8 rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-pencil-alt text-blue-600"></i> Editar Cliente
    </h2>

    <?php if (isset($_SESSION['mensagem_erro_form'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['mensagem_erro_form']); ?></span>
        </div>
        <?php unset($_SESSION['mensagem_erro_form']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                <input type="text" name="nome" id="nome" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" required>
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-700">Número de Telefone <span class="text-red-500">*</span></label>
                <input type="text" name="numero" id="numero" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF <span class="text-red-500">*</span></label>
                <input type="text" name="cpf" id="cpf" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="000.000.000-00" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" required>
            </div>
            <div>
                <label for="empreendimento" class="block text-sm font-medium text-gray-700">Empreendimento</label>
                <input type="text" name="empreendimento" id="empreendimento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['empreendimento'] ?? '') ?>">
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Informações Financeiras</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="renda" class="block text-sm font-medium text-gray-700">Renda (R$)</label>
                <input type="number" step="0.01" name="renda" id="renda" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['renda'] ?? '') ?>">
            </div>
            <div>
                <label for="entrada" class="block text-sm font-medium text-gray-700">Entrada (R$)</label>
                <input type="number" step="0.01" name="entrada" id="entrada" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['entrada'] ?? '') ?>">
            </div>
            <div>
                <label for="fgts" class="block text-sm font-medium text-gray-700">FGTS (R$)</label>
                <input type="number" step="0.01" name="fgts" id="fgts" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['fgts'] ?? '') ?>">
            </div>
            <div>
                <label for="subsidio" class="block text-sm font-medium text-gray-700">Subsídio (R$)</label>
                <input type="number" step="0.01" name="subsidio" id="subsidio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['subsidio'] ?? '') ?>">
            </div>
        </div>

        <div>
            <label for="foto" class="block text-sm font-medium text-gray-700">URL da Foto</label>
            <input type="url" name="foto" id="foto" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['foto'] ?? '') ?>">
            <?php if (!empty($cliente['foto'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto atual" class="rounded-md w-24 h-24 object-cover" onerror="this.style.display='none';">
                </div>
            <?php endif; ?>
        </div>

        <div>
            <label for="tipo_lista" class="block text-sm font-medium text-gray-700">Classificação <span class="text-red-500">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                <option value="Potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Potencial' ? 'selected' : '' ?>>Potencial</option>
                <option value="Não potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Não potencial' ? 'selected' : '' ?>>Não potencial</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">
                Cancelar
            </a>
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
<?php if (isset($_SESSION['mensagem_erro'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');

        if (cpfInput) {
            cpfInput.addEventListener('input', function() {
                let value = cpfInput.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                cpfInput.value = value;
            });
        }

        if (telefoneInput) {
            telefoneInput.addEventListener('input', function() {
                let value = telefoneInput.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                telefoneInput.value = value;
            });
        }
    });
</script>