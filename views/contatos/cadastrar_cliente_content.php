<!-- Importa a biblioteca SweetAlert2 para exibir alertas mais bonitos. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Título da página de cadastro. -->
<h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
    <i class="bi bi-person-plus-fill text-green-600"></i> Cadastrar Novo Cliente
</h2>

<!-- Formulário para cadastrar um novo cliente. -->
<form method="POST" action="index.php?controller=cliente&action=cadastrar" class="space-y-6 bg-white p-6 rounded-lg shadow-md">

    <!-- Grid para organizar os campos do formulário em colunas. -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
            <input type="text" name="nome" id="nome" placeholder="Digite o nome completo"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <!-- Campo de Telefone com seletor de DDI. -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Número de Telefone <span class="text-red-500">*</span></label>
            <div class="flex gap-2 mt-1">
                <!-- Seletor para o código do país (DDI). -->
                <select id="codigo_pais" class="w-28 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="BR" selected>+55 🇧🇷</option>
                    <option value="US">+1 🇺🇸</option>
                    <option value="PT">+351 🇵🇹</option>
                    <option value="AR">+54 🇦🇷</option>
                    <option value="DE">+49 🇩🇪</option>
                    <option value="FR">+33 🇫🇷</option>
                    <option value="IT">+39 🇮🇹</option>
                    <option value="JP">+81 🇯🇵</option>
                    <option value="UK">+44 🇬🇧</option>
                    <option value="IN">+91 🇮🇳</option>
                    <!-- Adicione mais países conforme necessário. -->
                </select>
                <!-- Campo para o número de telefone, que será formatado pelo JavaScript. -->
                <input type="text" name="numero" id="numero" placeholder="Digite o telefone"
                    class="flex-1 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
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

    <!-- Título da seção de informações financeiras. -->
    <h3 class="text-md font-semibold text-gray-600 mt-6">Informações Financeiras (opcional)</h3>

    <!-- Grid para os campos financeiros. -->
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

    <!-- Botões de ação do formulário. -->
    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
        <a href="index.php?controller=cliente&action=listar" class="text-gray-600 hover:underline">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            <i class="bi bi-check-lg"></i> Cadastrar Cliente
        </button>
    </div>
</form>

<!-- Script para exibir um alerta de erro do SweetAlert, se houver uma mensagem de erro na sessão. -->
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

<!-- Script para aplicar máscaras de formatação nos campos. -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');
        const paisSelect = document.getElementById('codigo_pais');

        if (cpfInput) {
            cpfInput.addEventListener('input', function() {
                let value = cpfInput.value.replace(/\D/g, ''); // Remove não-dígitos.
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                cpfInput.value = value;
            });
        }

        // Função para aplicar a máscara de telefone correta com base no país selecionado.
        function aplicarMascaraTelefone(pais) {
            // Remove ouvintes de evento antigos para evitar múltiplas formatações.
            const novoTelefoneInput = telefoneInput.cloneNode(true);
            telefoneInput.parentNode.replaceChild(novoTelefoneInput, telefoneInput);

            novoTelefoneInput.addEventListener('input', function formatarNumero() {
                let value = novoTelefoneInput.value.replace(/\D/g, '');

                switch (pais) {
                    case 'BR': // Brasil: (XX) XXXXX-XXXX
                        if (value.length > 11) value = value.slice(0, 11);
                        value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                        value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                        break;
                    case 'US': // EUA: (XXX) XXX-XXXX
                        if (value.length > 10) value = value.slice(0, 10);
                        value = value.replace(/^(\d{3})(\d)/, '($1) $2');
                        value = value.replace(/(\d{3})(\d{1,4})$/, '$1-$2');
                        break;
                    case 'PT': // Portugal: XXX XXX XXX
                        value = value.slice(0, 9);
                        value = value.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
                        break;
                        // Adicione outras máscaras aqui...
                    default:
                        break; // Nenhuma máscara para outros países.
                }

                novoTelefoneInput.value = value;
            });
        }

        // Aplica a máscara inicial com base no valor padrão do select.
        aplicarMascaraTelefone(paisSelect.value);

        // Adiciona um ouvinte para mudar a máscara quando o país for alterado.
        paisSelect.addEventListener('change', function() {
            document.getElementById('numero').value = ''; // Limpa o campo de telefone.
            aplicarMascaraTelefone(this.value);
        });
    });
</script>