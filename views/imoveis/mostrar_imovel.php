<?php
// Este código deve estar no início do seu ficheiro `mostrar.php`
// As variáveis $imovel, $imagens, $videos, $documentos já devem ter sido carregadas.

// Lógica para formatar o endereço completo para exibição e para a query do mapa.
$enderecoArray = array_filter([
    $imovel['endereco'],
    $imovel['numero'],
    $imovel['bairro'],
    $imovel['cidade'],
    $imovel['estado']
]);
$enderecoCompleto = implode(', ', $enderecoArray);
$mapQuery = urlencode($enderecoCompleto . ', ' . $imovel['cep']);


// Define a imagem principal
$imagemPrincipal = !empty($imagens) ? BASE_URL . ltrim($imagens[0]['caminho'], '/') : BASE_URL . 'assets/img/no-image.jpg';

// Verifica se o imóvel é um cadastro recente (últimos 7 dias)
$ehNovo = (strtotime($imovel['data_cadastro']) >= strtotime('-7 days'));
?>

<!-- Link para a biblioteca de ícones Bootstrap Icons (se não estiver no template base) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="max-w-7xl mx-auto">
    <!-- Botão Voltar -->
    <a href="listar.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors mb-6 text-sm font-semibold">
        <i class="bi bi-arrow-left-circle"></i>
        Voltar para a listagem
    </a>

    <!-- Layout Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

        <!-- Coluna Principal de Conteúdo (Esquerda) -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Cabeçalho do Imóvel -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight"><?= htmlspecialchars($imovel['titulo']) ?></h1>
                <p class="text-lg text-gray-500 mt-2 flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span><?= htmlspecialchars($enderecoCompleto) ?></span>
                </p>
            </div>

            <!-- Imagem Principal -->
            <div class="relative rounded-2xl overflow-hidden shadow-lg">
                <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="Foto principal de <?= htmlspecialchars($imovel['titulo']) ?>" class="w-full h-96 object-cover">
                <?php if ($ehNovo): ?>
                    <span class="absolute top-4 left-4 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">NOVO</span>
                <?php endif; ?>
            </div>

            <!-- Seção "Sobre este imóvel" -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Sobre este imóvel</h2>
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($imovel['descricao'])) ?>
                </div>
            </div>

            <!-- Seção "Localização e Mapa" -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Localização</h2>
                <iframe
                    src="https://maps.google.com/maps?q=<?= $mapQuery ?>&z=16&output=embed"
                    class="w-full h-80 rounded-xl shadow-md border"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen>
                </iframe>
            </div>

            <!-- Galeria de Imagens -->
            <?php if (!empty($imagens)): ?>
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Galeria de Imagens</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($imagens as $img): ?>
                            <a href="<?= BASE_URL . ltrim($img['caminho'], '/') ?>" target="_blank" class="block rounded-xl overflow-hidden shadow transition-transform duration-300 hover:scale-105">
                                <img src="<?= BASE_URL . ltrim($img['caminho'], '/') ?>" alt="Galeria do imóvel" class="w-full h-48 object-cover">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Vídeos e Documentos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php if (!empty($videos)): ?>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Vídeos</h2>
                        <div class="space-y-4">
                            <?php foreach ($videos as $vid): ?>
                                <video controls class="w-full rounded-lg shadow" preload="metadata">
                                    <source src="<?= BASE_URL . ltrim($vid['caminho'], '/') ?>" type="video/mp4">
                                    Seu navegador não suporta o elemento de vídeo.
                                </video>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($documentos)): ?>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Documentos</h2>
                        <div class="space-y-2">
                            <?php foreach ($documentos as $doc): ?>
                                <a href="<?= BASE_URL . ltrim($doc['caminho'], '/') ?>" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border">
                                    <i class="bi bi-file-earmark-text-fill text-blue-600 text-xl"></i>
                                    <span class="text-sm font-medium text-gray-700 truncate"><?= htmlspecialchars(basename($doc['caminho'])) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Coluna Lateral de Resumo (Direita) -->
        <div class="lg:col-span-1">
            <div class="lg:sticky top-8 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <p class="text-gray-500 text-sm">Preço de Venda</p>
                    <p class="text-4xl font-bold text-gray-900 mt-1">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></p>
                    
                    <div class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Tipo de Anúncio</span>
                            <span class="font-bold text-gray-800"><?= ucfirst($imovel['tipo']) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Status</span>
                            <span class="font-bold text-white text-xs px-2 py-1 rounded-full <?= $imovel['status'] === 'disponivel' ? 'bg-green-600' : 'bg-yellow-600' ?>"><?= ucfirst($imovel['status']) ?></span>
                        </div>
                    </div>

                    <button class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg mt-6 hover:bg-blue-700 transition-transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <i class="bi bi-whatsapp mr-2"></i>
                        Entrar em Contato
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
