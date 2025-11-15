<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/rotas.php';

// =================================================================
// CORREÇÃO DO BUG DE SESSÃO (AQUI!)
// =================================================================
// Trocado de 'tema' para o caminho correto que o AuthController usa
$userTheme = $_SESSION['usuario']['configuracoes']['appearance']['theme'] ?? 'light';
?>
<!DOCTYPE html>
<!-- O 'class' aqui agora vai funcionar depois do login -->
<html lang="pt-br" class="<?= $userTheme === 'dark' ? 'dark' : '' ?>">
<head>
<meta charset="UTF-8">
<!-- ... (resto do <head> sem mudanças) ... -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tio Broker CRM PAINEL</title>

<!-- 1. O script do Tailwind (CDN) deve vir PRIMEIRO. -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- 2. O script de CONFIGURAÇÃO deve vir DEPOIS. -->
<script>
    tailwind.config = {
        darkMode: 'class'
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/x-icon" href="../assets/img/favicon.png">
<style>
/* ... (estilos CSS sem mudanças) ... */
body { font-family: 'Inter', sans-serif; }
.sidebar-scroll::-webkit-scrollbar { width:4px; }
.sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
.sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius:10px; }
.sidebar-link.active { 
    background-color:#eff6ff; 
    color:#2563eb; 
    border-left:4px solid #2563eb; 
}
.translating { visibility:hidden; }
#sidebar, #main-content-container {
    transition: width 0.3s ease, margin-left 0.3s ease;
}
body.sidebar-collapsed #sidebar { width: 5rem; }
body.sidebar-collapsed .sidebar-text,
body.sidebar-collapsed .logo-full,
body.sidebar-collapsed .fa-chevron-down {
    display: none;
}
body.sidebar-collapsed .logo-mini { display: block; }
body.sidebar-collapsed .sidebar-link,
body.sidebar-collapsed .dropdown-toggle {
    justify-content: center;
}
@media (min-width: 1024px) {
    body.sidebar-collapsed #main-content-container { margin-left: 5rem; }
    body:not(.sidebar-collapsed) #main-content-container { margin-left: 16rem; }
    body.sidebar-collapsed #header-container { width: calc(100% - 5rem); left: 5rem; }
    body:not(.sidebar-collapsed) #header-container { width: calc(100% - 16rem); left: 16rem; }
}
body.sidebar-collapsed .dropdown-menu { display: none !important; }
body.sidebar-collapsed .group:hover .sidebar-text {
    display: none; position: absolute; left: 100%;
    margin-left: 0.75rem; /* ml-3 */
    padding: 0.25rem 0.75rem; /* px-3 py-1 */
    background-color: #1f2937; /* bg-gray-800 */
    color: white;
    font-size: 0.75rem; /* text-xs */
    border-radius: 0.375rem; /* rounded-md */
    z-index: 50; white-space: nowrap; opacity: 0;
    pointer-events: none; transition: opacity 0.2s;
    display: block;
    opacity: 1;
}
html.dark .sidebar-link.active {
    background-color: rgba(59, 130, 246, 0.2); /* bg-blue-500/20 */
    color: #93c5fd; /* text-blue-300 */
    border-left-color: #93c5fd; /* border-blue-300 */
}
</style>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<!-- Script do Tema (lógica do DB, agora usa o $userTheme corrigido) -->
<script>
    const dbTheme = '<?= $userTheme ?>';
    if (dbTheme === 'dark') {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
</script>

</head>
<body class="bg-slate-50 dark:bg-gray-900">
<div class="flex h-screen bg-white dark:bg-gray-800">
    <?php include 'sidebar.php'; ?>
    
    <div id="main-content-container" class="flex-1 flex flex-col overflow-hidden lg:ml-64 dark:bg-gray-900">
        
        <?php include 'header.php'; ?>

        <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 dark:bg-gray-900 pt-16">
            <?php include '_alertas.php'; ?>
            <?php
            if (isset($conteudo) && file_exists($conteudo)) {
                include $conteudo;
            } else {
                echo "<div class='text-center text-red-600'>Erro: conteúdo não encontrado.</div>";
            }
            ?>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</div>

<!-- JAVASCRIPT UNIFICADO DO LAYOUT (Sem mudanças) -->
<script>
// ... (todo o script do DOMContentLoaded... sem mudanças) ...
document.addEventListener('DOMContentLoaded', function() {
    
    const body = document.body;

    // --- 1. LÓGICA DA SIDEBAR COLAPSÁVEL ---
// ... (código existente) ...
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const toggleIcon = toggleBtn?.querySelector('i');
    
    function setSidebarState(isCollapsed) {
// ... (código existente) ...
        body.classList.toggle('sidebar-collapsed', isCollapsed);
        if (isCollapsed) {
            toggleIcon?.classList.replace('fa-chevron-left', 'fa-chevron-right');
        } else {
            toggleIcon?.classList.replace('fa-chevron-right', 'fa-chevron-left');
        }
    }
    const initialState = localStorage.getItem('sidebarCollapsed') === 'true';
    setSidebarState(initialState);

    toggleBtn?.addEventListener('click', () => {
// ... (código existente) ...
        const isNowCollapsed = !body.classList.contains('sidebar-collapsed');
        setSidebarState(isNowCollapsed);
        localStorage.setItem('sidebarCollapsed', isNowCollapsed);
    });

    // --- 2. LÓGICA DO DARK MODE ---
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
// ... (código existente) ...
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    function syncThemeIcon() {
// ... (código existente) ...
        if (document.documentElement.classList.contains('dark')) {
            themeToggleDarkIcon?.classList.add('hidden');
            themeToggleLightIcon?.classList.remove('hidden');
        } else {
            themeToggleDarkIcon?.classList.remove('hidden');
            themeToggleLightIcon?.classList.add('hidden');
        }
    }
    
    async function salvarTemaNoBanco(tema) {
// ... (código existente) ...
        try {
            await fetch('<?= BASE_URL ?>api/salvar_tema.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tema: tema })
            });
        } catch (error) {
            console.error("Erro ao salvar tema:", error);
        }
    }

    themeToggleBtn?.addEventListener('click', () => {
// ... (código existente) ...
        document.documentElement.classList.toggle('dark');
        const novoTema = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', novoTema);
        syncThemeIcon();
        salvarTemaNoBanco(novoTema);
    });
    
    syncThemeIcon(); // Sincroniza no load

    // --- 3. LÓGICA DE TRADUÇÃO ---
    let translations = {};
// ... (código existente) ...
    async function loadLayoutTranslations(lang) {
        try {
            const resp = await fetch(`<?= BASE_URL ?>controllers/TraducaoController.php?modulo=layout&lang=${lang}`);
            const result = await resp.json();
            if(result.success) translations = result.data;
        } catch(e) { console.error(e); }
    }
    function t(key){ return key.split('.').reduce((o,i)=>o && o[i],translations)||key; }
    function applyLayoutTranslations(){
// ... (código existente) ...
        document.querySelectorAll('[data-i18n]').forEach(el=>{
            const k=el.dataset.i18n, trans=t(k);
            if(trans!==k){
                if(el.hasAttribute('placeholder')) el.placeholder=trans;
                else el.innerText=trans;
            }
            el.classList.remove('translating');
        });
    }

    // --- 4. SELETORES GERAIS E MENUS (HEADER/MOBILE) ---
    const sidebar = document.getElementById('sidebar');
// ... (código existente) ...
    const sidebarBtn = document.getElementById('open-sidebar-btn');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');
    const logoutBtn = document.getElementById('logout-btn');
// ... (código existente) ...
    const logoutOverlay = document.getElementById('logout-overlay');
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');
    const notificationBtn = document.getElementById('notification-btn');
    const notificationMenu = document.getElementById('notification-menu');
    const markAsReadBtn = document.getElementById('mark-all-as-read');

    document.addEventListener('click', (e)=>{
// ... (código existente) ...
        if(profileMenu && profileBtn && !e.target.closest('#profile-menu') && !e.target.closest('#profile-btn')) profileMenu.classList.add('hidden');
        if(notificationMenu && notificationBtn && !e.target.closest('#notification-menu') && !e.target.closest('#notification-btn')) notificationMenu.classList.add('hidden');
        if(sidebar && sidebarBtn && window.innerWidth < 1024 && !e.target.closest('#sidebar') && !e.target.closest('#open-sidebar-btn')) {
             sidebar.classList.add('-translate-x-full');
             body.classList.remove('sidebar-collapsed'); 
        }
    });

    profileBtn?.addEventListener('click', e=>{
// ... (código existente) ...
        e.stopPropagation();
        profileMenu?.classList.toggle('hidden');
        notificationMenu?.classList.add('hidden');
    });
    notificationBtn?.addEventListener('click', e=>{
// ... (código existente) ...
        e.stopPropagation();
        notificationMenu?.classList.toggle('hidden');
        profileMenu?.classList.add('hidden');
        if(!notificationMenu?.classList.contains('hidden')) carregarNotificacoes();
    });
    markAsReadBtn?.addEventListener('click', async e=>{
// ... (código existente) ...
        e.preventDefault(); e.stopPropagation();
        try{
            const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php', { method:'POST' });
            const data = await resp.json();
            if(data.success) carregarNotificacoes();
            else console.error('Falha:', data.error);
        }catch(err){ console.error(err); }
    });
    sidebarBtn?.addEventListener('click', e=>{ 
// ... (código existente) ...
        e.stopPropagation(); 
        sidebar?.classList.remove('-translate-x-full'); 
    });
    closeSidebarBtn?.addEventListener('click', ()=>{ 
// ... (código existente) ...
        sidebar?.classList.add('-translate-x-full'); 
    });
    logoutBtn?.addEventListener('click', function(e){
// ... (código existente) ...
        e.preventDefault();
        logoutOverlay?.classList.remove('hidden');
        setTimeout(()=>window.location.href=this.href, 1500);
    });

    // --- 5. FUNÇÃO DE NOTIFICAÇÕES (COM DARK MODE) ---
    async function carregarNotificacoes(){
// ... (código existente) ...
        const notificationList = document.getElementById('notification-list');
        const notificationBadge = document.getElementById('notification-badge');
        if(!notificationList || !notificationBadge) return;
// ... (código existente) ...
        try{
            const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php');
            const data = await resp.json();
// ... (código existente) ...
            if(data.error) throw new Error(data.error);

            notificationBadge.textContent = data.count>0 ? data.count : '0';
            notificationBadge.classList.toggle('hidden', data.count<=0);

            notificationList.innerHTML='';
// ... (código existente) ...
            if(data.items?.length>0){
                data.items.forEach(item=>{
                    const a=document.createElement('a');
// ... (código existente) ...
                    a.href=item.link||'#';
                    a.className='block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 border-b dark:border-gray-700 last:border-b-0';
                    a.innerHTML=`<p class="text-sm text-gray-800 dark:text-gray-200 truncate">${item.mensagem}</p>
                                   <p class="text-xs text-blue-500 mt-1">${item.data}</p>`;
                    notificationList.appendChild(a);
                });
            } else {
                notificationList.innerHTML='<div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400" data-i18n="header.no_notifications">Nenhuma notificação nova.</div>';
            }
        }catch(err){ console.error(err); notificationList.innerHTML='<div class="px-4 py-3 text-center text-sm text-red-500">Erro ao carregar.</div>'; }
    }

    // --- 6. LÓGICA DE DROPDOWN E SCROLL (CORRIGIDA) ---
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
// ... (código existente) ...
    const dropdownState = JSON.parse(sessionStorage.getItem('dropdownState') || '{}');

    dropdownToggles.forEach((toggle, index) => {
// ... (código existente) ...
        const dropdownMenu = toggle.nextElementSibling;
        const chevron = toggle.querySelector('.fa-chevron-down');

        if (dropdownState[index] && !body.classList.contains('sidebar-collapsed')) {
// ... (código existente) ...
            dropdownMenu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        }

        toggle.addEventListener('click', (event) => {
// ... (código existente) ...
            event.preventDefault();
            
            if (body.classList.contains('sidebar-collapsed')) {
                setSidebarState(false);
// ... (código existente) ...
                localStorage.setItem('sidebarCollapsed', false);

                dropdownMenu.classList.remove('hidden');
                chevron.classList.add('rotate-180');
                dropdownState[index] = true;
                sessionStorage.setItem('dropdownState', JSON.stringify(dropdownState));
            } else {
// ... (código existente) ...
                dropdownMenu.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
                dropdownState[index] = !dropdownMenu.classList.contains('hidden');
                sessionStorage.setItem('dropdownState', JSON.stringify(dropdownState));
            }
        });
    });

    const sidebarNav = document.getElementById('sidebar-nav');
// ... (código existente) ...
    if (sidebarNav) {
        const savedScrollPosition = sessionStorage.getItem('sidebarScrollPos');
        if (savedScrollPosition) {
            sidebarNav.scrollTop = parseInt(savedScrollPosition, 10);
// ... (código existente) ...
        }
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
        });
    }

    // --- 7. INICIALIZAÇÃO GERAL ---
// ... (código existente) ...
    async function initializeLayout(){
        const lang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
        document.documentElement.lang = lang;
        localStorage.setItem('calendarLang', lang);
// ... (código existente) ...
        await loadLayoutTranslations(lang);
        applyLayoutTranslations();
        carregarNotificacoes();
    }
    initializeLayout();
});
</script>
</body>
</html>