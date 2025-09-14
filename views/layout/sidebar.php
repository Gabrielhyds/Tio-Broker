<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: sidebar.php (VERSÃO CORRIGIDA)
|--------------------------------------------------------------------------
| Adicionada a classe 'translating' aos elementos de texto.
*/
?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$activeMenu = $activeMenu ?? '';
require_once __DIR__ . '/../../config/rotas.php';
?>
<aside id="sidebar" class="w-64 flex-shrink-0 bg-white border-r border-gray-200 p-4 transform lg:transform-none lg:relative fixed h-full z-30 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div class="flex items-center justify-center mb-8">
        <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="flex items-center space-x-2">
            <img src="<?= BASE_URL ?>views/assets/img/tio_broker_ligth.png" alt="Logo Tio Broker" class="h-10 w-auto max-w-[160px] object-contain"> 
        </a>
        <button id="close-sidebar-btn" class="lg:hidden text-gray-500 hover:text-gray-800">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <nav class="space-y-4 sidebar-scroll h-full pb-20 overflow-y-auto">
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.home.title">Início</h3>
            <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-home w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.home.summary">Resumo</span>
            </a>
        </div>
        <div hidden>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.lead.title">Leads</h3>
            
            <a href="<?= BASE_URL ?>views/leads/cadastrar.php" class="sidebar-link <?= $activeMenu === 'lead_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-plus-circle w-6 text-center"></i>
                <span class="ml-2 translating" data-i18n="sidebar.lead.register">Cadastrar Lead</span>
            </a>
            
            <a href="<?= BASE_URL ?>views/leads/listar.php" class="sidebar-link <?= $activeMenu === 'lead_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-funnel-dollar w-6 text-center"></i>
                <span class="ml-2 translating" data-i18n="sidebar.lead.manage">Gerenciar Leads</span>
            </a>
        </div>
        <?php if ($permissao === 'SuperAdmin'): ?>
            <div>

                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.realty.title">Imobiliária</h3>
                <a href="<?= BASE_URL ?>views/imobiliarias/cadastrar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.realty.register">Cadastrar</span>
                </a>
                <a href="<?= BASE_URL ?>views/imobiliarias/listar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-building w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.realty.view">Ver Imobiliárias</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (in_array($permissao, ['SuperAdmin', 'Admin','Coordenador'])): ?>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.user.title">Usuário</h3>
                <a href="<?= BASE_URL ?>views/usuarios/cadastrar.php" class="sidebar-link <?= $activeMenu === 'usuario_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-user-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.user.register">Cadastrar Usuário</span>
                </a>
                <a href="<?= BASE_URL ?>views/usuarios/listar.php" class="sidebar-link <?= $activeMenu === 'usuario_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-users w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.user.manage">Gerenciar Usuários</span>
                </a>
            </div>
        <?php endif; ?>

        <?php if (in_array($permissao, ['SuperAdmin', 'Admin', 'Coordenador', 'Corretor'])): ?>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.tools.title">Ferramentas</h3>
                <a href="<?= BASE_URL ?>views/chat/chat.php" class="sidebar-link <?= $activeMenu === 'chat' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-comments w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.chat">Chat</span>
                </a>
                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=listar" class="sidebar-link <?= $activeMenu === 'contatos' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-address-book w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.contacts">Agenda de Contatos</span>
                </a>
                <a href="<?= BASE_URL ?>views/tarefas/listar_tarefa.php" class="sidebar-link <?= $activeMenu === 'tarefas' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-tasks w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.tasks">Tarefas</span>
                </a>
                <a href="<?= BASE_URL ?>views/agenda/agenda.php" class="sidebar-link <?= $activeMenu === 'agenda' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-calendar-alt w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.schedule">Agenda</span>
                </a>
            </div>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.properties.title">Imóveis</h3>
                <a href="<?= BASE_URL ?>views/imoveis/cadastrar.php" class="sidebar-link <?= $activeMenu === 'imovel_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.properties.register">Cadastrar Imóvel</span>
                </a>
                <a href="<?= BASE_URL ?>views/imoveis/listar.php" class="sidebar-link <?= $activeMenu === 'imovel_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-home w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.properties.view">Ver Imóveis</span>
                </a>
            </div>
        <?php endif; ?>
    </nav>
</aside>