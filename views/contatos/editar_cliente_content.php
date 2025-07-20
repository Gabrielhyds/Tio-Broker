<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/contatos/editar_cliente_content.php (VERSÃO FINAL)
|--------------------------------------------------------------------------
*/

// Garante que a sessão seja iniciada para podermos acessar os dados do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a variável $cliente, que deve ser carregada pelo controller, não existe ou está vazia.
if (!isset($cliente) || empty($cliente)) {
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg' role='alert'>Não foi possível carregar os dados do cliente.</div>";
    return; // Impede que o restante do arquivo seja executado.
}
?>
<!-- Importa a biblioteca SweetAlert2 para exibir alertas mais bonitos. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Esconde o texto que está aguardando tradução para evitar o "pisca-pisca" */
    .translating {
        visibility: hidden;
    }
</style>

<div class="container mx-auto max-w-5xl bg-white p-6 sm:p-8 rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-pencil-alt text-blue-600"></i> <span class="translating" data-i18n="edit.title">Editar Cliente</span>
    </h2>

    <?php if (isset($_SESSION['mensagem_erro_form'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['mensagem_erro_form']); ?></span>
        </div>
        <?php unset($_SESSION['mensagem_erro_form']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="space-y-6" id="edit-cliente-form">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fullNameLabel">Nome Completo <span class="text-red-500">*</span></label>
                <input type="text" name="nome" id="nome" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" required>
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.phoneLabel">Número de Telefone <span class="text-red-500">*</span></label>
                <input type="text" name="numero" id="numero" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.cpfLabel">CPF <span class="text-red-500">*</span></label>
                <input type="text" name="cpf" id="cpf" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="000.000.000-00" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" required>
            </div>
            <div>
                <label for="empreendimento" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.interestLabel">Empreendimento</label>
                <input type="text" name="empreendimento" id="empreendimento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['empreendimento'] ?? '') ?>">
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mt-8 mb-4 translating" data-i18n="add.financialTitle">Informações Financeiras</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="renda" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.incomeLabel">Renda (R$)</label>
                <input type="text" name="renda" id="renda" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['renda'] ?? '') ?>">
            </div>
            <div>
                <label for="entrada" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.downPaymentLabel">Entrada (R$)</label>
                <input type="text" name="entrada" id="entrada" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['entrada'] ?? '') ?>">
            </div>
            <div>
                <label for="fgts" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fgtsLabel">FGTS (R$)</label>
                <input type="text" name="fgts" id="fgts" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['fgts'] ?? '') ?>">
            </div>
            <div>
                <label for="subsidio" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.subsidyLabel">Subsídio (R$)</label>
                <input type="text" name="subsidio" id="subsidio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($cliente['subsidio'] ?? '') ?>">
            </div>
        </div>

        <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.photoUrlLabel">URL da Foto</label>
            <input type="url" name="foto" id="foto" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($cliente['foto'] ?? '') ?>">
            <?php if (!empty($cliente['foto'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto atual" class="rounded-md w-24 h-24 object-cover" onerror="this.style.display='none';">
                </div>
            <?php endif; ?>
        </div>

        <div>
            <label for="tipo_lista" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.classificationLabel">Classificação <span class="text-red-500">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                <option value="Potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Potencial' ? 'selected' : '' ?> class="translating" data-i18n="common.potential">Potencial</option>
                <option value="Não potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Não potencial' ? 'selected' : '' ?> class="translating" data-i18n="common.notPotential">Não potencial</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200 translating" data-i18n="common.cancelButton">
                Cancelar
            </a>
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700 translating" data-i18n="edit.submitButton">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let translations = {};
        let currentLang = 'pt-br';

        // --- FUNÇÕES DE TRADUÇÃO ---
        function t(key, fallback = '') {
            return key.split('.').reduce((obj, i) => obj && obj[i], translations) || fallback || key;
        }

        function applyTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                if (!el.closest('#sidebar')) {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        el.innerText = translation;
                    }
                }
                el.classList.remove('translating');
            });
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                el.placeholder = t(el.dataset.i18nPlaceholder);
            });
        }

        async function loadContactTranslations(lang) {
            try {
                const response = await fetch(`../../controllers/TraducaoController.php?modulo=contatos&lang=${lang}`);
                const result = await response.json();
                if (result.success) {
                    translations = result.data;
                    applyTranslations();
                }
            } catch (error) {
                console.error('Failed to load contact translations:', error);
            }
        }

        // --- LÓGICA DO FORMULÁRIO (MÁSCARAS, ETC.) ---
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');
        const form = document.getElementById('edit-cliente-form');

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

        const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];
        const formatToCurrency = (value, lang) => {
            let digits = String(value).replace(/\D/g, '');
            if (!digits) return '';
            const valueAsNumber = parseFloat(digits) / 100;
            const locale = lang === 'en' ? 'en-US' : 'pt-BR';
            const currency = lang === 'en' ? 'USD' : 'BRL';
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currency
            }).format(valueAsNumber);
        };

        camposDinheiro.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Formata o valor inicial que vem do banco de dados
                input.value = formatToCurrency(input.value, currentLang);
                // Adiciona o listener para formatar enquanto o usuário digita
                input.addEventListener('input', (e) => {
                    e.target.value = formatToCurrency(e.target.value, currentLang);
                });
            }
        });

        if (form) {
            form.addEventListener('submit', () => {
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

        // --- INICIALIZAÇÃO ---
        async function initializeForm() {
            currentLang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
            await loadContactTranslations(currentLang);

            // Exibe o SweetAlert de erro, se houver, já traduzido.
            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: t('common.errorTitle', 'Erro'),
                    text: "<?= addslashes($_SESSION['mensagem_erro']) ?>",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: t('common.okButton', 'OK')
                });
                <?php unset($_SESSION['mensagem_erro']); ?>
            <?php endif; ?>
        }

        initializeForm();
    });
</script>