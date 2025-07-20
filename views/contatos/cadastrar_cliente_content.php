<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .image-upload-container {
        border: 2px dashed #cbd5e1;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        position: relative;
    }
    .image-upload-container:hover {
        background-color: #f8fafc;
        border-color: #3b82f6;
    }
    .image-preview-wrapper {
        display: none; /* Começa escondido */
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    .image-preview {
        width: 100%;
        height: 100%;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .remove-image-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #ef4444;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .translating { visibility: hidden; }
</style>

<h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
    <i class="fas fa-user-plus text-green-600"></i> <span class="translating" data-i18n="add.title">Cadastrar Novo Cliente</span>
</h2>

<form method="POST" action="index.php?controller=cliente&action=cadastrar" class="space-y-6 bg-white p-6 rounded-lg shadow-md" id="cliente-form" enctype="multipart/form-data">
    <!-- Campos de nome, telefone, cpf, etc. (sem alterações) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fullNameLabel">Nome Completo <span class="text-red-500">*</span></label>
            <input type="text" name="nome" id="nome" data-i18n-placeholder="add.fullNamePlaceholder" placeholder="Digite o nome completo"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 translating" data-i18n="add.phoneLabel">Número de Telefone <span class="text-red-500">*</span></label>
            <div class="flex gap-2 mt-1">
                <select id="codigo_pais" class="w-28 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="BR" selected>+55 🇧🇷</option>
                    <option value="US">+1 🇺🇸</option>
                    <option value="PT">+351 🇵🇹</option>
                </select>
                <input type="text" name="numero" id="numero" data-i18n-placeholder="add.phonePlaceholder" placeholder="Digite o telefone"
                    class="flex-1 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="cpf" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.cpfLabel">CPF <span class="text-red-500">*</span></label>
            <input type="text" name="cpf" id="cpf" data-i18n-placeholder="add.cpfPlaceholder" placeholder="000.000.000-00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="empreendimento" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.interestLabel">Empreendimento de Interesse</label>
            <input type="text" name="empreendimento" id="empreendimento" data-i18n-placeholder="add.interestPlaceholder" placeholder="Ex: Residencial Alpha"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <h3 class="text-md font-semibold text-gray-600 mt-6 translating" data-i18n="add.financialTitle">Informações Financeiras (opcional)</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Campos financeiros (sem alterações) -->
        <div>
            <label for="renda" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.incomeLabel">Renda (R$)</label>
            <input type="text" name="renda" id="renda" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="entrada" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.downPaymentLabel">Entrada (R$)</label>
            <input type="text" name="entrada" id="entrada" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="fgts" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fgtsLabel">FGTS (R$)</label>
            <input type="text" name="fgts" id="fgts" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="subsidio" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.subsidyLabel">Subsídio (R$)</label>
            <input type="text" name="subsidio" id="subsidio" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <!-- Upload de Foto Melhorado -->
    <div>
        <label class="block text-sm font-medium text-gray-700 translating" data-i18n="add.photoUrlLabel">Foto do Cliente</label>
        <div class="mt-1">
            <label for="foto_arquivo" id="upload-label" class="image-upload-container">
                <div id="upload-prompt">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold text-blue-600">Clique para enviar</span> ou arraste e solte
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                </div>
            </label>
            <div id="image-preview-wrapper" class="image-preview-wrapper">
                <div class="image-preview">
                    <img id="preview-img" src="#" alt="Pré-visualização da imagem">
                </div>
                <button type="button" id="remove-image" class="remove-image-btn" title="Remover foto">&times;</button>
            </div>
            <input type="file" name="foto_arquivo" id="foto_arquivo" class="hidden" accept="image/png, image/jpeg, image/gif">
        </div>
    </div>

    <!-- Classificação e botões (sem alterações) -->
    <div>
        <label for="tipo_lista" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.classificationLabel">Classificação do Cliente <span class="text-red-500">*</span></label>
        <select name="tipo_lista" id="tipo_lista"
            class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500" required>
            <option disabled selected value="" class="translating" data-i18n="add.selectOption">Selecione uma opção</option>
            <option value="Potencial" class="translating" data-i18n="common.potential">Potencial</option>
            <option value="Não potencial" class="translating" data-i18n="common.notPotential">Não potencial</option>
        </select>
    </div>
    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
        <a href="index.php?controller=cliente&action=listar" class="text-gray-600 hover:underline translating" data-i18n="common.cancelButton">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow translating" data-i18n="add.submitButton">
            <i class="fas fa-check"></i> Cadastrar Cliente
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (seu código de tradução e máscaras permanece aqui)

        // Script para o upload de imagem
        const inputArquivo = document.getElementById('foto_arquivo');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const previewImage = document.getElementById('preview-img');
        const uploadLabel = document.getElementById('upload-label');
        const removeButton = document.getElementById('remove-image');

        inputArquivo.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                uploadLabel.style.display = 'none';
                previewWrapper.style.display = 'block';
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        removeButton.addEventListener('click', function() {
            inputArquivo.value = ''; // Limpa o arquivo selecionado
            previewImage.src = '#';
            previewWrapper.style.display = 'none';
            uploadLabel.style.display = 'block';
        });

        // O resto do seu script...
        let translations = {};
        let currentLang = 'pt-br';
        function t(key, fallback = '') { return key.split('.').reduce((obj, i) => obj && obj[i], translations) || fallback || key; }
        function applyTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => { if (!el.closest('#sidebar')) { const key = el.dataset.i18n; const translation = t(key); if (translation !== key) { el.innerText = translation; } } el.classList.remove('translating'); });
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.dataset.i18nPlaceholder); });
        }
        async function loadContactTranslations(lang) { try { const response = await fetch(`../../controllers/TraducaoController.php?modulo=contatos&lang=${lang}`); const result = await response.json(); if (result.success) { translations = result.data; applyTranslations(); } } catch (error) { console.error('Failed to load contact translations:', error); } }
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');
        const paisSelect = document.getElementById('codigo_pais');
        const form = document.getElementById('cliente-form');
        if (cpfInput) { cpfInput.addEventListener('input', function() { let value = cpfInput.value.replace(/\D/g, ''); if (value.length > 11) value = value.slice(0, 11); value = value.replace(/(\d{3})(\d)/, '$1.$2'); value = value.replace(/(\d{3})(\d)/, '$1.$2'); value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2'); cpfInput.value = value; }); }
        function formatarTelefone() { const pais = paisSelect.value; let value = telefoneInput.value.replace(/\D/g, ''); switch (pais) { case 'BR': if (value.length > 11) value = value.slice(0, 11); value = value.replace(/^(\d{2})(\d)/, '($1) $2'); value = value.replace(/(\d{5})(\d{4})$/, '$1-$2'); break; case 'US': if (value.length > 10) value = value.slice(0, 10); value = value.replace(/^(\d{3})(\d)/, '($1) $2'); value = value.replace(/(\d{3})(\d{1,4})$/, '$1-$2'); break; } telefoneInput.value = value; }
        if (telefoneInput && paisSelect) { telefoneInput.addEventListener('input', formatarTelefone); paisSelect.addEventListener('change', function() { telefoneInput.value = ''; telefoneInput.focus(); }); }
        const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];
        const formatToCurrency = (digits, lang) => { if (!digits) return ''; const valueAsNumber = parseInt(digits, 10) / 100; const locale = lang === 'en' ? 'en-US' : 'pt-BR'; const currency = lang === 'en' ? 'USD' : 'BRL'; return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(valueAsNumber); };
        camposDinheiro.forEach(id => { const input = document.getElementById(id); if (input) { input.addEventListener('input', (e) => { const currentDigits = e.target.value.replace(/\D/g, ''); e.target.value = formatToCurrency(currentDigits, currentLang); }); } });
        if (form) { form.addEventListener('submit', () => { camposDinheiro.forEach(id => { const input = document.getElementById(id); if (input && input.value) { const digits = input.value.replace(/\D/g, ''); if (digits) { const valueAsNumber = parseInt(digits, 10) / 100; input.value = valueAsNumber.toFixed(2); } } }); }); }
        async function initializeForm() { currentLang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br'; await loadContactTranslations(currentLang); <?php if (isset($_SESSION['mensagem_erro'])): ?> Swal.fire({ icon: 'error', title: t('alerts.errorTitle', 'Erro'), text: "<?= addslashes($_SESSION['mensagem_erro']) ?>", confirmButtonColor: '#3085d6', confirmButtonText: t('alerts.okButton', 'OK') }); <?php unset($_SESSION['mensagem_erro']); ?> <?php endif; ?> }
        initializeForm();
    });
</script>
