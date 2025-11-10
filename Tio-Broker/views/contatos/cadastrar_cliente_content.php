<?php
/**
 * @file
 * Este arquivo renderiza o formulário de cadastro de novos clientes.
 * Inclui lógica para repopular o formulário com dados previamente submetidos
 * em caso de erro de validação, além de manipulação de UI via JavaScript
 * para máscaras de entrada, pré-visualização de imagem e internacionalização.
 */

// Garante que a sessão PHP esteja ativa para manipulação de dados e mensagens.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recupera dados do formulário da sessão, se existirem (útil após um redirecionamento por erro de validação).
// O operador '??' (null coalescing) garante que $old_data seja um array vazio se 'form_data' não existir.
$old_data = $_SESSION['form_data'] ?? [];

// Limpa os dados do formulário da sessão para evitar que sejam exibidos em visitas futuras à página.
unset($_SESSION['form_data']);
?>
<!-- Inclusão da biblioteca SweetAlert2 para exibição de alertas modernos e personalizados. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Estilização para o container de upload de imagem e sua pré-visualização. */
    .image-upload-container { border: 2px dashed #cbd5e1; border-radius: 0.5rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out; position: relative; min-height: 150px; }
    .image-upload-container:hover { background-color: #f8fafc; border-color: #3b82f6; }
    .image-preview-wrapper { display: none; position: relative; width: 150px; height: 150px; margin: 0; }
    .image-preview { width: 100%; height: 100%; border-radius: 0.5rem; overflow: hidden; border: 2px solid #e5e7eb; }
    .image-preview img { width: 100%; height: 100%; object-fit: cover; }
    .remove-image-btn { position: absolute; top: -10px; right: -10px; background-color: #ef4444; color: white; border-radius: 50%; width: 28px; height: 28px; border: 2px solid white; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    
    /* Classe para ocultar elementos de texto enquanto a tradução é carregada, evitando "flash" de conteúdo. */
    .translating { visibility: hidden; }
</style>

<h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
    <i class="fas fa-user-plus text-green-600"></i>
    <!-- O texto é preenchido via JavaScript pelo sistema de internacionalização (i18n). -->
    <span class="translating" data-i18n="add.title">Cadastrar Novo Cliente</span>
</h2>



<!-- Formulário de cadastro de cliente. A action aponta para o controller 'cliente' e a action 'cadastrar'. -->
<!-- 'enctype="multipart/form-data"' é essencial para permitir o upload de arquivos (a foto do cliente). -->
<form method="POST" action="index.php?controller=cliente&action=cadastrar" class="space-y-6 bg-white p-6 rounded-lg shadow-md" id="cliente-form" enctype="multipart/form-data">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fullNameLabel">Nome Completo <span class="text-red-500">*</span></label>
            <!-- O valor do campo é preenchido com dados antigos, se existirem, para melhorar a experiência do usuário. -->
            <input type="text" name="nome" id="nome" data-i18n-placeholder="add.fullNamePlaceholder" placeholder="Digite o nome completo"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required
                   value="<?= htmlspecialchars($old_data['nome'] ?? '') ?>">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 translating" data-i18n="add.phoneLabel">Número de Telefone <span class="text-red-500">*</span></label>
            <div class="flex gap-2 mt-1">
                <!-- Seletor de código de país para formatação correta do telefone. -->
                <select id="codigo_pais" class="w-28 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="BR" selected>+55 🇧🇷</option>
                    <option value="US">+1 🇺🇸</option>
                    <option value="PT">+351 🇵🇹</option>
                </select>
                <input type="text" name="numero" id="numero" data-i18n-placeholder="add.phonePlaceholder" placeholder="Digite o telefone"
                       class="flex-1 rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required
                       value="<?= htmlspecialchars($old_data['numero'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="cpf" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.cpfLabel">CPF <span class="text-red-500">*</span></label>
            <input type="text" name="cpf" id="cpf" data-i18n-placeholder="add.cpfPlaceholder" placeholder="000.000.000-00"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500" required
                   value="<?= htmlspecialchars($old_data['cpf'] ?? '') ?>">
        </div>
        <div>
            <label for="empreendimento" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.interestLabel">Empreendimento de Interesse</label>
            <input type="text" name="empreendimento" id="empreendimento" data-i18n-placeholder="add.interestPlaceholder" placeholder="Ex: Residencial Alpha"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($old_data['empreendimento'] ?? '') ?>">
        </div>
    </div>

    <h3 class="text-md font-semibold text-gray-600 mt-6 translating" data-i18n="add.financialTitle">Informações Financeiras (opcional)</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="renda" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.incomeLabel">Renda (R$)</label>
            <input type="text" name="renda" id="renda" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($old_data['renda'] ?? '') ?>">
        </div>
        <div>
            <label for="entrada" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.downPaymentLabel">Entrada (R$)</label>
            <input type="text" name="entrada" id="entrada" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($old_data['entrada'] ?? '') ?>">
        </div>
        <div>
            <label for="fgts" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.fgtsLabel">FGTS (R$)</label>
            <input type="text" name="fgts" id="fgts" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($old_data['fgts'] ?? '') ?>">
        </div>
        <div>
            <label for="subsidio" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.subsidyLabel">Subsídio (R$)</label>
            <input type="text" name="subsidio" id="subsidio" data-i18n-placeholder="add.currencyPlaceholder" placeholder="R$ 0,00"
                   class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($old_data['subsidio'] ?? '') ?>">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 translating" data-i18n="add.photoUrlLabel">Foto do Cliente</label>
        <div class="mt-1">
            <!-- Container para upload de imagem com funcionalidade de arrastar e soltar (drag-and-drop). -->
            <label for="foto_arquivo" id="upload-label" class="image-upload-container flex flex-col justify-center items-center">
                <div id="upload-prompt" class="text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold text-blue-600">Clique para enviar</span> ou arraste e solte
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                </div>
            </label>
            <!-- Wrapper para a pré-visualização da imagem, que aparece após o upload. -->
            <div id="image-preview-wrapper" class="image-preview-wrapper">
                <div class="image-preview">
                    <img id="preview-img" src="#" alt="Pré-visualização da imagem">
                </div>
                <button type="button" id="remove-image" class="remove-image-btn" title="Remover foto">&times;</button>
            </div>
            <!-- Input de arquivo real, que é mantido oculto e acionado pelo label. -->
            <input type="file" name="foto_arquivo" id="foto_arquivo" class="hidden" accept="image/png, image/jpeg, image/gif">
        </div>
    </div>

    <div>
        <label for="tipo_lista" class="block text-sm font-medium text-gray-700 translating" data-i18n="add.classificationLabel">Classificação do Cliente <span class="text-red-500">*</span></label>
        <select name="tipo_lista" id="tipo_lista"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500" required>
            <option disabled selected value="" class="translating" data-i18n="add.selectOption">Selecione uma opção</option>
            <!-- Lógica para manter a opção selecionada anteriormente em caso de erro no formulário. -->
            <option value="Potencial" class="translating" data-i18n="common.potential" <?= (isset($old_data['tipo_lista']) && $old_data['tipo_lista'] === 'Potencial') ? 'selected' : '' ?>>Potencial</option>
            <option value="Não potencial" class="translating" data-i18n="common.notPotential" <?= (isset($old_data['tipo_lista']) && $old_data['tipo_lista'] === 'Não potencial') ? 'selected' : '' ?>>Não potencial</option>
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
    /**
     * Lógica para pré-visualização de imagem.
     */
    const inputArquivo = document.getElementById('foto_arquivo');
    const previewWrapper = document.getElementById('image-preview-wrapper');
    const previewImage = document.getElementById('preview-img');
    const uploadLabel = document.getElementById('upload-label');
    const removeButton = document.getElementById('remove-image');

    // Evento disparado quando um arquivo é selecionado no input.
    inputArquivo.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            // Esconde o prompt de upload e mostra a pré-visualização.
            uploadLabel.style.display = 'none';
            previewWrapper.style.display = 'block';
            
            // Quando o arquivo for lido, define o 'src' da imagem de preview.
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            }
            reader.readAsDataURL(file); // Inicia a leitura do arquivo como Data URL.
        }
    });

    // Evento para o botão de remover a imagem.
    removeButton.addEventListener('click', function() {
        inputArquivo.value = ''; // Limpa o valor do input de arquivo.
        previewImage.src = '#'; // Reseta o 'src' da imagem.
        previewWrapper.style.display = 'none'; // Esconde a pré-visualização.
        uploadLabel.style.display = 'flex'; // Mostra o prompt de upload novamente.
    });

    /**
     * Sistema de Internacionalização (i18n).
     * Carrega e aplica traduções para os elementos da página.
     */
    let translations = {};
    let currentLang = 'pt-br';

    // Função auxiliar para buscar uma tradução aninhada a partir de uma chave (ex: "add.title").
    function t(key, fallback = '') { 
        return key.split('.').reduce((obj, i) => obj && obj[i], translations) || fallback || key; 
    }

    // Aplica as traduções carregadas aos elementos com atributos 'data-i18n'.
    function applyTranslations() {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            // Ignora elementos dentro da sidebar para evitar sobreposição de traduções.
            if (!el.closest('#sidebar')) {
                const key = el.dataset.i18n;
                const translation = t(key);
                if (translation !== key) { el.innerText = translation; }
            }
            el.classList.remove('translating'); // Torna o elemento visível.
        });
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            el.placeholder = t(el.dataset.i18nPlaceholder);
        });
    }

    // Carrega o arquivo JSON de tradução do servidor.
    async function loadContactTranslations(lang) {
        try {
            const response = await fetch(`../../controllers/TraducaoController.php?modulo=contatos&lang=${lang}`);
            const result = await response.json();
            if (result.success) {
                translations = result.data;
                applyTranslations();
            }
        } catch (error) {
            console.error('Falha ao carregar as traduções:', error);
        }
    }

    /**
     * Máscaras de entrada para campos de formulário (CPF e Telefone).
     */
    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('numero');
    const paisSelect = document.getElementById('codigo_pais');

    // Aplica máscara de CPF (000.000.000-00) enquanto o usuário digita.
    if (cpfInput) {
        cpfInput.addEventListener('input', function() {
            let value = cpfInput.value.replace(/\D/g, ''); // Remove tudo que não é dígito.
            if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos.
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfInput.value = value;
        });
    }

    // Formata o número de telefone com base no país selecionado.
    function formatarTelefone() {
        const pais = paisSelect.value;
        let value = telefoneInput.value.replace(/\D/g, '');
        switch (pais) {
            case 'BR': // Formato Brasil: (XX) XXXXX-XXXX
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
                break;
            case 'US': // Formato EUA: (XXX) XXX-XXXX
                if (value.length > 10) value = value.slice(0, 10);
                value = value.replace(/^(\d{3})(\d)/, '($1) $2');
                value = value.replace(/(\d{3})(\d{1,4})$/, '$1-$2');
                break;
        }
        telefoneInput.value = value;
    }

    if (telefoneInput && paisSelect) {
        telefoneInput.addEventListener('input', formatarTelefone);
        // Limpa o campo de telefone ao trocar o país para evitar formatação incorreta.
        paisSelect.addEventListener('change', function() {
            telefoneInput.value = '';
            telefoneInput.focus();
        });
    }

    /**
     * Formatação de campos monetários.
     */
    const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];
    const form = document.getElementById('cliente-form');
    
    // Converte uma string de dígitos para um formato de moeda localizado.
    const formatToCurrency = (digits, lang) => {
        if (!digits) return '';
        const valueAsNumber = parseInt(digits, 10) / 100;
        const locale = lang === 'en' ? 'en-US' : 'pt-BR';
        const currency = lang === 'en' ? 'USD' : 'BRL';
        return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(valueAsNumber);
    };

    // Adiciona o listener de formatação para cada campo monetário.
    camposDinheiro.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', (e) => {
                const currentDigits = e.target.value.replace(/\D/g, '');
                e.target.value = formatToCurrency(currentDigits, currentLang);
            });
        }
    });
    
    // Antes de submeter o formulário, converte os valores monetários formatados
    // de volta para um formato numérico padrão (ex: "1234.56") para ser processado no backend.
    if (form) {
        form.addEventListener('submit', () => {
            camposDinheiro.forEach(id => {
                const input = document.getElementById(id);
                if (input && input.value) {
                    const digits = input.value.replace(/\D/g, '');
                    if (digits) {
                        const valueAsNumber = parseInt(digits, 10) / 100;
                        input.value = valueAsNumber.toFixed(2);
                    }
                }
            });
        });
    }

    /**
     * Função de inicialização do formulário.
     */
    async function initializeForm() {
        // Define o idioma com base na sessão do PHP, localStorage ou padrão 'pt-br'.
        currentLang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
        
        // Carrega as traduções necessárias para a UI.
        await loadContactTranslations(currentLang);
        
        <?php // Exibe um alerta de erro (vindo do controller) usando SweetAlert2, se houver. ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
        Swal.fire({
            icon: 'error',
            title: t('alerts.errorTitle', 'Erro'),
            text: "<?= addslashes($_SESSION['mensagem_erro']) ?>",
            confirmButtonColor: '#3085d6',
            confirmButtonText: t('alerts.okButton', 'OK')
        });
        <?php // Limpa a mensagem da sessão após exibi-la. ?>
        <?php unset($_SESSION['mensagem_erro']); ?>
        <?php endif; ?>
    }
    
    // Inicia a execução das funcionalidades do formulário.
    initializeForm();
});
</script>
