<?php
require_once __DIR__ . '/../../config/rotas.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Define valores padrão para o caso de a sessão não existir.
$usuarioLogado = $_SESSION['usuario'] ?? ['nome' => 'Usuário', 'email' => 'exemplo@email.com'];
$primeiroNome = explode(' ', trim($usuarioLogado['nome']))[0] ?? 'Usuário';
$primeiraLetra = strtoupper(substr($primeiroNome, 0, 1));
$fotoPerfil = $usuarioLogado['foto'] ?? null;

$temFoto = !empty($fotoPerfil);
// Garante que o caminho da foto seja construído corretamente.
$caminhoFoto = $temFoto ? BASE_URL . ltrim(str_replace('../', '', $fotoPerfil), '/') : '';

?>

<!-- A tag <header> define o cabeçalho da página. -->
<header class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
    <!-- Seção esquerda do cabeçalho. -->
    <div class="flex items-center space-x-4">
        <!-- Botão para abrir a barra lateral em telas pequenas (lg:hidden). -->
        <button id="open-sidebar-btn" class="lg:hidden text-gray-600 hover:text-gray-800 transition-colors duration-200">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <!-- Campo de busca, visível em telas médias e maiores (hidden md:block). -->
        <div class="relative hidden md:block">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Buscar..." class="w-64 lg:w-96 pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
        </div>
    </div>
    <!-- Seção direita do cabeçalho. -->
    <div class="flex items-center space-x-6">
        <!-- Botão de notificações com efeito de hover. -->
        <button class="text-gray-500 hover:text-blue-500 transition-colors duration-200">
            <i class="fas fa-bell text-xl"></i>
        </button>

        <!-- Menu de perfil do usuário (Controlado por CSS com 'group' e 'group-hover') -->
        <div class="relative group">
            <!-- Botão que exibe o avatar e aciona o menu ao passar o mouse. -->
            <button class="flex items-center space-x-2 cursor-pointer">
                <!-- Se o usuário tiver uma foto, exibe a imagem. -->
                <?php if ($temFoto): ?>
                    <img
                        src="<?= htmlspecialchars($caminhoFoto) ?>"
                        alt="Avatar"
                        class="w-9 h-9 rounded-full object-cover bg-gray-200 ring-2 ring-offset-2 ring-transparent group-hover:ring-blue-500 transition-all"
                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"> <!-- Se a imagem falhar, mostra o fallback. -->
                    <!-- Fallback: exibe a primeira letra do nome com um gradiente. -->
                    <div class="hidden w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold ring-2 ring-offset-2 ring-transparent group-hover:ring-blue-500 transition-all">
                        <?= $primeiraLetra ?>
                    </div>
                <?php else: ?>
                    <!-- Se o usuário não tiver foto, exibe diretamente o fallback com a letra e gradiente. -->
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 text-white flex items-center justify-center font-bold ring-2 ring-offset-2 ring-transparent group-hover:ring-blue-500 transition-all">
                        <?= $primeiraLetra ?>
                    </div>
                <?php endif; ?>
            </button>

            <!-- O menu dropdown com animação aprimorada. -->
            <div class="absolute right-0 mt-3 w-56 bg-white rounded-md shadow-lg py-1 z-20
                        invisible opacity-0 transform scale-95 transition-all duration-200 origin-top-right
                        group-hover:visible group-hover:opacity-100 group-hover:scale-100">
                <!-- Seção com o nome e e-mail do usuário. -->
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($usuarioLogado['nome']) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($usuarioLogado['email']) ?></p>
                </div>
                <!-- Divisor. -->
                <hr class="my-1">
                <!-- Links do menu. -->
                <a href="../usuarios/perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500">Meu Perfil</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-500">Configurações</a>
                <!-- Link para o logout, com um ID para o JavaScript. -->
                <a id="logout-btn" href="<?= BASE_URL ?>controllers/LogoutController.php" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sair</a>
            </div>
        </div>
    </div>
</header>

<!-- Overlay de Carregamento para Logout -->
<div id="logout-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white p-8 rounded-lg shadow-xl flex flex-col items-center space-y-4 text-center">
        <!-- Spinner animado com nova cor. -->
        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-cyan-500"></div>
        <p class="text-lg font-semibold text-gray-800">Encerrando sessão...</p>
        <p class="text-sm text-gray-500">Você será redirecionado em breve.</p>
    </div>
</div>

<!-- JavaScript para interatividade dos componentes -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do DOM
        const logoutBtn = document.getElementById('logout-btn');
        const logoutOverlay = document.getElementById('logout-overlay');

        // --- LÓGICA DO MENU DE PERFIL REMOVIDA ---
        // O menu agora é controlado apenas por CSS (hover), simplificando o código.

        // --- Lógica para o Feedback de Logout ---
        if (logoutBtn && logoutOverlay) {
            logoutBtn.addEventListener('click', function(event) {
                // Previne a navegação imediata para a URL de logout
                event.preventDefault();

                // Mostra o overlay com uma transição suave
                logoutOverlay.classList.remove('hidden');

                // Pega a URL de logout do atributo href do link
                const logoutUrl = this.href;

                // Aguarda um tempo para que o usuário veja o feedback e então redireciona
                setTimeout(() => {
                    window.location.href = logoutUrl;
                }, 1500); // Delay de 1.5 segundos
            });
        }

        // --- Lógica para o Botão de Abrir Sidebar (exemplo) ---
        const openSidebarBtn = document.getElementById('open-sidebar-btn');
        const sidebar = document.getElementById('sidebar'); // Supondo que a sidebar exista

        if (openSidebarBtn && sidebar) {
            openSidebarBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full'); // Exemplo de classe para mostrar/esconder
            });
        }
    });
</script>