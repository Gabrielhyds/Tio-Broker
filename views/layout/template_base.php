<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/rotas.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tio Broker CRM PAINEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.png">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; /* bg-slate-50 */
            overscroll-behavior: none; /* Previne "pull-to-refresh" no mobile */
        }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        /* Estilo do link ativo da sidebar */
        .sidebar-link.active { 
            background-color: #eff6ff; /* bg-blue-50 */
            color: #2563eb; /* text-blue-600 */
            border-left: 4px solid #2563eb; /* border-blue-600 */
            font-weight: 500; /* medium */
        }
        /* Classe para esconder elementos durante a tradução */
        .translating { visibility: hidden; }

        /* Animação suave para a sidebar */
        #sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Overlay para o fundo do menu mobile */
        #sidebar-overlay {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50">

    <!-- Container Flexível de Tela Cheia -->
    <div class="flex h-screen overflow-hidden bg-white">
        
        <!-- Sidebar (Menu Lateral) -->
        <?php include 'sidebar.php'; ?>

        <!-- Overlay do Menu (Apenas Mobile) -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden"></div>

        <!-- Área de Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header (Cabeçalho) -->
            <?php include 'header.php'; ?>

            <!-- Conteúdo da Página (Rolável) -->
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6">
                <?php include '_alertas.php'; ?>
                <?php
                // Carrega o conteúdo dinâmico da página
                if (isset($conteudo) && file_exists($conteudo)) {
                    include $conteudo;
                } else {
                    echo "<div class='text-center text-red-600 p-8 bg-white rounded-lg shadow'>Erro: conteúdo não encontrado. Verifique o caminho.</div>";
                }
                ?>
            </main>
            
            <!-- Footer (Rodapé) -->
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <!-- 
    ==========================================================================
    SCRIPT UNIFICADO
    Todo o JavaScript foi movido para cá para evitar conflitos e
    garantir que todos os seletores sejam encontrados após o DOM carregar.
    ==========================================================================
    -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. SELETORES GLOBAIS ---
        // Layout
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const openSidebarBtn = document.getElementById('open-sidebar-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const sidebarNav = document.getElementById('sidebar-nav');
        
        // Header
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profile-menu');
        const notificationBtn = document.getElementById('notification-btn');
        const notificationMenu = document.getElementById('notification-menu');
        const markAsReadBtn = document.getElementById('mark-all-as-read');
        
        // Logout
        const logoutBtn = document.getElementById('logout-btn');
        const logoutOverlay = document.getElementById('logout-overlay');

        // Sidebar Dropdowns
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        const dropdownState = JSON.parse(sessionStorage.getItem('dropdownState') || '{}');


        // --- 2. LÓGICA DA SIDEBAR (MENU LATERAL) ---
        function openSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        }

        openSidebarBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            openSidebar();
        });
        
        closeSidebarBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebar();
        });
        
        sidebarOverlay?.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebar();
        });

        // --- 3. LÓGICA DOS DROPDOWNS DA SIDEBAR ---
        dropdownToggles.forEach((toggle, index) => {
            const dropdownMenu = toggle.nextElementSibling;
            const chevron = toggle.querySelector('.fa-chevron-down');

            // Aplica estado salvo (se houver)
            if (dropdownState[index]) {
                dropdownMenu?.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            }

            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                dropdownMenu?.classList.toggle('hidden');
                chevron?.classList.toggle('rotate-180');

                // Salva o estado atual
                dropdownState[index] = !dropdownMenu?.classList.contains('hidden');
                sessionStorage.setItem('dropdownState', JSON.stringify(dropdownState));
            });
        });

        // --- 4. MEMÓRIA DE SCROLL DA SIDEBAR ---
        if (sidebarNav) {
            // Restaura posição ao carregar
            const savedScrollPosition = sessionStorage.getItem('sidebarScrollPos');
            if (savedScrollPosition) {
                sidebarNav.scrollTop = parseInt(savedScrollPosition, 10);
            }
            // Salva posição ao descarregar
            window.addEventListener('beforeunload', () => {
                sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
            });
        }

        // --- 5. LÓGICA DOS MENUS DO HEADER (PERFIL E NOTIFICAÇÕES) ---
        profileBtn?.addEventListener('click', e => {
            e.stopPropagation();
            profileMenu?.classList.toggle('hidden');
            notificationMenu?.classList.add('hidden');
        });

        notificationBtn?.addEventListener('click', e => {
            e.stopPropagation();
            notificationMenu?.classList.toggle('hidden');
            profileMenu?.classList.add('hidden');
            // Carrega notificações se o menu for aberto
            if (!notificationMenu?.classList.contains('hidden')) {
                carregarNotificacoes();
            }
        });

        // --- 6. FECHAR MENUS AO CLICAR FORA ---
        document.addEventListener('click', (e) => {
            // Fecha perfil
            if (profileMenu && !profileMenu.classList.contains('hidden') && !e.target.closest('#profile-menu') && !e.target.closest('#profile-btn')) {
                profileMenu.classList.add('hidden');
            }
            // Fecha notificações
            if (notificationMenu && !notificationMenu.classList.contains('hidden') && !e.target.closest('#notification-menu') && !e.target.closest('#notification-btn')) {
                notificationMenu.classList.add('hidden');
            }
            // Fecha sidebar (apenas em telas < lg e se o clique não for na própria sidebar)
            if (window.innerWidth < 1024 && sidebar && !sidebar.classList.contains('-translate-x-full') && !e.target.closest('#sidebar') && !e.target.closest('#open-sidebar-btn')) {
                 // Esta lógica foi substituída pelo sidebarOverlay, mas mantida como fallback
            }
        });

        // --- 7. LÓGICA DE NOTIFICAÇÕES ---
        markAsReadBtn?.addEventListener('click', async e => {
            e.preventDefault(); 
            e.stopPropagation();
            try {
                const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php', { method: 'POST' });
                const data = await resp.json();
                if (data.success) {
                    carregarNotificacoes();
                } else {
                    console.error('Falha ao marcar como lidas:', data.error);
                }
            } catch (err) { console.error('Erro na requisição:', err); }
        });

        async function carregarNotificacoes() {
            const notificationList = document.getElementById('notification-list');
            const notificationBadge = document.getElementById('notification-badge');
            if (!notificationList || !notificationBadge) return;

            notificationList.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500">Carregando...</div>';

            try {
                const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php');
                const data = await resp.json();
                if (data.error) throw new Error(data.error);

                notificationBadge.textContent = data.count > 0 ? data.count : '0';
                notificationBadge.classList.toggle('hidden', data.count <= 0);

                notificationList.innerHTML = ''; // Limpa a lista
                if (data.items?.length > 0) {
                    data.items.forEach(item => {
                        const a = document.createElement('a');
                        a.href = item.link || '#';
                        a.className = 'block px-4 py-3 hover:bg-gray-100 border-b last:border-b-0 transition duration-150 ease-in-out';
                        a.innerHTML = `
                            <p class="text-sm font-medium text-gray-800 truncate">${item.mensagem}</p>
                            <p class="text-xs text-blue-500 mt-1">${item.data}</p>
                        `;
                        notificationList.appendChild(a);
                    });
                } else {
                    notificationList.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500" data-i18n="header.no_notifications">Nenhuma notificação nova.</div>';
                }
            } catch (err) {
                console.error('Erro ao carregar notificações:', err);
                notificationList.innerHTML = '<div class="px-4 py-3 text-center text-sm text-red-500">Erro ao carregar.</div>';
            }
        }

        // --- 8. LÓGICA DE LOGOUT ---
        logoutBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            logoutOverlay?.classList.remove('hidden');
            setTimeout(() => window.location.href = this.href, 1500);
        });

        // --- 9. LÓGICA DE TRADUÇÃO ---
        let translations = {};
        async function loadLayoutTranslations(lang) {
            try {
                const resp = await fetch(`<?= BASE_URL ?>controllers/TraducaoController.php?modulo=layout&lang=${lang}`);
                const result = await resp.json();
                if (result.success) translations = result.data;
            } catch (e) { console.error('Erro ao carregar traduções:', e); }
        }

        function t(key) {
            return key.split('.').reduce((o, i) => o && o[i], translations) || key;
        }

        function applyLayoutTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const k = el.dataset.i18n;
                const trans = t(k);
                if (trans !== k) {
                    if (el.hasAttribute('placeholder')) el.placeholder = trans;
                    else el.innerText = trans;
                }
                el.classList.remove('translating'); // Mostra o elemento
            });
        }

        // --- 10. INICIALIZAÇÃO ---
        async function initializeLayout() {
            const lang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
            document.documentElement.lang = lang;
            localStorage.setItem('calendarLang', lang);
            
            await loadLayoutTranslations(lang);
            applyLayoutTranslations();
            carregarNotificacoes();
        }

        // Inicia o layout
        initializeLayout();

        // --- 11. LÓGICA DO TEMPLATE (VISUALIZAÇÃO) ---
        // Isso controla o seletor de visualização Tabela/Cards
        const viewToggleList = document.getElementById('view-toggle-list');
        const viewToggleGrid = document.getElementById('view-toggle-grid');
        const cardView = document.getElementById('card-view');
        const tableView = document.getElementById('table-view');
        const allToggles = document.querySelectorAll('.view-toggle');

        function activateView(view) {
            // Remove a classe ativa de todos
            allToggles.forEach(btn => {
                btn.classList.remove('active-view', 'bg-blue-50', 'text-blue-600');
                btn.classList.add('text-gray-700', 'hover:bg-gray-100');
            });

            if (view === 'grid') {
                // Ativa modo Grid (Cards)
                cardView?.classList.remove('hidden');
                tableView?.classList.add('hidden');
                viewToggleGrid?.classList.add('active-view', 'bg-blue-50', 'text-blue-600');
                viewToggleGrid?.classList.remove('text-gray-700', 'hover:bg-gray-100');
                localStorage.setItem('crm_view_mode', 'grid'); // Salva preferência
            } else {
                // Ativa modo List (Tabela)
                cardView?.classList.add('hidden');
                tableView?.classList.remove('hidden');
                viewToggleList?.classList.add('active-view', 'bg-blue-50', 'text-blue-600');
                viewToggleList?.classList.remove('text-gray-700', 'hover:bg-gray-100');
                localStorage.setItem('crm_view_mode', 'list'); // Salva preferência
            }
        }

        // Adiciona eventos
        viewToggleList?.addEventListener('click', () => activateView('list'));
        viewToggleGrid?.addEventListener('click', () => activateView('grid'));

        // Verifica a preferência salva ao carregar a página
        const savedView = localStorage.getItem('crm_view_mode') || 'grid'; // 'grid' é o padrão
        activateView(savedView);

    });
    </script>

</body>
</html>