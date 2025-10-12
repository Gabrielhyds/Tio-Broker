<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: sidebar.php (VERSÃO COM DROPDOWNS E MEMÓRIA DE SCROLL)
|--------------------------------------------------------------------------
| Itens de menu agrupados em dropdowns para melhor organização.
| Adicionado JavaScript para controlar a abertura/fechamento e memorizar
| a posição da barra de rolagem (scroll) entre as páginas.
*/

if (session_status() === PHP_SESSION_NONE) session_start();
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$activeMenu = $activeMenu ?? '';
require_once __DIR__ . '/../../config/rotas.php';

// --- Lógica para manter o dropdown ativo aberto ---
$leadSubMenu = ['lead_cadastrar', 'lead_listar'];
$imobiliariaSubMenu = ['imobiliaria_cadastrar', 'imobiliaria_listar'];
$usuarioSubMenu = ['usuario_cadastrar', 'usuario_listar'];
$ferramentasSubMenu = ['chat', 'contatos', 'tarefas', 'agenda'];
$empreendimentoSubMenu = ['empreendimento_cadastrar', 'empreendimento_listar'];
$imovelSubMenu = ['imovel_cadastrar', 'imovel_listar'];

$isLeadMenuActive = in_array($activeMenu, $leadSubMenu);
$isImobiliariaMenuActive = in_array($activeMenu, $imobiliariaSubMenu);
$isUsuarioMenuActive = in_array($activeMenu, $usuarioSubMenu);
$isFerramentasMenuActive = in_array($activeMenu, $ferramentasSubMenu);
$isEmpreendimentoMenuActive = in_array($activeMenu, $empreendimentoSubMenu);
$isImovelMenuActive = in_array($activeMenu, $imovelSubMenu);

?>
<aside id="sidebar" class="w-64 flex-shrink-0 bg-white border-r border-gray-200 p-4 transform lg:relative fixed h-full z-30 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div class="relative flex justify-center items-center mb-8">
        <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php">
            <img src="<?= BASE_URL ?>views/assets/img/tio_broker_ligth.png" alt="Logo Tio Broker" class="h-10 w-auto max-w-[160px] object-contain">
        </a>
        <button id="close-sidebar-btn" class="absolute right-0 top-1/2 -translate-y-1/2 lg:hidden text-gray-500 hover:text-gray-800">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <!-- ID 'sidebar-nav' adicionado para controlar o scroll -->
    <nav id="sidebar-nav" class="space-y-2 sidebar-scroll h-full pb-20 overflow-y-auto">
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 translating" data-i18n="sidebar.home.title">Início</h3>
            <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-home w-6 text-center"></i><span class="ml-2 translating" data-i18n="sidebar.home.summary">Resumo</span>
            </a>
        </div>
        
        <!-- Dropdown Leads -->
        <div hidden>
            <button type="button" class="dropdown-toggle flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-funnel-dollar w-6 text-center"></i>
                <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.lead.title">Leads</span>
                <i class="fas fa-chevron-down ml-auto transition-transform duration-200 <?= $isLeadMenuActive ? 'rotate-180' : '' ?>"></i>
            </button>
            <div class="dropdown-menu <?= $isLeadMenuActive ? '' : 'hidden' ?> py-1 space-y-1 pl-8">
                <a href="<?= BASE_URL ?>views/leads/cadastrar.php" class="sidebar-link <?= $activeMenu === 'lead_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-plus-circle w-6 text-center"></i>
                    <span class="ml-2 translating" data-i18n="sidebar.lead.register">Cadastrar Lead</span>
                </a>
                <a href="<?= BASE_URL ?>views/leads/listar.php" class="sidebar-link <?= $activeMenu === 'lead_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-tasks w-6 text-center"></i>
                    <span class="ml-2 translating" data-i18n="sidebar.lead.manage">Gerenciar Leads</span>
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

        <?php if (in_array($permissao, ['SuperAdmin', 'Admin', 'Coordenador', 'Corretor'])): ?>
            <!-- Dropdown Ferramentas -->
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
                    <span class="ml-2 flex-1 text-left whitespace-nowrap translating" data-i18n="sidebar.properties.title">Imóveis</span>
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
        <?php endif; ?>
    </nav>
</aside>

<!-- SCRIPT ATUALIZADO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Funcionalidade dos Dropdowns
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            const dropdownMenu = toggle.nextElementSibling;
            const chevron = toggle.querySelector('.fa-chevron-down');
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('hidden');
            }
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        });
    });

    // 2. Lógica para abrir e fechar a Sidebar (mobile e desktop)
    const sidebar = document.getElementById('sidebar');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');
    const openSidebarBtn = document.getElementById('open-sidebar-btn'); // Botão no header.php

    if (sidebar) {
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });
        }
        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
            });
        }
    }

    // 3. Lógica para manter a posição do scroll da Sidebar
    const sidebarNav = document.getElementById('sidebar-nav');
    if (sidebarNav) {
        // Ao carregar a página, restaura a posição do scroll
        const savedScrollPosition = sessionStorage.getItem('sidebarScrollPos');
        if (savedScrollPosition) {
            sidebarNav.scrollTop = parseInt(savedScrollPosition, 10);
        }

        // Antes de navegar para outra página, salva a posição do scroll
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
        });
    }
});
</script>

