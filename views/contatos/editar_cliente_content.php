<?php
if (!isset($cliente) || empty($cliente)) {
    echo "<div class='p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg' role='alert'>Não foi possível carregar os dados do cliente.</div>";
    return;
}

// ALTERAÇÃO: Pega os dados antigos do formulário da sessão, se existirem (após um erro de validação).
$old_data = $_SESSION['form_data'] ?? [];
// ALTERAÇÃO: Limpa os dados da sessão para não repopular o formulário em futuras visitas.
unset($_SESSION['form_data']);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Estilos permanecem os mesmos */
    .image-upload-container { border: 2px dashed #cbd5e1; border-radius: 0.5rem; padding: 1.5rem; text-align: center; cursor: pointer; transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out; position: relative; }
    .image-upload-container:hover { background-color: #f8fafc; border-color: #3b82f6; }
    .image-preview-wrapper { display: none; position: relative; width: 150px; height: 150px; margin: 0; }
    .image-preview { width: 100%; height: 100%; border-radius: 0.5rem; overflow: hidden; border: 2px solid #e5e7eb; }
    .image-preview img { width: 100%; height: 100%; object-fit: cover; }
    .remove-image-btn { position: absolute; top: -10px; right: -10px; background-color: #ef4444; color: white; border-radius: 50%; width: 28px; height: 28px; border: 2px solid white; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
</style>

<div class="container mx-auto max-w-5xl bg-white p-6 sm:p-8 rounded-lg shadow-md mt-6">
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

    <form method="POST" action="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="space-y-6" id="edit-cliente-form" enctype="multipart/form-data">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                <!-- ALTERAÇÃO: Prioriza os dados da sessão; se não houver, usa os dados do banco. -->
                <input type="text" name="nome" id="nome" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($old_data['nome'] ?? $cliente['nome'] ?? '') ?>" required>
            </div>
            <div>
                <label for="numero" class="block text-sm font-medium text-gray-700">Número de Telefone <span class="text-red-500">*</span></label>
                <input type="text" name="numero" id="numero" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($old_data['numero'] ?? $cliente['numero'] ?? '') ?>" required>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700">CPF <span class="text-red-500">*</span></label>
                <input type="text" name="cpf" id="cpf" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="000.000.000-00" value="<?= htmlspecialchars($old_data['cpf'] ?? $cliente['cpf'] ?? '') ?>" required>
            </div>
            <div>
                <label for="empreendimento" class="block text-sm font-medium text-gray-700">Empreendimento</label>
                <input type="text" name="empreendimento" id="empreendimento" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($old_data['empreendimento'] ?? $cliente['empreendimento'] ?? '') ?>">
            </div>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-800 mt-8 mb-4">Informações Financeiras</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="renda" class="block text-sm font-medium text-gray-700">Renda (R$)</label>
                <input type="text" name="renda" id="renda" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($old_data['renda'] ?? $cliente['renda'] ?? '') ?>">
            </div>
            <div>
                <label for="entrada" class="block text-sm font-medium text-gray-700">Entrada (R$)</label>
                <input type="text" name="entrada" id="entrada" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($old_data['entrada'] ?? $cliente['entrada'] ?? '') ?>">
            </div>
            <div>
                <label for="fgts" class="block text-sm font-medium text-gray-700">FGTS (R$)</label>
                <input type="text" name="fgts" id="fgts" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($old_data['fgts'] ?? $cliente['fgts'] ?? '') ?>">
            </div>
            <div>
                <label for="subsidio" class="block text-sm font-medium text-gray-700">Subsídio (R$)</label>
                <input type="text" name="subsidio" id="subsidio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="R$ 0,00" value="<?= htmlspecialchars($old_data['subsidio'] ?? $cliente['subsidio'] ?? '') ?>">
            </div>
        </div>

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
                        <img id="preview-img" src="<?= !empty($cliente['foto']) ? BASE_URL . htmlspecialchars($cliente['foto']) : '#' ?>" alt="Pré-visualização da imagem">
                    </div>
                    <button type="button" id="remove-image" class="remove-image-btn" title="Remover foto">&times;</button>
                </div>
                <input type="file" name="foto_arquivo" id="foto_arquivo" class="hidden" accept="image/png, image/jpeg, image/gif">
                <input type="hidden" name="remover_foto_existente" id="remover_foto_existente" value="0">
            </div>
        </div>

        <div>
            <label for="tipo_lista" class="block text-sm font-medium text-gray-700">Classificação <span class="text-red-500">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                <!-- ALTERAÇÃO: Define qual valor deve ser selecionado -->
                <?php $selectedValue = $old_data['tipo_lista'] ?? $cliente['tipo_lista'] ?? ''; ?>
                <option value="Potencial" <?= ($selectedValue === 'Potencial') ? 'selected' : '' ?>>Potencial</option>
                <option value="Não potencial" <?= ($selectedValue === 'Não potencial') ? 'selected' : '' ?>>Não potencial</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200">Cancelar</a>
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700">Salvar Alterações</button>
        </div>
    </form>
</div>

<script>
// O JavaScript permanece o mesmo, pois a lógica de repopular é feita no PHP.
document.addEventListener('DOMContentLoaded', function() {
    const inputArquivo = document.getElementById('foto_arquivo');
    const previewWrapper = document.getElementById('image-preview-wrapper');
    const previewImage = document.getElementById('preview-img');
    const uploadLabel = document.getElementById('upload-label');
    const removeButton = document.getElementById('remove-image');
    const removerFotoExistenteInput = document.getElementById('remover_foto_existente');
    const fotoExistente = <?= !empty($cliente['foto']) ? 'true' : 'false' ?>;
    if (fotoExistente) {
        uploadLabel.style.display = 'none';
        previewWrapper.style.display = 'block';
    } else {
        uploadLabel.style.display = 'block';
        previewWrapper.style.display = 'none';
    }
    inputArquivo.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            uploadLabel.style.display = 'none';
            previewWrapper.style.display = 'block';
            removerFotoExistenteInput.value = '0'; 
            reader.onload = (e) => { previewImage.src = e.target.result; }
            reader.readAsDataURL(file);
        }
    });
    removeButton.addEventListener('click', function() {
        inputArquivo.value = '';
        previewImage.src = '#';
        previewWrapper.style.display = 'none';
        uploadLabel.style.display = 'block';
        removerFotoExistenteInput.value = '1';
    });
    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('numero');
    const form = document.getElementById('edit-cliente-form');
    const camposDinheiro = ['renda', 'entrada', 'fgts', 'subsidio'];
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
            value = value.replace(/(\d{5})(\d{4})$/, '$1-$2');
            telefoneInput.value = value;
        });
    }
    const formatToCurrency = (value) => {
        let digits = String(value).replace(/\D/g, '');
        if (!digits) return '';
        const valueAsNumber = parseFloat(digits) / 100;
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valueAsNumber);
    };
    camposDinheiro.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            if(input.value) {
                input.value = formatToCurrency(input.value);
            }
            input.addEventListener('input', (e) => {
                e.target.value = formatToCurrency(e.target.value);
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
});
</script>
