
<div class="bg-gray-50 min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        <form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data" id="imovel-form">

            <!-- Cabeçalho da Página e Ações -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Editar Imóvel</h1>
                    <p class="text-gray-600 mt-1">Atualize as informações do imóvel: <span class="font-semibold text-blue-600"><?= htmlspecialchars($imovel['titulo']) ?></span></p>
                </div>
                <div class="flex items-center gap-4 mt-4 sm:mt-0">
                    <a href="listar.php" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm hover:shadow-md">
                        Salvar Alterações
                    </button>
                </div>
            </div>


            <input type="hidden" name="action" value="editar">
            <input type="hidden" name="id_imovel" value="<?= htmlspecialchars($imovel['id_imovel']) ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Coluna Esquerda: Detalhes do Imóvel -->
                <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-md space-y-8">

                    <!-- Seção: Informações Principais -->
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Detalhes do Imóvel</legend>
                        <div class="space-y-6">
                            <div>
                                <label for="titulo" class="block text-sm font-medium text-gray-700">Título *</label>
                                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($imovel['titulo']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$) *</label>
                                    <div class="relative mt-1">
                                        <input type="text" name="preco" id="preco" value="<?= number_format($imovel['preco'], 2, ',', '.') ?>" required class=" block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="R$ 0,00">
                                    </div>
                                </div>
                                <div>
                                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Anúncio *</label>
                                    <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <?php foreach (["venda", "locacao", "temporada", "lancamento"] as $tipo): ?>
                                            <option value="<?= $tipo ?>" <?= $imovel['tipo'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                                <textarea name="descricao" id="descricao" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($imovel['descricao']) ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Seção: Localização -->
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Localização</legend>
                        <div class="space-y-6">
                            <div>
                                <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                                <input type="text" name="endereco" id="endereco" value="<?= htmlspecialchars($imovel['endereco']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" value="<?= htmlspecialchars($imovel['latitude']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="text" name="longitude" id="longitude" value="<?= htmlspecialchars($imovel['longitude']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Coluna Direita: Mídia e Status -->
                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <fieldset>
                            <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Status do Anúncio</legend>
                            <label for="status" class="block text-sm font-medium text-gray-700">Disponibilidade *</label>
                            <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <?php foreach (["disponivel", "reservado", "vendido", "indisponivel"] as $status): ?>
                                    <option value="<?= $status ?>" <?= $imovel['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </fieldset>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <fieldset>
                            <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Gerenciar Mídias</legend>

                            <!-- Galeria de Imagens Existentes -->
                            <?php if (!empty($imagens)): ?>
                                <div class="mb-6">
                                    <h3 class="text-md font-semibold text-gray-700 mb-4">Imagens Atuais</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        <?php foreach ($imagens as $img): ?>
                                            <div class="relative group">
                                                <img src="/tio-broker/<?= str_replace('\\', '/', ltrim($img['caminho'], '/')) ?>" alt="Imagem do Imóvel" class="rounded-lg w-full h-28 object-cover transition-transform duration-300 group-hover:scale-105">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-lg flex items-center justify-center">
                                                    <button
                                                        type="button"
                                                        onclick="deleteFile('<?= htmlspecialchars($img['id']) ?>', '<?= htmlspecialchars($imovel['id_imovel']) ?>', 'imagem')"
                                                        class="text-white opacity-0 group-hover:opacity-100 transition-opacity p-2 rounded-full bg-red-600 hover:bg-red-700"
                                                        aria-label="Excluir Imagem">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Galeria de Vídeos Existentes -->
                            <?php if (!empty($videos)): ?>
                                <div class="mb-6">
                                    <h3 class="text-md font-semibold text-gray-700 mb-4">Vídeos Atuais</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        <?php foreach ($videos as $vid): ?>
                                            <div class="relative group bg-black rounded-lg">
                                                <video controls class="rounded-lg w-full h-28 object-cover">
                                                    <source src="<?= BASE_URL . str_replace('\\', '/', ltrim($vid['caminho'], '/')) ?>" type="video/mp4">
                                                    Seu navegador não suporta o elemento de vídeo.
                                                </video>
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 rounded-lg flex items-center justify-center">
                                                    <button
                                                        type="button"
                                                        onclick="deleteFile('<?= htmlspecialchars($vid['id']) ?>', '<?= htmlspecialchars($imovel['id_imovel']) ?>', 'video')"
                                                        class="text-white opacity-0 group-hover:opacity-100 transition-opacity p-2 rounded-full bg-red-600 hover:bg-red-700"
                                                        aria-label="Excluir Vídeo">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Galeria de Documentos Existentes -->
                            <?php if (!empty($documentos)): ?>
                                <div class="mb-6">
                                    <h3 class="text-md font-semibold text-gray-700 mb-4">Documentos Atuais</h3>
                                    <div class="space-y-3">
                                        <?php foreach ($documentos as $doc): ?>
                                            <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg">
                                                <a href="<?= BASE_URL . str_replace('\\', '/', ltrim($doc['caminho'], '/')) ?>" target="_blank" class="flex items-center gap-3 text-blue-600 hover:underline">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium truncate"><?= htmlspecialchars(basename($doc['caminho'])) ?></span>
                                                </a>
                                                <button type="button" onclick="deleteFile('<?= htmlspecialchars($doc['id']) ?>', '<?= htmlspecialchars($imovel['id_imovel']) ?>', 'documento')" class="flex-shrink-0 text-gray-400 hover:text-red-600" aria-label="Excluir Documento">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Upload de Novos Arquivos -->
                            <div class="space-y-4 pt-4 border-t">
                                <div>
                                    <label for="imagens" class="block text-sm font-medium text-gray-700">Adicionar Imagens</label>
                                    <div class="mt-1">
                                        <input type="file" name="imagens[]" id="imagens" multiple accept="image/*" class="sr-only">
                                        <label for="imagens" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span id="imagens-label">Selecionar Imagens</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="videos" class="block text-sm font-medium text-gray-700">Adicionar Vídeos</label>
                                    <div class="mt-1">
                                        <input type="file" name="videos[]" id="videos" multiple accept="video/*" class="sr-only">
                                        <label for="videos" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <span id="videos-label">Selecionar Vídeos</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="documentos" class="block text-sm font-medium text-gray-700">Adicionar Documentos</label>
                                    <div class="mt-1">
                                        <input type="file" name="documentos[]" id="documentos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" class="sr-only">
                                        <label for="documentos" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span id="documentos-label">Selecionar Documentos</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ✅ SCRIPT CORRIGIDO E MELHORADO -->
<script>
    // Função para deletar arquivos via POST (original do seu código)
    function deleteFile(idArquivo, idImovel, tipo) {
        if (confirm(`Tem certeza que deseja excluir este ${tipo}? A ação não pode ser desfeita.`)) {
            // Cria um formulário dinamicamente
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../controllers/ImovelController.php';

            // Cria os campos ocultos com os dados necessários
            const fields = {
                action: 'excluir_arquivo',
                id_arquivo: idArquivo,
                id_imovel: idImovel,
                tipo: tipo
            };

            for (const key in fields) {
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = key;
                hiddenField.value = fields[key];
                form.appendChild(hiddenField);
            }

            // Adiciona o formulário à página e o submete
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Função para atualizar o texto do label do input de arquivo (original do seu código)
    function setupFileInput(inputId, labelId, single, plural) {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.addEventListener('change', function(e) {
            const label = document.getElementById(labelId);
            const fileCount = e.target.files.length;
            if (fileCount === 1) {
                label.textContent = e.target.files[0].name;
            } else if (fileCount > 1) {
                label.textContent = `${fileCount} ${plural} selecionados`;
            } else {
                label.textContent = `Selecionar ${plural}`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const precoInput = document.getElementById('preco');
        const imovelForm = document.getElementById('imovel-form');

        // --- Lógica de Formatação de Preço ---
        if (precoInput) {
            // Função que formata uma string de dígitos (ex: "12345") para moeda (ex: "R$ 123,45")
            const formatToCurrency = (digits) => {
                if (!digits) return '';
                // Usa a API Intl.NumberFormat que é o padrão moderno para isso.
                // Ela lida com diferentes localidades e formatos automaticamente.
                const valueAsNumber = parseInt(digits, 10) / 100;
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(valueAsNumber);
            };

            // Formata o valor inicial que vem do PHP
            const initialDigits = precoInput.value.replace(/\D/g, '');
            precoInput.value = formatToCurrency(initialDigits);

            // Formata o valor enquanto o usuário digita
            precoInput.addEventListener('input', (e) => {
                const currentDigits = e.target.value.replace(/\D/g, '');
                e.target.value = formatToCurrency(currentDigits);
            });
        }

        // --- Lógica do Formulário ---
        if (imovelForm) {
            imovelForm.addEventListener('submit', () => {
                if (precoInput && precoInput.value) {
                    // Pega o valor formatado (ex: "R$ 9,99")
                    // Extrai apenas os dígitos ("999")
                    const digits = precoInput.value.replace(/\D/g, '');

                    if (digits) {
                        // Converte para o formato numérico correto para o backend (ex: "9.99")
                        const valueAsNumber = parseInt(digits, 10) / 100;
                        precoInput.value = valueAsNumber.toFixed(2);
                    }
                }
            });
        }

        // --- Inicialização dos Labels de Arquivo ---
        setupFileInput('imagens', 'imagens-label', 'imagem', 'imagens');
        setupFileInput('videos', 'videos-label', 'vídeo', 'vídeos');
        setupFileInput('documentos', 'documentos-label', 'documento', 'documentos');
    });
</script>
