<?php
require_once __DIR__ . '/../../config/rotas.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$usuarioLogado = $_SESSION['usuario'] ?? ['nome' => 'Usuário', 'email' => ''];
$primeiroNome = explode(' ', trim($usuarioLogado['nome']))[0] ?? 'Usuário';
$primeiraLetra = strtoupper(substr($primeiroNome, 0, 1));
$fotoPerfil = $usuarioLogado['foto'] ?? null;

$temFoto = !empty($fotoPerfil);
$caminhoFoto = BASE_URL . ltrim(str_replace('../', '', !empty($fotoPerfil) ? $fotoPerfil : ''), '/');
?>

<!-- A tag <header> define o cabeçalho da página. -->
<header class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
    <!-- Seção esquerda do cabeçalho. -->
    <div class="flex items-center space-x-4">
        <!-- Botão para abrir a barra lateral em telas pequenas (lg:hidden). -->
        <button id="open-sidebar-btn" class="lg:hidden text-gray-600 hover:text-gray-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <!-- Campo de busca, visível em telas médias e maiores (hidden md:block). -->
        <div class="relative hidden md:block">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Buscar..." class="w-64 lg:w-96 pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
        </div>
    </div>
    <!-- Seção direita do cabeçalho. -->
    <div class="flex items-center space-x-4">
        <!-- Botão de notificações. -->
        <button class="text-gray-500 hover:text-gray-800"><i class="fas fa-bell text-xl"></i></button>
        <!-- Menu de perfil do usuário. -->
        <div class="relative">
            <!-- Botão que exibe o avatar e abre o menu de perfil. -->
            <button id="profile-btn" class="flex items-center space-x-2">
                <!-- Se o usuário tiver uma foto, exibe a imagem. -->
                <?php if ($temFoto): ?>
                    <img
                        src="<?= htmlspecialchars($caminhoFoto) ?>"
                        alt="Avatar"
                        class="w-9 h-9 rounded-full object-cover bg-gray-100"
                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"> <!-- Se a imagem falhar ao carregar, esconde a tag <img> e mostra o fallback com a letra. -->
                    <!-- Fallback: exibe a primeira letra do nome se a imagem não carregar. -->
                    <div class="hidden w-9 h-9 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold">
                        <?= $primeiraLetra ?>
                    </div>
                    <!-- Se o usuário não tiver foto, exibe diretamente o fallback com a letra. -->
                <?php else: ?>
                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold">
                        <?= $primeiraLetra ?>
                    </div>
                <?php endif; ?>
            </button>
            <!-- O menu dropdown, inicialmente oculto. -->
            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-20">
                <!-- Seção com o nome e e-mail do usuário. -->
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($primeiroNome) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($usuarioLogado['email']) ?></p>
                </div>
                <!-- Divisor. -->
                <hr class="my-1">
                <!-- Links do menu. -->
                <a href="../usuarios/perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meu Perfil</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
                <!-- Link para o logout, usando a URL base definida em rotas.php. -->
                <a href="<?= BASE_URL ?>controllers/LogoutController.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sair</a>
            </div>
        </div>
    </div>
</header>