<?php
// --- Verificação Inicial de Dados ---
// Garante que a variável $cliente, vinda do controller, existe e não está vazia.
// Se não houver dados do cliente, exibe uma mensagem de erro e interrompe a renderização da página.
if (!isset($cliente) || empty($cliente)) {
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg' role='alert'>Não foi possível carregar os dados do cliente.</div>";
    return;
}
?>
<!-- Inclusão da biblioteca SweetAlert2 para criar alertas e modais mais bonitos e interativos. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- Estilos para o componente de Upload de Imagem --- */

    /* Container principal para a área de upload (a caixa tracejada). */
    .image-upload-container {
        border: 2px dashed #cbd5e1;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        position: relative;
    }

    /* Efeito visual ao passar o mouse sobre a área de upload. */
    .image-upload-container:hover {
        background-color: #f8fafc;
        border-color: #3b82f6;
    }

    /* Wrapper que contém a imagem de pré-visualização e o botão de remover. */
    .image-preview-wrapper {
        display: none; /* Começa oculto e só aparece se houver imagem. */
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0; /* ALTERAÇÃO: Alinha o wrapper da imagem à esquerda (antes era '0 auto'). */
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
        top: -10px;
        right: -10px;
        background-color: #ef4444; /* Cor vermelha para indicar remoção */
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
</style>

<div class="container mx-auto max-w-5xl bg-white p-6 sm:p-8 rounded-lg shadow-md mt-6">
    <!-- Título da Página -->
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fas fa-pencil-alt text-blue-600"></i> Editar Cliente
    </h2>

    <?php // --- Exibição de Erros de Validação ---
    // Verifica se existe uma mensagem de erro na sessão (definida pelo controller após uma tentativa de submissão falha).
    if (isset($_SESSION['mensagem_erro_form'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['mensagem_erro_form']); ?></span>
        </div>
        <?php // Limpa a mensagem da sessão para que não seja exibida novamente.
        unset($_SESSION['mensagem_erro_form']); ?>
    <?php endif; ?>

    <!-- Formulário de edição de cliente.
         - action: URL para onde os dados serão enviados. Inclui o ID do cliente.
         - enctype="multipart/form-data": Essencial para permitir o envio de arquivos (a foto). -->
    <form method="POST" action="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="space-y-6" id="edit-cliente-form" enctype="multipart/form-data">
        
        <!-- Seção de Dados Pessoais -->
        <!-- Os valores dos campos são preenchidos com os dados existentes do cliente.
             htmlspecialchars() é usado para prevenir ataques XSS, garantindo que os dados sejam exibidos como texto puro. -->
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
        
        <!-- Seção de Informações Financeiras -->
        <h3 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Informações Financeiras</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="renda" class="block text-sm font-medium text-gray-700">Renda (R$)</label>
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

        <!-- Seção de Upload de Foto -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Foto do Cliente</label>
            <div class="mt-1">
                <label for="foto_arquivo" id="upload-label" class="image-upload-container">
                    <div id="upload-prompt">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                        <p class="mt-2 text-sm text-gray-600"><span class="font-semibold text-blue-600">Clique para enviar uma nova foto</span> ou arraste e solte</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF até 2MB</p>
                    </div>
                </label>
                <div id="image-preview-wrapper" class="image-preview-wrapper">
                    <div class="image-preview">
                        <!-- Exibe a foto existente do cliente, se houver. -->
                        <img id="preview-img" src="<?= !empty($cliente['foto']) ? BASE_URL . htmlspecialchars($cliente['foto']) : '#' ?>" alt="Pré-visualização da imagem">
                    </div>
                    <button type="button" id="remove-image" class="remove-image-btn" title="Remover foto">&times;</button>
                </div>
                <!-- Input de arquivo real, fica oculto. -->
                <input type="file" name="foto_arquivo" id="foto_arquivo" class="hidden" accept="image/png, image/jpeg, image/gif">
                <!-- Input oculto para sinalizar ao backend se a foto existente deve ser removida. -->
                <input type="hidden" name="remover_foto_existente" id="remover_foto_existente" value="0">
            </div>
        </div>

        <!-- Seção de Classificação e Botões de Ação -->
        <div>
            <label for="tipo_lista" class="block text-sm font-medium text-gray-700">Classificação <span class="text-red-500">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                <!-- O operador ternário verifica o valor atual do cliente e adiciona o atributo 'selected' à opção correspondente. -->
                <option value="Potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Potencial' ? 'selected' : '' ?>>Potencial</option>
                <option value="Não potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Não potencial' ? 'selected' : '' ?>>Não potencial</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">Cancelar</a>
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700">Salvar Alterações</button>
        </div>
    </form>
</div>

<script>
// Executa o script quando o DOM estiver totalmente carregado.
document.addEventListener('DOMContentLoaded', function() {
    // --- Lógica para Upload e Preview de Imagem ---
    const inputArquivo = document.getElementById('foto_arquivo');
    const previewWrapper = document.getElementById('image-preview-wrapper');
    const previewImage = document.getElementById('preview-img');
    const uploadLabel = document.getElementById('upload-label');
    const removeButton = document.getElementById('remove-image');
    const removerFotoExistenteInput = document.getElementById('remover_foto_existente');
    // Verifica se o cliente já possui uma foto (valor injetado pelo PHP).
    const fotoExistente = <?= !empty($cliente['foto']) ? 'true' : 'false' ?>;

    // Ao carregar a página, exibe a foto existente ou a área de upload.
    if (fotoExistente) {
        uploadLabel.style.display = 'none';
        previewWrapper.style.display = 'block';
    } else {
        uploadLabel.style.display = 'block';
        previewWrapper.style.display = 'none';
    }

    // Monitora mudanças no input de arquivo (quando um novo arquivo é selecionado).
    inputArquivo.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            uploadLabel.style.display = 'none';
            previewWrapper.style.display = 'block';
            // Se um novo arquivo é selecionado, não queremos remover a foto antiga (ela será substituída).
            removerFotoExistenteInput.value = '0'; 
            reader.onload = (e) => { previewImage.src = e.target.result; }
            reader.readAsDataURL(file);
        }
    });

    // Monitora cliques no botão de remover imagem.
    removeButton.addEventListener('click', function() {
        inputArquivo.value = ''; // Limpa o input de arquivo.
        previewImage.src = '#';
        previewWrapper.style.display = 'none';
        uploadLabel.style.display = 'block';
        // Define o valor do input oculto como '1' para sinalizar ao backend que a foto deve ser removida.
        removerFotoExistenteInput.value = '1';
    });

    // --- Máscaras de Input e Formatação ---
    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('numero');
    const form = document.getElementById('edit-cliente-form');
    const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];

    // Máscara para CPF (000.000.000-00).
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

    // Máscara para Telefone ( (XX) XXXXX-XXXX ).
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function() {
            let value = telefoneInput.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/^(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
            telefoneInput.value = value;
        });
    }
    
    // Função que converte um valor numérico em string de moeda (ex: 1234.56 -> R$ 1.234,56).
    const formatToCurrency = (value) => {
        let digits = String(value).replace(/\D/g, '');
        if (!digits) return '';
        const valueAsNumber = parseFloat(digits) / 100;
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valueAsNumber);
    };

    // Aplica a formatação de moeda aos campos financeiros.
    camposDinheiro.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            // Formata o valor inicial que vem do banco de dados.
            if(input.value) {
                input.value = formatToCurrency(input.value);
            }
            // Adiciona o listener para formatar enquanto o usuário digita.
            input.addEventListener('input', (e) => {
                e.target.value = formatToCurrency(e.target.value);
            });
        }
    });

    // Antes de enviar o formulário, remove a máscara dos campos de moeda.
    if (form) {
        form.addEventListener('submit', () => {
            camposDinheiro.forEach(id => {
                const input = document.getElementById(id);
                if (input && input.value) {
                    // Converte "R$ 1.234,56" para "1234.56" para ser salvo no banco.
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