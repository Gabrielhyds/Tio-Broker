<?php
// Este arquivo é um "pedaço" da página e espera que a variável $empreendimento já exista.
// Ela é criada no arquivo principal: ver_empreendimento.php
?>
<div class="max-w-7xl mx-auto bg-white rounded-3xl shadow-lg p-8">

    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row justify-between items-start mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($empreendimento['nome']) ?></h1>
            <p class="text-gray-500 mt-2"><?= htmlspecialchars($empreendimento['cidade']) ?> - <?= htmlspecialchars($empreendimento['estado']) ?></p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="listar_empreendimento.php" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition">Voltar</a>
            <a href="editar_empreendimento.php?id=<?= $empreendimento['id_empreendimento'] ?>" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">Editar</a>
        </div>
    </div>

    <!-- Galeria de Imagens -->
    <section class="mb-10">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Galeria de Imagens</h2>
        <?php if (!empty($empreendimento['imagens'])): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($empreendimento['imagens'] as $imagem): ?>
                    <a href="../../<?= htmlspecialchars($imagem['caminho']) ?>" target="_blank">
                        <img src="../../<?= htmlspecialchars($imagem['caminho']) ?>" alt="Imagem do empreendimento" class="w-full h-40 object-cover rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Nenhuma imagem cadastrada.</p>
        <?php endif; ?>
    </section>

    <!-- Detalhes do Empreendimento -->
    <section class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Detalhes</h2>
            <div class="space-y-3 text-gray-600">
                <p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($empreendimento['descricao'])) ?></p>
                <p><strong>Categoria:</strong> <span class="font-medium text-gray-800"><?= htmlspecialchars(ucfirst($empreendimento['categoria'])) ?></span></p>
                <p><strong>Status:</strong> <span class="font-medium text-gray-800"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $empreendimento['status']))) ?></span></p>
                <p><strong>Responsável:</strong> <span class="font-medium text-gray-800"><?= htmlspecialchars($empreendimento['responsavel'] ?: 'Não informado') ?></span></p>
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Localização e Preços</h2>
            <div class="space-y-3 text-gray-600">
                <p><strong>Endereço:</strong> <span class="font-medium text-gray-800"><?= htmlspecialchars($empreendimento['endereco'] ?: 'Não informado') ?></span></p>
                <p><strong>CEP:</strong> <span class="font-medium text-gray-800"><?= htmlspecialchars($empreendimento['cep'] ?: 'Não informado') ?></span></p>
                <p><strong>Preço Mínimo:</strong> <span class="font-medium text-gray-800">R$ <?= htmlspecialchars(number_format($empreendimento['preco_min'] ?? 0, 2, ',', '.')) ?></span></p>
                <p><strong>Preço Máximo:</strong> <span class="font-medium text-gray-800">R$ <?= htmlspecialchars(number_format($empreendimento['preco_max'] ?? 0, 2, ',', '.')) ?></span></p>
            </div>
        </div>
    </section>

    <!-- Vídeos e Documentos -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Vídeos</h2>
            <?php if (!empty($empreendimento['videos'])): ?>
                <div class="space-y-4">
                <?php foreach ($empreendimento['videos'] as $video): ?>
                    <a href="../../<?= htmlspecialchars($video['caminho']) ?>" target="_blank" class="block bg-gray-100 p-3 rounded-xl hover:bg-gray-200 transition">
                        <span class="text-blue-600 font-semibold underline">Assistir Vídeo</span>
                        <p class="text-xs text-gray-500 truncate"><?= basename($video['caminho']) ?></p>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Nenhum vídeo cadastrado.</p>
            <?php endif; ?>
        </div>
         <div>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Documentos</h2>
             <?php if (!empty($empreendimento['documentos'])): ?>
                <div class="space-y-4">
                <?php foreach ($empreendimento['documentos'] as $documento): ?>
                    <a href="../../<?= htmlspecialchars($documento['caminho']) ?>" target="_blank" class="block bg-gray-100 p-3 rounded-xl hover:bg-gray-200 transition">
                        <span class="text-blue-600 font-semibold underline">Baixar Documento</span>
                        <p class="text-xs text-gray-500 truncate"><?= basename($documento['caminho']) ?></p>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Nenhum documento cadastrado.</p>
            <?php endif; ?>
        </div>
    </section>

</div>
