<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
    <i class="bi bi-person-plus-fill text-green-600"></i> Cadastrar Novo Cliente
</h2>

<form method="POST" action="index.php?controller=cliente&action=cadastrar" class="space-y-6 bg-white p-6 rounded-lg shadow-md">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
            <input type="text" name="nome" id="nome" placeholder="Digite o nome completo"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="numero" class="block text-sm font-medium text-gray-700">Número de Telefone <span class="text-red-500">*</span></label>
            <input type="text" name="numero" id="numero" placeholder="(XX) XXXXX-XXXX"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="cpf" class="block text-sm font-medium text-gray-700">CPF <span class="text-red-500">*</span></label>
            <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="empreendimento" class="block text-sm font-medium text-gray-700">Empreendimento de Interesse</label>
            <input type="text" name="empreendimento" id="empreendimento" placeholder="Ex: Residencial Alpha"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <h3 class="text-md font-semibold text-gray-600 mt-6">Informações Financeiras (opcional)</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="renda" class="block text-sm font-medium text-gray-700">Renda (R$)</label>
            <input type="number" step="0.01" name="renda" id="renda" placeholder="0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="entrada" class="block text-sm font-medium text-gray-700">Entrada (R$)</label>
            <input type="number" step="0.01" name="entrada" id="entrada" placeholder="0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="fgts" class="block text-sm font-medium text-gray-700">FGTS (R$)</label>
            <input type="number" step="0.01" name="fgts" id="fgts" placeholder="0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="subsidio" class="block text-sm font-medium text-gray-700">Subsídio (R$)</label>
            <input type="number" step="0.01" name="subsidio" id="subsidio" placeholder="0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <div>
        <label for="foto" class="block text-sm font-medium text-gray-700">URL da Foto do Cliente</label>
        <input type="url" name="foto" id="foto" placeholder="https://exemplo.com/imagem.jpg"
            class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
        <label for="tipo_lista" class="block text-sm font-medium text-gray-700">Classificação do Cliente <span class="text-red-500">*</span></label>
        <select name="tipo_lista" id="tipo_lista"
            class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500" required>
            <option disabled selected>Selecione uma opção</option>
            <option value="Potencial">Potencial</option>
            <option value="Não potencial">Não potencial</option>
        </select>
    </div>

    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
        <a href="index.php?controller=cliente&action=listar" class="text-gray-600 hover:underline">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            <i class="bi bi-check-lg"></i> Cadastrar Cliente
        </button>
    </div>
</form>

<!-- SweetAlert2 - Erros -->
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

<!-- Máscaras JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');

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