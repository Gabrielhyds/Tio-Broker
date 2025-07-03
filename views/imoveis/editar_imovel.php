<?php
// Requerimentos e inicialização da sessão
require_once '../../config/config.php';
require_once '../../models/Imovel.php';
require_once '../../config/rotas.php';
@session_start();

// Validação do ID do imóvel
$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['erro'] = "ID do imóvel não informado.";
    header('Location: listar.php');
    exit;
}

// Conexão e busca dos dados do imóvel
try {
    $imovelModel = new Imovel($connection);
    $imovel = $imovelModel->buscarPorId($id);

    // Verifica se o imóvel foi encontrado
    if (!$imovel) {
        $_SESSION['erro'] = "Imóvel não encontrado.";
        header('Location: listar.php');
        exit;
    }

    // Busca os arquivos associados
    $imagens = $imovelModel->buscarArquivos($id, 'imagem');
    $videos = $imovelModel->buscarArquivos($id, 'video');
    $documentos = $imovelModel->buscarArquivos($id, 'documento');
} catch (Exception $e) {
    // Em um ambiente de produção, seria bom logar o erro.
    $_SESSION['erro'] = "Ocorreu um erro ao buscar os dados do imóvel: " . $e->getMessage();
    header('Location: listar.php');
    exit;
}
?>

<!-- 
    HEAD da página. Adicione isso ao seu <head> principal.
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Classe para esconder visualmente um elemento, mas mantê-lo acessível para leitores de tela */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>
-->

<div class="bg-gray-50 min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        <form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data">

            <!-- Cabeçalho da Página e Ações -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Editar Imóvel</h1>
                    <p class="text-gray-600 mt-1">Atualize as informações do imóvel: <span class="font-semibold text-blue-600"><?= htmlspecialchars($imovel['titulo']) ?></span></p>
                </div>
                <!-- ✅ CORREÇÃO: Botões movidos para o topo -->
                <div class="flex items-center gap-4 mt-4 sm:mt-0">
                    <a href="listar.php" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm hover:shadow-md">
                        Salvar Alterações
                    </button>
                </div>
            </div>

            <!-- Alertas de Sucesso/Erro -->
            <?php if (isset($_SESSION['sucesso'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                    <p class="font-bold">Sucesso!</p>
                    <p><?= htmlspecialchars($_SESSION['sucesso']); ?></p>
                    <?php unset($_SESSION['sucesso']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                    <p class="font-bold">Erro!</p>
                    <p><?= htmlspecialchars($_SESSION['erro']); ?></p>
                    <?php unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>


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
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">R$</span>
                                        </div>
                                        <input type="number" step="0.01" name="preco" id="preco" value="<?= htmlspecialchars($imovel['preco']) ?>" required class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                                <img src="<?= BASE_URL . str_replace('\\', '/', ltrim($img['caminho'], '/')) ?>" alt="Imagem do Imóvel" class="rounded-lg w-full h-28 object-cover transition-transform duration-300 group-hover:scale-105">
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

                            <!-- Upload de Novos Arquivos -->
                            <div class="space-y-4">
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
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Função para deletar arquivos via POST
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

    // Função para atualizar o texto do label do input de arquivo
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

    // Inicializa os inputs de arquivo
    document.addEventListener('DOMContentLoaded', function() {
        setupFileInput('imagens', 'imagens-label', 'imagem', 'imagens');
        setupFileInput('videos', 'videos-label', 'vídeo', 'vídeos');
    });
</script>