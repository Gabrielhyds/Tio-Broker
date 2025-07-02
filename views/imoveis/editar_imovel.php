<?php
require_once '../../config/config.php';
require_once '../../models/Imovel.php';
require_once '../../config/rotas.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['erro'] = "ID do imóvel não informado.";
    header('Location: listar.php');
    exit;
}

$imovelModel = new Imovel($connection);
$imovel = $imovelModel->buscarPorId($id);
$imagens = $imovelModel->buscarArquivos($id, 'imagem');
$videos = $imovelModel->buscarArquivos($id, 'video');
$documentos = $imovelModel->buscarArquivos($id, 'documento');

if (!$imovel) {
    $_SESSION['erro'] = "Imóvel não encontrado.";
    header('Location: listar.php');
    exit;
}
?>

<h2 class="text-2xl font-bold mb-6 text-gray-900">Editar Imóvel</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-6">
        <?= htmlspecialchars($_SESSION['erro']);
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data" class="space-y-8 bg-white p-8 rounded-xl shadow-lg">
    <input type="hidden" name="action" value="editar">
    <input type="hidden" name="id_imovel" value="<?= htmlspecialchars($imovel['id_imovel']) ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700">Título *</label>
            <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($imovel['titulo']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$) *</label>
            <input type="number" step="0.01" name="preco" id="preco" value="<?= htmlspecialchars($imovel['preco']) ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?= htmlspecialchars($imovel['descricao']) ?></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo *</label>
            <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <?php foreach (["venda", "locacao", "temporada", "lancamento"] as $tipo): ?>
                    <option value="<?= $tipo ?>" <?= $imovel['tipo'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
            <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <?php foreach (["disponivel", "reservado", "vendido", "indisponivel"] as $status): ?>
                    <option value="<?= $status ?>" <?= $imovel['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div>
        <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
        <input type="text" name="endereco" id="endereco" value="<?= htmlspecialchars($imovel['endereco']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" id="latitude" value="<?= htmlspecialchars($imovel['latitude']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" id="longitude" value="<?= htmlspecialchars($imovel['longitude']) ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <?php if (!empty($imagens)): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Imagens Existentes</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($imagens as $img): ?>
                    <div class="relative group">
                        <img src="<?= BASE_URL . str_replace('\\', '/', ltrim($img['caminho'], '/')) ?>" alt="Imagem" class="rounded shadow w-full h-36 object-cover">
                        <form action="../../controllers/ImovelController.php" method="POST" onsubmit="return confirm('Excluir esta imagem?')" class="absolute top-1 right-1">
                            <input type="hidden" name="action" value="excluir_arquivo">
                            <input type="hidden" name="tipo" value="imagem">
                            <input type="hidden" name="id_arquivo" value="<?= htmlspecialchars($img['id']) ?>">
                            <input type="hidden" name="id_imovel" value="<?= htmlspecialchars($imovel['id_imovel']) ?>">
                            <button type="submit" class="bg-red-600 text-white text-xs px-2 py-1 rounded opacity-80 hover:opacity-100">&#10006;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adicionar Imagens</label>
            <input type="file" name="imagens[]" multiple accept="image/*" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adicionar Vídeos</label>
            <input type="file" name="videos[]" multiple accept="video/*" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adicionar Documentos</label>
            <input type="file" name="documentos[]" multiple accept=".pdf,.doc,.docx" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 rounded-md shadow-sm">
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            Salvar Alterações
        </button>
    </div>
</form>