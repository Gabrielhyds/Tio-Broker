<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Usuários Cadastrados</h2>
    <a href="cadastrar.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i> Novo Usuário
    </a>
</div>

<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4 shadow">
        <?= htmlspecialchars($_SESSION['sucesso']);
        unset($_SESSION['sucesso']); ?>
    </div>
<?php endif; ?>

<form method="GET" action="listar.php" class="mb-6">
    <div class="flex flex-col md:flex-row gap-4">
        <input type="text" name="filtro" placeholder="Buscar por nome, email, permissão ou imobiliária..."
            class="w-full md:w-1/2 p-2 border border-gray-300 rounded"
            value="<?= htmlspecialchars($filtro) ?>">
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            <a href="listar.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>
</form>

<div class="overflow-x-auto bg-white p-4 rounded shadow">
    <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left px-4 py-2">Nome</th>
                <th class="text-left px-4 py-2">Email</th>
                <th class="text-left px-4 py-2">Permissão</th>
                <th class="text-left px-4 py-2">Imobiliária</th>
                <th class="text-center px-4 py-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($lista)): ?>
                <?php foreach ($lista as $u): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($u['permissao']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($u['nome_imobiliaria'] ?? '---') ?></td>
                        <td class="px-4 py-2 text-center">
                            <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="text-yellow-500 hover:text-yellow-600 mr-2">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>"
                                onclick="return confirm('Deseja realmente excluir?')"
                                class="text-red-500 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">Nenhum usuário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_paginas > 1): ?>
    <div class="flex justify-center mt-6">
        <nav class="flex space-x-2">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>"
                    class="px-3 py-1 rounded border <?= $i == $pagina_atual ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white hover:bg-gray-100' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
<?php endif; ?>