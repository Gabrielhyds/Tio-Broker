    <style>
        /* Estilo para um corpo de página mais suave */
        body {
            background-color: #f8fafc;
            /* bg-gray-50 */
        }

        /* Adiciona transições suaves para uma melhor experiência */
        a,
        button,
        [role="button"] {
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
        }

        .crm-title {
            font-size: 1.875rem;
            /* text-3xl */
            font-weight: 700;
            /* font-bold */
            color: #1f2937;
            /* text-gray-800 */
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .crm-title i {
            color: #16a34a;
            /* text-green-600 */
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

        .crm-btn-primary {
            color: #ffffff;
            background-color: #16a34a;
            /* bg-green-600 */
        }

        .crm-btn-primary:hover {
            background-color: #15803d;
            /* hover:bg-green-700 */
        }

        .crm-alert {
            margin: 0 2rem 1.5rem 2rem;
            padding: 1rem;
            border-left-width: 4px;
            border-radius: 0 0.5rem 0.5rem 0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .crm-alert-success {
            color: #166534;
            /* text-green-800 */
            background-color: #dcfce7;
            /* bg-green-100 */
            border-color: #22c55e;
            /* border-green-500 */
        }

        /* Animação para o modal */
        .modal-backdrop {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: all 0.3s ease;
        }

        /* Media Queries para Responsividade */
        @media (min-width: 768px) {

            /* md */
            .crm-header {
                flex-direction: row;
                align-items: center;
            }

            .crm-actions {
                width: auto;
            }
        }
    </style>
    <div class="crm-card">
        <!-- Cabeçalho da página -->
        <div class="crm-header">
            <h2 class="crm-title">
                <i class="fas fa-users"></i>
                <span>Usuários Cadastrados</span>
            </h2>
            <div class="crm-actions">
                <a href="cadastrar.php" class="crm-btn crm-btn-primary">
                    <i class="fas fa-plus mr-2"></i> Novo Usuário
                </a>
            </div>
        </div>

        <!-- Mensagem de sucesso -->
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="crm-alert crm-alert-success">
                <?= htmlspecialchars($_SESSION['sucesso']); ?>
            </div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <!-- Seção de Filtro e Conteúdo Principal -->
        <div class="p-6 md:p-8">
            <!-- Formulário de Filtro -->
            <form method="GET" action="listar.php" class="mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-grow">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="filtro" placeholder="Buscar por nome, email, permissão ou imobiliária..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            value="<?= htmlspecialchars($filtro) ?>">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex-grow md:flex-grow-0">
                            <i class="fas fa-search mr-1"></i> Buscar
                        </button>
                        <a href="listar.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300" title="Limpar Filtro">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabela de Usuários -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissão</th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imobiliária</th>
                            <th scope="col" class="p-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($lista)): ?>
                            <?php foreach ($lista as $u): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 whitespace-nowrap font-medium text-gray-900"><?= htmlspecialchars($u['nome']) ?></td>
                                    <td class="p-4 whitespace-nowrap text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="p-4 whitespace-nowrap text-gray-500"><?= htmlspecialchars($u['permissao']) ?></td>
                                    <td class="p-4 whitespace-nowrap text-gray-500"><?= htmlspecialchars($u['nome_imobiliaria'] ?? '---') ?></td>
                                    <td class="p-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="text-blue-600 hover:text-blue-900" title="Editar Usuário">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button onclick="openDeleteModal(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($u['nome']), ENT_QUOTES) ?>')" class="text-red-600 hover:text-red-900" title="Excluir Usuário">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-6">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="flex justify-center mt-6">
                    <nav class="flex space-x-2">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <a href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>"
                                class="px-4 py-2 rounded-md border text-sm <?= $i == $pagina_atual ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 bg-white hover:bg-gray-100 border-gray-300' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Exclusão -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden modal-backdrop" onclick="closeDeleteModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full modal-content" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-0 text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Excluir Usuário</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Tem certeza que deseja excluir o usuário <strong id="userNameToDelete"></strong>? Esta ação não pode ser desfeita.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                <a id="confirmDeleteButton" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:w-auto sm:text-sm">
                    Confirmar Exclusão
                </a>
                <button type="button" onclick="closeDeleteModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    <script>
        const deleteModal = document.getElementById('deleteModal');
        const userNameToDelete = document.getElementById('userNameToDelete');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');

        function openDeleteModal(userId, userName) {
            // Define o nome do usuário no modal
            userNameToDelete.textContent = userName;
            // Define o link de exclusão no botão de confirmação
            const deleteUrl = `../../controllers/UsuarioController.php?excluir=${userId}`;
            confirmDeleteButton.setAttribute('href', deleteUrl);
            // Exibe o modal
            deleteModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
        }
    </script>