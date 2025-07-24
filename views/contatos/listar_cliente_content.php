<?php
// Garante que a sessão seja iniciada para podermos acessar os dados do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Garante que a constante BASE_URL esteja disponível neste arquivo.
require_once __DIR__ . '/../../config/rotas.php';
?>
<style>
    /* Estilo para um corpo de página mais suave */
    body {
        background-color: #f8fafc;
    }

    /* Adiciona transições suaves para uma melhor experiência */
    a,
    button {
        transition: all 0.2s ease-in-out;
    }

    .crm-card {
        background-color: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
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
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .crm-title i {
        color: #2563eb;
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
        font-weight: 500;
        border-radius: 0.5rem;
        border: 1px solid transparent;
    }

    .crm-btn-secondary {
        border-color: #d1d5db;
        color: #374151;
        background-color: #ffffff;
    }

    .crm-btn-secondary:hover {
        background-color: #f9fafb;
    }

    .crm-btn-primary {
        color: #ffffff;
        background-color: #2563eb;
    }

    .crm-btn-primary:hover {
        background-color: #1d4ed8;
    }

    .crm-alert {
        margin: 1.5rem 2rem 0 2rem;
        padding: 1rem;
        border-left-width: 4px;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .crm-alert-success {
        color: #166534;
        background-color: #dcfce7;
        border-color: #22c55e;
    }

    .crm-alert-error {
        color: #991b1b;
        background-color: #fee2e2;
        border-color: #ef4444;
    }

    .crm-empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background-color: #f9fafb;
    }

    /* Esconde o texto que está aguardando tradução para evitar o "pisca-pisca" */
    .translating {
        visibility: hidden;
    }

    /* Media Queries para Responsividade */
    @media (min-width: 640px) {
        .crm-actions {
            flex-direction: row;
            width: auto;
        }
    }

    @media (min-width: 768px) {
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
            <i class="fas fa-address-book"></i> <!-- Ícone Corrigido -->
            <span class="translating" data-i18n="list.title">Clientes Cadastrados</span>
        </h2>
        <div class="crm-actions">
            <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=cadastrar" class="crm-btn crm-btn-primary">
                <i class="fas fa-plus-circle mr-2"></i> <span class="translating" data-i18n="list.newClient">Novo Cliente</span>
            </a>
        </div>
    </div>


    <!-- Conteúdo Principal -->
    <div class="p-6 md:p-8">
        <?php if (empty($clientes)): ?>
            <div class="crm-empty-state">
                <div class="inline-block bg-blue-100 text-blue-500 rounded-full p-4 mb-4">
                    <i class="fas fa-users-slash text-5xl"></i> <!-- Ícone Corrigido -->
                </div>
                <h3 class="text-xl font-semibold text-gray-800 translating" data-i18n="list.empty.title">Nenhum Cliente Encontrado</h3>
                <p class="text-gray-500 mt-2 translating" data-i18n="list.empty.message">Parece que ainda não há clientes cadastrados no sistema.</p>
                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=cadastrar" class="crm-btn crm-btn-primary mt-6">
                    <i class="fas fa-user-plus mr-2"></i> <span class="translating" data-i18n="list.empty.button">Adicionar Primeiro Cliente</span>
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.client">Cliente</th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.phone">Telefone</th>
                            <th scope="col" class="hidden lg:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.cpf">CPF</th>
                            <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.interest">Empreendimento</th>
                            <th scope="col" class="p-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.classification">Classificação</th>
                            <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.agent">Corretor</th>
                            <?php if ($_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                <th scope="col" class="hidden md:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.realty">Imobiliária</th>
                            <?php endif; ?>
                            <th scope="col" class="hidden lg:table-cell p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.date">Data Cadastro</th>
                            <th scope="col" class="p-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider translating" data-i18n="list.table.actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php if (!empty($cliente['foto'])): 
                                                // CORREÇÃO: Monta a URL usando apenas a BASE_URL, pois $cliente['foto'] já contém o caminho relativo.
                                                $caminhoFoto = BASE_URL . htmlspecialchars($cliente['foto']);
                                            ?>
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?= $caminhoFoto ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
                                    $isPotencial = ($cliente['tipo_lista'] ?? '') === 'Potencial';
                                    $tipoListaClass = $isPotencial ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    $tipoListaKey = $isPotencial ? 'common.potential' : 'common.notPotential';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $tipoListaClass ?> translating" data-i18n="<?= $tipoListaKey ?>">
                                        <?= htmlspecialchars($cliente['tipo_lista']) ?>
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
                                    <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=mostrar&id_cliente=<?= $cliente['id_cliente'] ?>" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                        <i class="fas fa-eye"></i> <!-- Ícone Corrigido -->
                                        <span class="hidden md:inline ml-1 translating" data-i18n="list.table.details">Detalhes</span>
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

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        let translations = {};

        function t(key, fallback = '') {
            return key.split('.').reduce((obj, i) => obj && obj[i], translations) || fallback || key;
        }

        function applyTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                // Não traduz novamente os elementos da sidebar, pois o template_base já fez isso.
                if (!el.closest('#sidebar')) {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        el.innerText = translation;
                    }
                }
                el.classList.remove('translating');
            });
        }

        async function loadContactTranslations(lang) {
            try {
                const response = await fetch(`../../controllers/TraducaoController.php?modulo=contatos&lang=${lang}`);
                const result = await response.json();
                if (result.success) {
                    translations = result.data;
                    applyTranslations();
                }
            } catch (error) {
                console.error('Failed to load contact translations:', error);
            }
        }

        const lang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
        await loadContactTranslations(lang);
    });
</script>
