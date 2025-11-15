<?php
require_once __DIR__ . '/../../config/rotas.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$usuarioLogado = $_SESSION['usuario'] ?? ['nome' => 'Usuário', 'email' => 'email@example.com'];
$primeiroNome = explode(' ', trim($usuarioLogado['nome']))[0] ?? 'Usuário';
$primeiraLetra = strtoupper(substr($primeiroNome, 0, 1));
$fotoPerfil = $usuarioLogado['foto'] ?? null;
$temFoto = !empty($fotoPerfil);
$caminhoFoto = $temFoto ? BASE_URL . ltrim(str_replace('../', '', $fotoPerfil), '/') : '';
?>

<header 
    id="header-container"
    class="w-full h-16 fixed top-0 left-0 lg:left-64 z-40 
           flex items-center justify-between 
           px-4 bg-white border-b border-gray-200 
           dark:bg-gray-800 dark:border-gray-700 
           transition-all duration-300 ease-in-out">

    <!-- LADO ESQUERDO (HAMBURGUER MOBILE) -->
    <div class="flex-shrink-0 flex items-center">
        <button id="open-sidebar-btn" class="lg:hidden text-gray-600 hover:text-gray-800 dark:text-gray-400">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- CENTRO - CAMPO DE BUSCA (SOME NO MOBILE) -->
    <div class="flex-1 hidden md:flex justify-center px-4">
        <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
            <input 
                type="text" 
                placeholder="Buscar..."
                class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white 
                       dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400
                       dark:focus:ring-blue-500 transition">
        </div>
    </div>

    <!-- LADO DIREITO (ÍCONES) -->
    <div class="flex-shrink-0 flex items-center space-x-4 md:space-x-6">

        <!-- Botão Tema -->
        <button id="theme-toggle-btn" class="text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400">
            <i id="theme-toggle-dark-icon" class="fas fa-moon text-xl hidden"></i>
            <i id="theme-toggle-light-icon" class="fas fa-sun text-xl hidden"></i>
        </button>

        <!-- Notificações -->
        <div class="relative">
            <button id="notification-btn" class="text-gray-500 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-400">
                <i class="fas fa-bell text-xl"></i>
                <span id="notification-badge" class="hidden absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">0</span>
            </button>

            <!-- Dropdown de Notificações -->
            <div id="notification-menu" class="hidden absolute right-0 mt-3 w-72 md:w-80 bg-white rounded-md shadow-lg py-1 z-20 origin-top-right dark:bg-gray-800 dark:border dark:border-gray-700">
                <div class="px-4 py-3 flex justify-between items-center">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Notificações</p>
                    <button id="mark-all-as-read" class="text-xs text-blue-500 hover:underline">Marcar todas</button>
                </div>
                <hr class="dark:border-gray-700">

                <div id="notification-list" class="max-h-64 overflow-y-auto">
                    <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nenhuma notificação nova.
                    </div>
                </div>

                <hr class="dark:border-gray-700">

                <a href="<?= BASE_URL ?>views/notificacoes/todas.php" class="block px-4 py-2 text-sm text-center text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-blue-400">
                    Ver todas
                </a>
            </div>
        </div>

        <!-- Perfil -->
        <div class="relative">
            <button id="profile-btn" class="flex items-center space-x-2 cursor-pointer">
                <?php if ($temFoto): ?>
                    <img src="<?= htmlspecialchars($caminhoFoto) ?>" alt="Avatar" 
                         class="w-9 h-9 rounded-full object-cover bg-gray-200" 
                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold">
                        <?= $primeiraLetra ?>
                    </div>
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold">
                        <?= $primeiraLetra ?>
                    </div>
                <?php endif; ?>
            </button>

            <!-- Dropdown perfil -->
            <div id="profile-menu" class="hidden absolute right-0 mt-3 w-56 bg-white rounded-md shadow-lg py-1 z-20 origin-top-right dark:bg-gray-800 dark:border dark:border-gray-700">
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($usuarioLogado['nome']) ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($usuarioLogado['email']) ?></p>
                </div>

                <hr class="my-1 dark:border-gray-700">

                <a href="../usuarios/perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-blue-400">Meu Perfil</a>

                <a href="../configuracao/configuracao.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-blue-400">Configurações</a>

                <a id="logout-btn" href="<?= BASE_URL ?>controllers/LogoutController.php" 
                   class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-500 dark:hover:bg-red-900/30">
                   Sair
                </a>
            </div>
        </div>

    </div>
</header>

<!-- Overlay de Logout -->
<div id="logout-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 dark:bg-gray-900/80 z-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl flex flex-col items-center space-y-4 text-center dark:bg-gray-800">
        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-cyan-500"></div>
        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">Encerrando sessão...</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">Você será redirecionado em breve.</p>
    </div>
</div>
