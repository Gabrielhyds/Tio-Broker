<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Imóveis Cadastrados</h2>
    <a href="cadastrar.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        + Novo Imóvel
    </a>
</div>

<?php if (empty($imoveis)): ?>
    <p class="text-gray-500">Nenhum imóvel cadastrado ainda.</p>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($imoveis as $imovel): ?>
            <?php
            $fotos = $imovelModel->buscarArquivos($imovel['id_imovel'], 'imagem');
            $imgUrl = !empty($fotos) ? BASE_URL . ltrim($fotos[0]['caminho'], '/') : BASE_URL . 'assets/no-image.jpg';
            ?>
            <div class="bg-white rounded-xl shadow overflow-hidden border hover:shadow-lg transition group">
                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Imagem do Imóvel"
                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200">

                <div class="p-4 space-y-1">
                    <h3 class="text-lg font-bold"><?= htmlspecialchars($imovel['titulo']) ?></h3>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($imovel['tipo']) ?> - <?= htmlspecialchars($imovel['status']) ?></p>
                    <p class="text-blue-700 font-semibold">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></p>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($imovel['endereco']) ?></p>

                    <div class="flex justify-between pt-3 text-sm">
                        <a href="mostrar.php?id=<?= $imovel['id_imovel'] ?>" class="text-blue-600 hover:underline">Ver</a>
                        <a href="editar.php?id=<?= $imovel['id_imovel'] ?>" class="text-yellow-600 hover:underline">Editar</a>
                        <a href="../../controllers/ImovelController.php?action=excluir&id=<?= $imovel['id_imovel'] ?>"
                            class="text-red-600 hover:underline"
                            onclick="return confirm('Tem certeza que deseja excluir este imóvel?')">Excluir</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>