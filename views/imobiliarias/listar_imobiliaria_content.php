<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['sucesso']);
        unset($_SESSION['sucesso']); ?>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Imobiliárias Cadastradas</h2>
    <a href="cadastrar_imobiliaria.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
        <i class="fas fa-plus mr-2"></i> Nova Imobiliária
    </a>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="p-4">
        <form method="GET" action="listar_imobiliaria.php" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="filtro" class="w-full border border-gray-300 rounded px-4 py-2 text-sm" placeholder="Buscar por ID, nome ou CNPJ..." value="<?= htmlspecialchars($filtro) ?>">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <a href="listar_imobiliaria.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm" title="Limpar filtro">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-xs uppercase font-semibold text-gray-600">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Nome</th>
                        <th class="px-4 py-2">CNPJ</th>
                        <th class="px-4 py-2">Usuários</th>
                        <th class="px-4 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lista)): ?>
                        <?php foreach ($lista as $item): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?= $item['id_imobiliaria'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($item['nome']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($item['cnpj']) ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        <?= $item['total_usuarios'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="editar_imobiliaria.php?id=<?= $item['id_imobiliaria'] ?>" class="text-yellow-500 hover:text-yellow-600" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>" class="text-red-500 hover:text-red-600" title="Excluir" onclick="return confirm('Deseja realmente excluir?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-400">Nenhuma imobiliária encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
            <div class="mt-6 flex justify-center">
                <nav class="inline-flex -space-x-px text-sm">
                    <a href="?pagina=<?= $pagina_atual - 1 ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 rounded-l <?= $pagina_atual <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">Anterior</a>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 <?= $i == $pagina_atual ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a href="?pagina=<?= $pagina_atual + 1 ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 rounded-r <?= $pagina_atual >= $total_paginas ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">Próxima</a>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>