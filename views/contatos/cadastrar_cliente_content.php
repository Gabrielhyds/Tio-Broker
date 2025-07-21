<?php
// Garante que uma sessão PHP seja iniciada.
// É necessário para usar variáveis de sessão, como as de mensagens de erro ou configurações do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- Estilos para o componente de Upload de Imagem --- */

    /* Container principal para a área de upload (a caixa tracejada). */
    .image-upload-container {
        border: 2px dashed #cbd5e1; /* Borda tracejada */
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer; /* Indica que a área é clicável */
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        position: relative;
        min-height: 150px; /* Garante uma altura mínima para o componente */
    }

    /* Efeito visual ao passar o mouse sobre a área de upload. */
    .image-upload-container:hover {
        background-color: #f8fafc;
        border-color: #3b82f6;
    }

    /* Wrapper que contém a imagem de pré-visualização e o botão de remover. */
    .image-preview-wrapper {
        display: none; /* Começa oculto e só aparece após selecionar uma imagem. */
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0; /* Garante que o wrapper não tenha margens indesejadas. */
    }

    /* Estiliza a caixa que contém a imagem pré-visualizada. */
    .image-preview {
        width: 100%;
        height: 100%;
        border-radius: 0.5rem;
        overflow: hidden; /* Garante que a imagem não ultrapasse as bordas arredondadas. */
        border: 2px solid #e5e7eb;
    }

    /* Garante que a imagem preencha o container de preview sem distorcer. */
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Recorta e centraliza a imagem para preencher o espaço. */
    }

    /* Estilo do botão 'X' para remover a imagem selecionada. */
    .remove-image-btn {
        position: absolute; /* Posiciona o botão em relação ao 'image-preview-wrapper'. */
        top: -10px;  /* Levemente acima */
        right: -10px; /* Levemente à direita */
        background-color: #ef4444; /* Cor vermelha para indicar remoção */
        color: white;
        border-radius: 50%; /* Deixa o botão redondo */
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
    
    /* Classe utilitária para ocultar elementos temporariamente durante o carregamento das traduções,
       evitando o "flash" de texto não traduzido. */
    .translating { visibility: hidden; }
</style>

<h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-800">
    <i class="fas fa-user-plus text-green-600"></i>
    <span class="translating" data-i18n="add.title">Cadastrar Novo Cliente</span>
</h2>

<form method="POST" action="index.php?controller=cliente&action=cadastrar" class="space-y-6 bg-white p-6 rounded-lg shadow-md" id="cliente-form" enctype="multipart/form-data">
    
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

    <div>
        <label class="block text-sm font-medium text-gray-700 translating" data-i18n="add.photoUrlLabel">Foto do Cliente</label>
        <div class="mt-1">
            <label for="foto_arquivo" id="upload-label" class="image-upload-container flex flex-col justify-center items-center">
                <div id="upload-prompt" class="text-center">
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
    // Executa o script quando o DOM (a estrutura da página) estiver totalmente carregado.
    document.addEventListener('DOMContentLoaded', function() {

        // --- Lógica para Upload e Preview de Imagem ---
        const inputArquivo = document.getElementById('foto_arquivo');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const previewImage = document.getElementById('preview-img');
        const uploadLabel = document.getElementById('upload-label');
        const removeButton = document.getElementById('remove-image');

        // Monitora mudanças no input de arquivo (quando um arquivo é selecionado).
        inputArquivo.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader(); // Objeto para ler o arquivo.
                
                // Esconde a área de upload e mostra o container da pré-visualização.
                uploadLabel.style.display = 'none';
                previewWrapper.style.display = 'block';

                // Quando o arquivo for lido, define o resultado como a fonte da imagem de preview.
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                reader.readAsDataURL(file); // Inicia a leitura do arquivo como uma URL de dados.
            }
        });

        // Monitora cliques no botão de remover imagem.
        removeButton.addEventListener('click', function() {
            inputArquivo.value = ''; // Limpa o arquivo do input.
            previewImage.src = '#';   // Reseta a imagem de preview.

            // Esconde a pré-visualização e mostra a área de upload novamente.
            previewWrapper.style.display = 'none';
            uploadLabel.style.display = 'flex'; // 'flex' para manter a centralização original.
        });

        // --- Lógica de Tradução (i18n) ---
        let translations = {}; // Objeto que armazenará as traduções carregadas.
        let currentLang = 'pt-br'; // Idioma padrão.

        // Função auxiliar para buscar uma tradução aninhada (ex: 'add.title').
        function t(key, fallback = '') { return key.split('.').reduce((obj, i) => obj && obj[i], translations) || fallback || key; }

        // Aplica as traduções aos elementos HTML marcados com 'data-i18n'.
        function applyTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                if (!el.closest('#sidebar')) { // Ignora a sidebar (se houver)
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) { el.innerText = translation; }
                }
                el.classList.remove('translating'); // Torna o elemento visível após a tradução.
            });
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                el.placeholder = t(el.dataset.i18nPlaceholder);
            });
        }

        // Carrega o arquivo de tradução do servidor de forma assíncrona.
        async function loadContactTranslations(lang) {
            try {
                const response = await fetch(`../../controllers/TraducaoController.php?modulo=contatos&lang=${lang}`);
                const result = await response.json();
                if (result.success) {
                    translations = result.data;
                    applyTranslations(); // Aplica as traduções após o carregamento.
                }
            } catch (error) {
                console.error('Falha ao carregar as traduções:', error);
            }
        }

        // --- Máscaras de Input ---
        const cpfInput = document.getElementById('cpf');
        const telefoneInput = document.getElementById('numero');
        const paisSelect = document.getElementById('codigo_pais');

        // Máscara para CPF (000.000.000-00).
        if (cpfInput) {
            cpfInput.addEventListener('input', function() {
                let value = cpfInput.value.replace(/\D/g, ''); // Remove tudo que não é dígito.
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                cpfInput.value = value;
            });
        }
        
        // Função para aplicar máscara de telefone baseada no país selecionado.
        function formatarTelefone() {
            const pais = paisSelect.value;
            let value = telefoneInput.value.replace(/\D/g, '');
            switch (pais) {
                case 'BR': // (XX) XXXXX-XXXX
                    if (value.length > 11) value = value.slice(0, 11);
                    value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
                    break;
                case 'US': // (XXX) XXX-XXXX
                    if (value.length > 10) value = value.slice(0, 10);
                    value = value.replace(/^(\d{3})(\d)/, '($1) $2');
                    value = value.replace(/(\d{3})(\d{1,4})$/, '$1-$2');
                    break;
            }
            telefoneInput.value = value;
        }

        if (telefoneInput && paisSelect) {
            telefoneInput.addEventListener('input', formatarTelefone);
            // Limpa o campo de telefone ao trocar o país para evitar máscara incorreta.
            paisSelect.addEventListener('change', function() {
                telefoneInput.value = '';
                telefoneInput.focus();
            });
        }

        // --- Formatação de Campos Monetários ---
        const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];
        const form = document.getElementById('cliente-form');

        // Função que converte um valor numérico em string de moeda (ex: 12345 -> R$ 123,45).
        const formatToCurrency = (digits, lang) => {
            if (!digits) return '';
            const valueAsNumber = parseInt(digits, 10) / 100;
            const locale = lang === 'en' ? 'en-US' : 'pt-BR';
            const currency = lang === 'en' ? 'USD' : 'BRL';
            return new Intl.NumberFormat(locale, { style: 'currency', currency: currency }).format(valueAsNumber);
        };

        // Aplica a formatação em tempo real enquanto o usuário digita.
        camposDinheiro.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', (e) => {
                    const currentDigits = e.target.value.replace(/\D/g, '');
                    e.target.value = formatToCurrency(currentDigits, currentLang);
                });
            }
        });

        // Antes de enviar o formulário, converte os valores monetários de volta para um formato numérico
        // que o backend (PHP) possa processar facilmente (ex: "R$ 1.234,56" -> "1234.56").
        if (form) {
            form.addEventListener('submit', () => {
                camposDinheiro.forEach(id => {
                    const input = document.getElementById(id);
                    if (input && input.value) {
                        const digits = input.value.replace(/\D/g, '');
                        if (digits) {
                            const valueAsNumber = parseInt(digits, 10) / 100;
                            input.value = valueAsNumber.toFixed(2); // Envia como "1234.56"
                        }
                    }
                });
            });
        }

        // --- Inicialização do Formulário ---
        async function initializeForm() {
            // Define o idioma com base na sessão PHP, localStorage ou padrão 'pt-br'.
            currentLang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
            
            // Carrega as traduções para o idioma definido.
            await loadContactTranslations(currentLang);
            
            // Verifica se existe uma mensagem de erro na sessão (vinda do PHP após um POST falho).
            <?php if (isset($_SESSION['mensagem_erro'])): ?>
            // Se houver, exibe um alerta com SweetAlert2.
            Swal.fire({
                icon: 'error',
                title: t('alerts.errorTitle', 'Erro'), // Título traduzido
                text: "<?= addslashes($_SESSION['mensagem_erro']) ?>",
                confirmButtonColor: '#3085d6',
                confirmButtonText: t('alerts.okButton', 'OK') // Botão traduzido
            });
            <?php unset($_SESSION['mensagem_erro']); // Limpa a mensagem da sessão para não mostrá-la novamente. ?>
            <?php endif; ?>
        }
        
        // Chama a função principal para configurar o formulário.
        initializeForm();
    });
</script>