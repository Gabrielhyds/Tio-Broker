<?php
// Verifica se a variável $cliente, que deve ser carregada pelo controller, não existe ou está vazia.
if (!isset($cliente) || empty($cliente)) {
    // Se não houver dados, exibe uma mensagem de erro e interrompe a renderização do formulário.
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg' role='alert'>Não foi possível carregar os dados do cliente.</div>";
    return; // 'return' impede que o restante do arquivo seja executado.
}
?>
<!-- Importa a biblioteca SweetAlert2 para exibir alertas mais bonitos. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Contêiner principal com estilo de cartão, centralizado na página. -->
<div class="container mx-auto max-w-5xl bg-white p-6 sm:p-8 rounded-lg shadow-md mt-6">
    <!-- Título da página de edição. -->
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-pencil-alt text-blue-600"></i> Editar Cliente
    </h2>

    <!-- Bloco PHP para exibir uma mensagem de erro vinda da validação do formulário. -->
    <?php if (isset($_SESSION['mensagem_erro_form'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['mensagem_erro_form']); ?></span>
        </div>
        <?php unset($_SESSION['mensagem_erro_form']); // Limpa a mensagem da sessão. 
        ?>
    <?php endif; ?>

    <!-- Formulário para editar os dados do cliente. -->
    <form method="POST" action="index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="space-y-6" id="edit-cliente-form">
        <!-- Grid para organizar os campos do formulário. -->
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

        <!-- Título da seção de informações financeiras. -->
        <h3 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Informações Financeiras</h3>

        <!-- Grid para os campos financeiros. -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="renda" class="block text-sm font-medium text-gray-700">Renda (R$)</label>
                <!-- ✅ CORREÇÃO: Alterado type para "text" para a máscara funcionar -->
                <input type="text" name="renda" id="renda" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['renda'] ?? '') ?>">
            </div>
            <div>
                <label for="entrada" class="block text-sm font-medium text-gray-700">Entrada (R$)</label>
                <input type="text" name="entrada" id="entrada" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['entrada'] ?? '') ?>">
            </div>
            <div>
                <label for="fgts" class="block text-sm font-medium text-gray-700">FGTS (R$)</label>
                <input type="text" name="fgts" id="fgts" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['fgts'] ?? '') ?>">
            </div>
            <div>
                <label for="subsidio" class="block text-sm font-medium text-gray-700">Subsídio (R$)</label>
                <input type="text" name="subsidio" id="subsidio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['subsidio'] ?? '') ?>">
            </div>
        </div>

        <div>
            <label for="foto" class="block text-sm font-medium text-gray-700">URL da Foto</label>
            <input type="url" name="foto" id="foto" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['foto'] ?? '') ?>">
            <!-- Exibe a foto atual do cliente, se existir. -->
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

        <!-- Botões de ação do formulário. -->
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
<!-- Bloco para exibir um alerta de erro do SweetAlert, se houver uma mensagem de erro na sessão. -->
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

<!-- ✅ SCRIPT CORRIGIDO E MELHORADO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');
        const form = document.getElementById('edit-cliente-form');

        // --- Máscara de CPF ---
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

        // --- Máscara de Telefone ---
        if (telefoneInput) {
            telefoneInput.addEventListener('input', function() {
                let value = telefoneInput.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
                telefoneInput.value = value;
            });
        }

        // --- Máscara de Moeda (DINHEIRO) ---
        const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];

        const formatToCurrency = (value) => {
            // Converte o valor para string e remove não-dígitos
            let digits = String(value).replace(/\D/g, '');
            if (!digits) return '';

            // Converte para número e formata
            const valueAsNumber = parseFloat(digits) / 100;
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valueAsNumber);
        };

        camposDinheiro.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Formata o valor inicial carregado do banco de dados
                input.value = formatToCurrency(input.value);

                // Adiciona o listener para formatar enquanto digita
                input.addEventListener('input', (e) => {
                    e.target.value = formatToCurrency(e.target.value);
                });
            }
        });

        // --- Limpeza do Formulário antes do Envio ---
        if (form) {
            form.addEventListener('submit', () => {
                // Limpa os campos de moeda
                camposDinheiro.forEach(id => {
                    const input = document.getElementById(id);
                    if (input && input.value) {
                        const digits = input.value.replace(/\D/g, '');
                        if (digits) {
                            const valueAsNumber = parseFloat(digits) / 100;
                            input.value = valueAsNumber.toFixed(2);
                        }
                    }
                });
            });
        }
    });
</script>