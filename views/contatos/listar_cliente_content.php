    <style>
        /* Estilo para um corpo de página mais suave */
        body {
            background-color: #f8fafc;
            /* bg-gray-50 */
        }

        /* Adiciona transições suaves para uma melhor experiência */
        a,
        button {
            transition: all 0.2s ease-in-out;
        }

        .crm-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            /* rounded-xl */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            /* shadow-lg */
            overflow: hidden;
        }

        .crm-header {
            padding: 1.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .crm-title {
            font-size: 1.5rem;
            /* text-2xl */
            font-weight: 600;
            /* font-semibold */
            color: #1f2937;
            /* text-gray-800 */
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .crm-title i {
            color: #2563eb;
            /* text-blue-600 */
        }

        .crm-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        .crm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            /* text-sm */
            font-weight: 500;
            border-radius: 0.5rem;
            /* rounded-lg */
            border: 1px solid transparent;
        }

        .crm-btn-secondary {
            border-color: #d1d5db;
            /* border-gray-300 */
            color: #374151;
            /* text-gray-700 */
            background-color: #ffffff;
        }

        .crm-btn-secondary:hover {
            background-color: #f9fafb;
            /* hover:bg-gray-50 */
        }

        .crm-btn-primary {
            color: #ffffff;
            background-color: #2563eb;
            /* bg-blue-600 */
        }

        .crm-btn-primary:hover {
            background-color: #1d4ed8;
            /* hover:bg-blue-700 */
        }

        .crm-alert {
            margin: 1.5rem 2rem 0 2rem;
            padding: 1rem;
            border-left-width: 4px;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        .crm-alert-success {
            color: #166534;
            /* text-green-800 */
            background-color: #dcfce7;
            /* bg-green-100 */
            border-color: #22c55e;
            /* border-green-500 */
        }

        .crm-alert-error {
            color: #991b1b;
            /* text-red-800 */
            background-color: #fee2e2;
            /* bg-red-100 */
            border-color: #ef4444;
            /* border-red-500 */
        }

        .crm-empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background-color: #f9fafb;
        }

        /* Media Queries para Responsividade */
        @media (min-width: 640px) {

            /* sm */
            .crm-actions {
                flex-direction: row;
                width: auto;
            }
        }

        @media (min-width: 768px) {

            /* md */
            .crm-header {
                flex-direction: row;
                align-items: center;
            }
        }
    </style>
    <div class="crm-card">
        <!-- Cabeçalho da página -->
        <div class="crm-header">
            <h2 class="crm-title">
                <i class="bi bi-people-fill"></i>
                <span>Clientes Cadastrados</span>
            </h2>
            <div class="crm-actions">
                <a href="<?= htmlspecialchars($dashboardUrl ?? '../../index.php') ?>" class="crm-btn crm-btn-secondary">
                    <i class="bi bi-house-door-fill mr-2"></i> Voltar ao Início
                </a>
                <a href="index.php?controller=cliente&action=cadastrar" class="crm-btn crm-btn-primary">
                    <i class="bi bi-plus-circle mr-2"></i> Novo Cliente
                </a>
            </div>
        </div>

        <!-- Mensagens de Sucesso/Erro -->
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="crm-alert crm-alert-success">
                <i class="bi bi-check-circle-fill mr-2"></i> <?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
            </div>
            <?php unset($_SESSION['mensagem_sucesso']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
            <div class="crm-alert crm-alert-error">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i> <?= htmlspecialchars($_SESSION['mensagem_erro']); ?>
            </div>
            <?php unset($_SESSION['mensagem_erro']); ?>
        <?php endif; ?>

        <!-- Conteúdo Principal -->
        <div class="p-6 md:p-8">
            <?php if (empty($clientes)): ?>
                <div class="crm-empty-state">
                    <div class="inline-block bg-blue-100 text-blue-500 rounded-full p-4 mb-4">
                        <i class="bi bi-journal-x text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Nenhum Cliente Encontrado</h3>
                    <p class="text-gray-500 mt-2">Parece que ainda não há clientes cadastrados no sistema.</p>
                    <a href="index.php?controller=cliente&action=cadastrar" class="crm-btn crm-btn-primary mt-6">
                        <i class="bi bi-person-plus-fill mr-2"></i> Adicionar Primeiro Cliente
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                                <th scope="col" class="hidden lg:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPF</th>
                                <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empreendimento</th>
                                <th scope="col" class="p-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Classificação</th>
                                <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Corretor</th>
                                <?php if ($_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                    <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imobiliária</th>
                                <?php endif; ?>
                                <th scope="col" class="hidden lg:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
                                <th scope="col" class="p-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($clientes as $cliente): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <?php if (!empty($cliente['foto'])): ?>
                                                    <img class="h-10 w-10 rounded-full object-cover" src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold" style="display:none;"><?= strtoupper(substr($cliente['nome'], 0, 1)) ?></div>
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold"><?= strtoupper(substr($cliente['nome'], 0, 1)) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($cliente['nome']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['numero']) ?></td>
                                    <td class="hidden lg:table-cell p-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['cpf'] ?? '-') ?></td>
                                    <td class="hidden md:table-cell p-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['empreendimento'] ?? '-') ?></td>
                                    <td class="p-4 whitespace-nowrap text-center">
                                        <?php
                                        $tipoListaClass = 'bg-gray-100 text-gray-800'; // Padrão
                                        switch ($cliente['tipo_lista']) {
                                            case 'Potencial':
                                                $tipoListaClass = 'bg-green-100 text-green-800';
                                                break;
                                            case 'Não potencial':
                                                $tipoListaClass = 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $tipoListaClass ?>">
                                            <?= htmlspecialchars($cliente['tipo_lista'] ?? 'Neutro') ?>
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell p-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['nome_corretor'] ?? 'N/A') ?></td>
                                    <?php if ($_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                        <td class="hidden md:table-cell p-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td class="hidden lg:table-cell p-4 whitespace-nowrap text-sm text-gray-500">
                                        <div><?= isset($cliente['criado_em']) ? date('d/m/Y', strtotime($cliente['criado_em'])) : '-' ?></div>
                                        <div class="text-xs text-gray-400"><?= isset($cliente['criado_em']) ? date('H:i', strtotime($cliente['criado_em'])) : '' ?></div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= $cliente['id_cliente'] ?>" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                            <i class="bi bi-eye-fill"></i>
                                            <span class="hidden md:inline ml-1">Detalhes</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>