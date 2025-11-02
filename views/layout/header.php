<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: header.php
|--------------------------------------------------------------------------
| Versão corrigida com HTML limpo e JavaScript funcional.
| **FIX:** Alterado para 'window.load' para garantir que este script 
| rode por último e resolva conflitos.
*/
?>
<?php
require_once __DIR__ . '/../../config/rotas.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$usuarioLogado = $_SESSION['usuario'] ?? ['nome' => 'Usuário', 'email' => 'exemplo@email.com'];
$primeiroNome = explode(' ', trim($usuarioLogado['nome']))[0] ?? 'Usuário';
$primeiraLetra = strtoupper(substr($primeiroNome, 0, 1));
$fotoPerfil = $usuarioLogado['foto'] ?? null;
$temFoto = !empty($fotoPerfil);
$caminhoFoto = $temFoto ? BASE_URL . ltrim(str_replace('../', '', $fotoPerfil), '/') : '';
?>
<header class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
    <!-- Lado Esquerdo: Botão Sidebar e Busca -->
    <div class="flex items-center space-x-4">
        <button id="open-sidebar-btn" class="lg:hidden text-gray-600 hover:text-gray-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="relative hidden md:block">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" data-i18n="header.search" placeholder="Buscar..." class="w-64 lg:w-96 pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>
    </div>

    <!-- Lado Direito: Notificações e Perfil -->
    <div class="flex items-center space-x-6">
        
        <!-- Ícone de Notificação (Sino) -->
        <div class="relative">
            <button id="notification-btn" class="text-gray-500 hover:text-blue-500">
                <i class="fas fa-bell text-xl"></i>
                <!-- Badge (Contador) -->
                <span id="notification-badge" class="hidden absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                    0
                </span>
            </button>
            <!-- Menu Dropdown de Notificações -->
            <div id="notification-menu" class="hidden absolute right-0 mt-3 w-72 md:w-80 bg-white rounded-md shadow-lg py-1 z-20 transition-all duration-200 origin-top-right">
                <div class="px-4 py-3 flex justify-between items-center">
                    <p class="text-sm font-semibold text-gray-800" data-i18n="header.notifications">Notificações</p>
                    <button id="mark-all-as-read" class="text-xs text-blue-500 hover:underline" data-i18n="header.mark_all_read">Marcar todas como lidas</button>
                </div>
                <hr>
                <!-- Lista de Notificações (preenchida via JS) -->
                <div id="notification-list" class="max-h-64 overflow-y-auto">
                    <div class="px-4 py-3 text-center text-sm text-gray-500" data-i18n="header.no_notifications">
                        Nenhuma notificação nova.
                    </div>
                </div>
                <hr>
                <a href="#" class="block px-4 py-2 text-sm text-center text-blue-500 hover:bg-gray-100" data-i18n="header.all_notifications">
                    Ver todas
                </a>
            </div>
        </div>

        <!-- Ícone de Perfil -->
        <div class="relative">
            <button id="profile-btn" class="flex items-center space-x-2 cursor-pointer">
                <?php if ($temFoto): ?>
                    <img src="<?= htmlspecialchars($caminhoFoto) ?>" alt="Avatar" class="w-9 h-9 rounded-full object-cover bg-gray-200" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold"><?= $primeiraLetra ?></div>
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold"><?= $primeiraLetra ?></div>
                <?php endif; ?>
            </button>
            <!-- Menu Dropdown de Perfil -->
            <div id="profile-menu" class="hidden absolute right-0 mt-3 w-56 bg-white rounded-md shadow-lg py-1 z-20 transition-all duration-200 origin-top-right">
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($usuarioLogado['nome']) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($usuarioLogado['email']) ?></p>
                </div>
                <hr class="my-1">
                <a href="../usuarios/perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500" data-i18n="header.profile">Meu Perfil</a>
                <a href="../configuracao/configuracao.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500" data-i18n="header.settings">Configurações</a>
                <a id="logout-btn" href="<?= BASE_URL ?>controllers/LogoutController.php" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" data-i18n="header.logout">Sair</a>
            </div>
        </div>

    </div>
</header>

<!-- Overlay de Logout -->
<div id="logout-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 z-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl flex flex-col items-center space-y-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-cyan-500"></div>
        <p class="text-lg font-semibold text-gray-800" data-i18n="header.logging_out">Encerrando sessão...</p>
        <p class="text-sm text-gray-500" data-i18n="header.redirecting">Você será redirecionado em breve.</p>
    </div>
</div>

