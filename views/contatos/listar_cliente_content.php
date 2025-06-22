<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="bi bi-people-fill"></i>
            Clientes Cadastrados
        </h2>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <a href="<?= htmlspecialchars($dashboardUrl ?? '../../index.php') ?>" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border rounded-md text-gray-700 bg-white hover:bg-gray-100">
                <i class="bi bi-house-door-fill mr-2"></i> Voltar ao Início
            </a>
            <a href="index.php?controller=cliente&action=cadastrar" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                <i class="bi bi-plus-circle mr-2"></i> Novo Cliente
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="mb-4 p-4 text-green-800 bg-green-100 border border-green-200 rounded">
            <i class="bi bi-check-circle-fill mr-2"></i> <?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="mb-4 p-4 text-red-800 bg-red-100 border border-red-200 rounded">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i> <?= htmlspecialchars($_SESSION['mensagem_erro']); ?>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
        <div class="text-center py-10">
            <i class="bi bi-journal-x text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800">Nenhum Cliente Encontrado</h3>
            <p class="text-gray-600">Parece que ainda não há clientes cadastrados no sistema.</p>
            <a href="index.php?controller=cliente&action=cadastrar" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                <i class="bi bi-person-plus-fill mr-1"></i> Adicionar Primeiro Cliente
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="p-3">Nome</th>
                        <th class="p-3">Telefone</th>
                        <th class="p-3 hidden lg:table-cell">CPF</th>
                        <th class="p-3 hidden md:table-cell">Empreendimento</th>
                        <th class="p-3 text-center">Classificação</th>
                        <th class="p-3 text-center">Foto</th>
                        <th class="p-3 hidden md:table-cell">Corretor</th>
                        <?php if ($_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                            <th class="p-3 hidden md:table-cell">Imobiliária</th>
                        <?php endif; ?>
                        <th class="p-3 hidden lg:table-cell">Data Cadastro</th>
                        <th class="p-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-800">
                    <?php foreach ($clientes as $cliente): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="p-3 font-medium"><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($cliente['numero']) ?></td>
                            <td class="p-3 hidden lg:table-cell"><?= htmlspecialchars($cliente['cpf'] ?? '-') ?></td>
                            <td class="p-3 hidden md:table-cell"><?= htmlspecialchars($cliente['empreendimento'] ?? '-') ?></td>
                            <td class="p-3 text-center">
                                <?php
                                switch ($cliente['tipo_lista']) {
                                    case 'Potencial':
                                        $tipoListaClass = 'bg-green-100 text-green-800 border-green-200';
                                        break;
                                    case 'Não potencial':
                                        $tipoListaClass = 'bg-red-100 text-red-800 border-red-200';
                                        break;
                                    default:
                                        $tipoListaClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                        break;
                                }
                                ?>
                                <span class="px-2 py-1 border text-xs font-medium rounded <?= $tipoListaClass ?>">
                                    <?= htmlspecialchars($cliente['tipo_lista']) ?>
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <?php if (!empty($cliente['foto'])): ?>
                                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto" class="w-10 h-10 rounded object-cover inline-block border" onerror="this.src='https://via.placeholder.com/40';">
                                <?php else: ?>
                                    <i class="bi bi-camera-fill text-xl text-gray-400"></i>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 hidden md:table-cell"><?= htmlspecialchars($cliente['nome_corretor'] ?? 'N/A') ?></td>
                            <?php if ($_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                <td class="p-3 hidden md:table-cell"><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'N/A') ?></td>
                            <?php endif; ?>
                            <td class="p-3 hidden lg:table-cell">
                                <div><?= isset($cliente['criado_em']) ? date('d/m/Y', strtotime($cliente['criado_em'])) : '-' ?></div>
                                <small class="text-gray-500"><?= isset($cliente['criado_em']) ? date('H:i', strtotime($cliente['criado_em'])) : '' ?></small>
                            </td>
                            <td class="p-3 text-center">
                                <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= $cliente['id_cliente'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="bi bi-eye-fill"></i> <span class="hidden md:inline">Detalhes</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>