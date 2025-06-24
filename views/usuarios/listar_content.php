<!-- Cabeçalho da página com título e botão de ação. -->
<div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Usuários Cadastrados</h2>
    <!-- Link estilizado como botão que leva para a página de cadastro de um novo usuário. -->
    <a href="cadastrar.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i> Novo Usuário
    </a>
</div>

<!-- Bloco PHP para exibir uma mensagem de sucesso (se existir na sessão). -->
<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4 shadow">
        <?= htmlspecialchars($_SESSION['sucesso']); // Exibe a mensagem de sucesso com segurança.
        unset($_SESSION['sucesso']); // Remove a mensagem da sessão para não exibi-la novamente. 
        ?>
    </div>
<?php endif; ?>

<!-- Formulário para filtrar a lista de usuários. -->
<form method="GET" action="listar.php" class="mb-6">
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Campo de texto para o usuário digitar o termo da busca. -->
        <input type="text" name="filtro" placeholder="Buscar por nome, email, permissão ou imobiliária..."
            class="w-full md:w-1/2 p-2 border border-gray-300 rounded"
            value="<?= htmlspecialchars($filtro) ?>"> <!-- Exibe o valor do filtro atual. -->
        <div class="flex gap-2">
            <!-- Botão para submeter o formulário de busca. -->
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            <!-- Link para limpar o filtro e recarregar a página. -->
            <a href="listar.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>
</form>

<!-- Contêiner da tabela, permitindo rolagem horizontal em telas pequenas. -->
<div class="overflow-x-auto bg-white p-4 rounded shadow">
    <table class="min-w-full text-sm text-gray-700">
        <!-- Cabeçalho da tabela. -->
        <thead class="bg-gray-100">
            <tr>
                <th class="text-left px-4 py-2">Nome</th>
                <th class="text-left px-4 py-2">Email</th>
                <th class="text-left px-4 py-2">Permissão</th>
                <th class="text-left px-4 py-2">Imobiliária</th>
                <th class="text-center px-4 py-2">Ações</th>
            </tr>
        </thead>
        <!-- Corpo da tabela onde os dados dos usuários são exibidos. -->
        <tbody>
            <!-- Verifica se a lista de usuários não está vazia. -->
            <?php if (!empty($lista)): ?>
                <!-- Loop para percorrer cada usuário na lista. -->
                <?php foreach ($lista as $u): ?>
                    <tr class="border-t">
                        <!-- Colunas com os dados do usuário. -->
                        <td class="px-4 py-2"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($u['permissao']) ?></td>
                        <!-- Exibe o nome da imobiliária ou '---' se não houver. -->
                        <td class="px-4 py-2"><?= htmlspecialchars($u['nome_imobiliaria'] ?? '---') ?></td>
                        <!-- Coluna com os botões de ação (editar e excluir). -->
                        <td class="px-4 py-2 text-center">
                            <!-- Link para editar o usuário. -->
                            <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="text-yellow-500 hover:text-yellow-600 mr-2">
                                <i class="fas fa-pen"></i>
                            </a>
                            <!-- Link para excluir o usuário, com uma confirmação em JavaScript. -->
                            <a href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>"
                                onclick="return confirm('Deseja realmente excluir?')"
                                class="text-red-500 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Linha exibida se a lista de usuários estiver vazia. -->
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">Nenhum usuário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Seção de paginação, exibida apenas se houver mais de uma página. -->
<?php if ($total_paginas > 1): ?>
    <div class="flex justify-center mt-6">
        <nav class="flex space-x-2">
            <!-- Loop para criar um link para cada página. -->
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <!-- Aplica classes diferentes para destacar a página atual -->
                <a href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>"
                    class="px-3 py-1 rounded border <?= $i == $pagina_atual ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white hover:bg-gray-100' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
<?php endif; ?>