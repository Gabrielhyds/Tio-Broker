<a href="listar.php" class="inline-flex items-center text-blue-600 hover:underline mb-6">
    <i class="fas fa-arrow-left mr-2"></i> Voltar para listagem
</a>

<?php
// Exibe imagem principal
$imagemPrincipal = !empty($imagens) ? BASE_URL . ltrim($imagens[0]['caminho'], '/') : BASE_URL . 'assets/no-image.jpg';

// Verifica se é "novo" (menos de 7 dias)
$ehNovo = (strtotime($imovel['data_cadastro']) >= strtotime('-7 days'));
?>

<div class="relative mb-6 rounded-2xl overflow-hidden shadow-lg">
    <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($imovel['titulo']) ?>" class="w-full h-64 object-cover">

    <!-- Badge Novo -->
    <?php if ($ehNovo): ?>
        <span class="absolute top-4 left-4 bg-green-600 text-white text-xs px-2 py-1 rounded-full shadow">Novo</span>
    <?php endif; ?>

    <!-- Botão Favoritar -->
    <button class="absolute top-4 right-4 bg-white/80 backdrop-blur-md p-2 rounded-full text-red-500 hover:scale-110 transition" title="Favoritar">
        <i class="far fa-heart text-lg"></i>
    </button>
</div>

<div class="bg-white shadow-xl rounded-2xl p-8 space-y-8">
    <!-- Título e descrição -->
    <div>
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2"><?= htmlspecialchars($imovel['titulo']) ?></h2>
        <p class="text-lg text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($imovel['descricao'])) ?></p>
    </div>

    <!-- Informações principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base text-gray-700">
        <div class="space-y-2">
            <p><i class="fas fa-tag mr-2 text-gray-500"></i><strong>Tipo:</strong> <?= ucfirst($imovel['tipo']) ?></p>
            <p><i class="fas fa-info-circle mr-2 text-gray-500"></i><strong>Status:</strong> <?= ucfirst($imovel['status']) ?></p>
            <p><i class="fas fa-dollar-sign mr-2 text-green-600"></i><strong>Preço:</strong> <span class="text-green-700 font-semibold">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></span></p>
        </div>
        <div class="space-y-2">
            <p><i class="fas fa-map-marker-alt mr-2 text-gray-500"></i><strong>Endereço:</strong> <?= $imovel['endereco'] ?></p>
            <p><i class="fas fa-location-arrow mr-2 text-gray-500"></i><strong>Latitude:</strong> <?= $imovel['latitude'] ?></p>
            <p><i class="fas fa-location-arrow mr-2 text-gray-500"></i><strong>Longitude:</strong> <?= $imovel['longitude'] ?></p>
        </div>
    </div>

    <!-- Mapa (Google Maps embed) -->
    <?php if ($imovel['latitude'] && $imovel['longitude']): ?>
        <div class="mt-6">
            <iframe
                src="https://maps.google.com/maps?q=<?= $imovel['latitude'] ?>,<?= $imovel['longitude'] ?>&z=16&output=embed"
                class="w-full h-64 rounded-xl shadow"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        </div>
    <?php endif; ?>

    <!-- Galeria de imagens -->
    <?php if (!empty($imagens)): ?>
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4"><i class="fas fa-image mr-2"></i>Galeria de Imagens</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($imagens as $img): ?>
                    <?php $url = BASE_URL . ltrim($img['caminho'], '/'); ?>
                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="block">
                        <img src="<?= htmlspecialchars($url) ?>" alt="Imagem do imóvel <?= htmlspecialchars($imovel['titulo']) ?>" class="rounded-xl shadow hover:scale-105 transition duration-200 object-cover w-full h-40" loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Vídeos -->
    <?php if (!empty($videos)): ?>
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4"><i class="fas fa-video mr-2"></i>Vídeos</h3>
            <div class="space-y-4">
                <?php foreach ($videos as $vid): ?>
                    <?php $url = BASE_URL . $vid['caminho']; ?>
                    <?php if (preg_match('/\.mp4$/', $vid['caminho'])): ?>
                        <video controls class="w-full max-w-3xl rounded-lg shadow" preload="metadata">
                            <source src="<?= htmlspecialchars($url) ?>" type="video/mp4">
                            Seu navegador não suporta vídeo.
                        </video>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="text-blue-600 underline">
                            Link para vídeo externo
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Documentos -->
    <?php if (!empty($documentos)): ?>
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4"><i class="fas fa-file-alt mr-2"></i>Documentos</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <?php foreach ($documentos as $doc): ?>
                    <?php $url = BASE_URL . $doc['caminho']; ?>
                    <li>
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="text-blue-600 hover:underline">
                            <?= htmlspecialchars(basename($doc['caminho'])) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>