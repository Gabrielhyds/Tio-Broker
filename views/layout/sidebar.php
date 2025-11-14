<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: sidebar.php (VERSÃO REVISADA)
|--------------------------------------------------------------------------
| O <script> foi REMOVIDO e movido para 'index.php'.
| A estrutura HTML foi limpa e otimizada para responsividade.
*/

if (session_status() === PHP_SESSION_NONE) session_start();
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$activeMenu = $activeMenu ?? '';
require_once __DIR__ . '/../../config/rotas.php';

// --- Lógica para manter o dropdown ativo aberto ---
$leadSubMenu = ['lead_cadastrar', 'lead_listar','contatos'];
$imobiliariaSubMenu = ['imobiliaria_cadastrar', 'imobiliaria_listar'];
$usuarioSubMenu = ['usuario_cadastrar', 'usuario_listar'];
$ferramentasSubMenu = ['chat', 'tarefas', 'agenda'];
$empreendimentoSubMenu = ['empreendimento_cadastrar', 'empreendimento_listar'];
$imovelSubMenu = ['imovel_cadastrar', 'imovel_listar'];
$contratoSubMenu = ['contrato_cadastrar', 'listar_contratos', 'contrato_assinar'];

$emdesenvolviementoSubmenu = ['em_desenvolvimento'];
$isemdesenvolviementoMenuActive = in_array($activeMenu, $emdesenvolviementoSubmenu);

$isContratoMenuActive = in_array($activeMenu, $contratoSubMenu);
$isLeadMenuActive = in_array($activeMenu, $leadSubMenu);
$isImobiliariaMenuActive = in_array($activeMenu, $imobiliariaSubMenu);
$isUsuarioMenuActive = in_array($activeMenu, $usuarioSubMenu);
$isFerramentasMenuActive = in_array($activeMenu, $ferramentasSubMenu);
$isEmpreendimentoMenuActive = in_array($activeMenu, $empreendimentoSubMenu);
$isImovelMenuActive = in_array($activeMenu, $imovelSubMenu);
?>

<!-- 
    Sidebar:
    - 'fixed' e 'inset-y-0' para cobrir a altura total no mobile
    - 'z-40' para ficar acima do conteúdo mas abaixo do overlay (z-30)
    - '-translate-x-full' é o estado "fechado"
    - 'lg:relative' e 'lg:translate-x-0' para ficar fixo e visível no desktop
-->
<aside id="sidebar" class="w-64 flex-shrink-0 bg-white border-r border-gray-200 p-4 fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:relative lg:translate-x-0 lg:h-screen lg:sticky lg:top-0">
    
    <!-- Header da Sidebar -->
    <div class="relative flex justify-center items-center mb-6 pt-2">
        <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php">
            <img src="<?= BASE_URL ?>views/assets/img/tio_broker_ligth.png" alt="Logo Tio Broker" class="h-10 w-auto max-w-[160px] object-contain">
        </a>
        <!-- Botão de Fechar (Apenas Mobile) -->
        <button id="close-sidebar-btn" class="absolute right-0 top-1/2 -translate-y-1/2 lg:hidden text-gray-500 hover:text-gray-800 p-2">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navegação (Rolável) -->
    <!-- ID 'sidebar-nav' é usado pelo JS para memória de scroll -->
    <nav id="sidebar-nav" class_name="sidebar-scroll h-full pb-24 overflow-y-auto space-y-2">
        
        <!-- Bloco Início -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.home.title">Início</h3>
            <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-home w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.home.summary">Resumo</span>
            </a>
        </div>
        
        <!-- Dropdown Leads -->
        <div>
            <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-funnel-dollar w-6 text-center"></i>
                <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.lead.title">Leads</span>
                <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isLeadMenuActive ? 'rotate-180' : '' ?>"></i>
            </button>
            <div class="dropdown-menu <?= $isLeadMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                <a href="<?= BASE_URL ?>views/leads/cadastrar.php" class="sidebar-link <?= $activeMenu === 'lead_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-plus-circle w-6 text-center"></i>
                    <span class="ml-2 translating" data-i1imn="sidebar.lead.register">Cadastrar Lead</span>
                </a>
                <a href="<?= BASE_URL ?>views/leads/pipeline.php" class="sidebar-link <?= $activeMenu === 'lead_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-tasks w-6 text-center"></i>
                    <span class="ml-2 translating" data-i18n="sidebar.lead.manage">Gerenciar Leads</span>
                </a>
                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=listar" class="sidebar-link <?= $activeMenu === 'contatos' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-address-book w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.contacts">Agenda de Contatos</span>
                </a>
                <a href="<?= BASE_URL ?>views/relatorios/relatorio_leads.php" class="hidden sidebar-link <?= $activeMenu === 'relatorio_leads' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-chart-line w-6 text-center"></i>
                    <span class="ml-2 translating" data-i18n="sidebar.lead.reports">Relatórios de Leads</span>
                </a>
            </div>
        </div>

        <!-- Dropdown Imobiliária -->
        <?php if ($permissao === 'SuperAdmin'): ?>
            <div>
                <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-city w-6 text-center"></i>
                    <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.realty.title">Imobiliária</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isImobiliariaMenuActive ? 'rotate-180' : '' ?>"></i>
                </button>
                <div class="dropdown-menu <?= $isImobiliariaMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                    <a href="<?= BASE_URL ?>views/imobiliarias/cadastrar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.realty.register">Cadastrar</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/imobiliarias/listar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-building w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.realty.view">Ver Imobiliárias</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Dropdown Usuário -->
        <?php if (in_array($permissao, ['SuperAdmin', 'Admin','Coordenador'])): ?>
            <div>
                <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-users-cog w-6 text-center"></i>
                    <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.user.title">Usuário</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isUsuarioMenuActive ? 'rotate-180' : '' ?>"></i>
                </button>
                <div class="dropdown-menu <?= $isUsuarioMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                    <a href="<?= BASE_URL ?>views/usuarios/cadastrar.php" class="sidebar-link <?= $activeMenu === 'usuario_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-user-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.user.register">Cadastrar Usuário</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/usuarios/listar.php" class="sidebar-link <?= $activeMenu === 'usuario_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-users w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.user.manage">Gerenciar Usuários</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Dropdown Ferramentas -->
        <?php if (in_array($permissao, ['SuperAdmin', 'Admin', 'Coordenador', 'Corretor'])): ?>
            <div>
                <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-tools w-6 text-center"></i>
                    <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.tools.title">Ferramentas</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isFerramentasMenuActive ? 'rotate-180' : '' ?>"></i>
                </button>
                <div class="dropdown-menu <?= $isFerramentasMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                    <a href="<?= BASE_URL ?>views/chat/chat.php" class="sidebar-link <?= $activeMenu === 'chat' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-comments w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.chat">Chat</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/tarefas/listar_tarefa.php" class="sidebar-link <?= $activeMenu === 'tarefas' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-tasks w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.tasks">Tarefas</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/agenda/agenda.php" class="sidebar-link <?= $activeMenu === 'agenda' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-calendar-alt w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.tools.schedule">Agenda</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown Empreendimentos -->
             <div>
                <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-building w-6 text-center"></i>
                    <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.empreendimento.title">Empreendimentos</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isEmpreendimentoMenuActive ? 'rotate-180' : '' ?>"></i>
                </button>
                <div class="dropdown-menu <?= $isEmpreendimentoMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                    <a href="<?= BASE_URL ?>views/empreendimento/cadastrar.php" class="sidebar-link <?= $activeMenu === 'empreendimento_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.empreendimento.register">Cadastrar</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/empreendimento/listar.php" class="sidebar-link <?= $activeMenu === 'empreendimento_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-list-alt w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.empreendimento.view">Ver Empreendimentos</span>
                    </a>
                </div>
            </div>
            
            <!-- Dropdown Imóveis -->
            <div>
                <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-home w-6 text-center"></i>
                    <span class="ml-2 flex-1 text-left whitespace-nowtam-wrap translating" data-i18n="sidebar.properties.title">Imóveis</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isImovelMenuActive ? 'rotate-180' : '' ?>"></i>
                </button>
                <div class="dropdown-menu <?= $isImovelMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                     <a href="<?= BASE_URL ?>views/imoveis/cadastrar.php" class="sidebar-link <?= $activeMenu === 'imovel_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-plus w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.properties.register">Cadastrar Imóvel</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/imoveis/listar.php" class="sidebar-link <?= $activeMenu === 'imovel_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-list-alt w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.properties.view">Ver Imóveis</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown Contratos -->
             <div>
                <div>
                    <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-file-signature w-6 text-center"></i>
                        <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.contracts.title">Contratos</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isContratoMenuActive ? 'rotate-180' : '' ?>"></i>
                    </button>
                    <div class="dropdown-menu <?= $isContratoMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                        <a href="<?= BASE_URL ?>views/contratos/contrato.php" class="sidebar-link <?= $activeMenu === 'contrato_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-plus-circle w-6 text-center"></i>
                            <span class="ml-2 translating" data-i18n="sidebar.contracts.register">Cadastrar Contrato</span>
                        </a>
                        <a href="<?= BASE_URL ?>views/contratos/listar_contratos.php" class="sidebar-link <?= $activeMenu === 'listar_contratos' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-list-alt w-6 text-center"></i>
                            <span class="ml-2 translating" data-i18n="sidebar.contracts.view">Ver Contratos</span>
                        </a>
                        <a href="<?= BASE_URL ?>views/em_desenvolvimento/em_desenvolvimento.php" class="sidebar-link <?= $activeMenu === 'em_desenvolviemento' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-pen-nib w-6 text-center"></i>
                            <span class="ml-2 translating" data-i18n="sidebar.contracts.sign">Assinar (Teste)</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </nav>
</aside>