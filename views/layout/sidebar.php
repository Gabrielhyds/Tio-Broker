<?php
// Garante que uma sessão PHP seja iniciada, se ainda não houver uma ativa.
if (session_status() === PHP_SESSION_NONE) session_start();
// Obtém o nível de permissão do usuário da sessão para controle de acesso aos menus.
$permissao = $_SESSION['usuario']['permissao'] ?? '';

// Garante que a variável $activeMenu exista para evitar erros, definindo-a como vazia se não foi previamente declarada.
$activeMenu = $activeMenu ?? '';

// Inclui o arquivo que define constantes de rota, como a URL base do site.
require_once __DIR__ . '/../../config/rotas.php';
?>

<!-- A tag <aside> define um conteúdo lateral, ideal para uma barra de navegação. -->
<aside id="sidebar" class="w-64 flex-shrink-0 bg-white border-r border-gray-200 p-4 transform lg:transform-none lg:relative fixed h-full z-30 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <!-- Seção do logo e botão de fechar. -->
    <div class="flex items-center justify-between mb-8">
        <!-- Link no logo que leva para o dashboard principal. -->
        <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="flex items-center space-x-2">
            <i class="fas fa-chart-pie text-2xl text-blue-600"></i>
            <span class="text-xl font-bold text-gray-800">Tio Broker</span>
        </a>
        <!-- Botão para fechar a sidebar em telas pequenas (visível apenas em telas menores que 'lg'). -->
        <button id="close-sidebar-btn" class="lg:hidden text-gray-500 hover:text-gray-800">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navegação principal da sidebar, com rolagem vertical se o conteúdo for maior que a tela. -->
    <nav class="space-y-4 sidebar-scroll h-full pb-20 overflow-y-auto">
        <!-- Seção de menu: Início -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Início</h3>
            <!-- Link para o dashboard. A classe 'active' é adicionada dinamicamente se for o menu ativo. -->
            <a href="<?= BASE_URL ?>views/dashboards/dashboard_unificado.php" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-home w-6 text-center"></i><span class="ml-2">Resumo</span>
            </a>
        </div>

        <!-- Seção de menu: Imobiliária (visível apenas para 'SuperAdmin'). -->
        <?php if ($permissao === 'SuperAdmin'): ?>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Imobiliária</h3>
                <a href="<?= BASE_URL ?>views/imobiliarias/cadastrar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-plus w-6 text-center"></i><span class="ml-2">Cadastrar</span>
                </a>
                <a href="<?= BASE_URL ?>views/imobiliarias/listar_imobiliaria.php" class="sidebar-link <?= $activeMenu === 'imobiliaria_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-building w-6 text-center"></i><span class="ml-2">Ver Imobiliárias</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Seção de menu: Usuários (visível para 'SuperAdmin' e 'Admin'). -->
        <?php if (in_array($permissao, ['SuperAdmin', 'Admin'])): ?>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Usuário</h3>
                <a href="<?= BASE_URL ?>views/usuarios/cadastrar.php" class="sidebar-link <?= $activeMenu === 'usuario_cadastrar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-user-plus w-6 text-center"></i><span class="ml-2">Cadastrar Usuário</span>
                </a>
                <a href="<?= BASE_URL ?>views/usuarios/listar.php" class="sidebar-link <?= $activeMenu === 'usuario_listar' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-users w-6 text-center"></i><span class="ml-2">Gerenciar Usuários</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Seção de menu: Ferramentas (visível para todos os perfis). -->
        <?php if (in_array($permissao, ['SuperAdmin', 'Admin', 'Coordenador', 'Corretor'])): ?>
            <div>
                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ferramentas</h3>
                <a href="<?= BASE_URL ?>views/chat/chat.php" class="sidebar-link <?= $activeMenu === 'chat' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-comments w-6 text-center"></i><span class="ml-2">Chat</span>
                </a>
                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=listar" class="sidebar-link <?= $activeMenu === 'contatos' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-address-book w-6 text-center"></i><span class="ml-2">Agenda de Contatos</span>
                </a>
                <a href="#" class="sidebar-link <?= $activeMenu === 'tarefas' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-tasks w-6 text-center"></i><span class="ml-2">Tarefas</span>
                </a>
                <a href="<?= BASE_URL ?>views/agenda/agenda.php" class="sidebar-link <?= $activeMenu === 'agenda' ? 'active' : '' ?> flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-calendar-alt w-6 text-center"></i><span class="ml-2">Agenda</span>
                </a>
            </div>
        <?php endif; ?>
    </nav>
</aside>