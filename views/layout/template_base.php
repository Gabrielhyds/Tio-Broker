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
body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
.sidebar-scroll::-webkit-scrollbar { width:4px; }
.sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
.sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius:10px; }
.sidebar-link.active { background-color:#eff6ff;color:#2563eb;border-left:4px solid #2563eb; }
.translating { visibility:hidden; }
</style>
</head>
<body class="bg-slate-50">
<div class="flex h-screen bg-white">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include 'header.php'; ?>
        <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Tradução ---
    let translations = {};
    async function loadLayoutTranslations(lang) {
        try {
            const resp = await fetch(`<?= BASE_URL ?>controllers/TraducaoController.php?modulo=layout&lang=${lang}`);
            const result = await resp.json();
            if(result.success) translations = result.data;
        } catch(e) { console.error(e); }
    }
    function t(key){ return key.split('.').reduce((o,i)=>o && o[i],translations)||key; }
    function applyLayoutTranslations(){
        document.querySelectorAll('[data-i18n]').forEach(el=>{
            const k=el.dataset.i18n, trans=t(k);
            if(trans!==k){
                if(el.hasAttribute('placeholder')) el.placeholder=trans;
                else el.innerText=trans;
            }
            el.classList.remove('translating');
        });
    }

    // --- Seletores ---
    const sidebar = document.getElementById('sidebar');
    const sidebarBtn = document.getElementById('open-sidebar-btn');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const logoutOverlay = document.getElementById('logout-overlay');
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');
    const notificationBtn = document.getElementById('notification-btn');
    const notificationMenu = document.getElementById('notification-menu');
    const markAsReadBtn = document.getElementById('mark-all-as-read');

    // --- Fechar menus ao clicar fora ---
    document.addEventListener('click', (e)=>{
        if(profileMenu && profileBtn && !e.target.closest('#profile-menu') && !e.target.closest('#profile-btn')) profileMenu.classList.add('hidden');
        if(notificationMenu && notificationBtn && !e.target.closest('#notification-menu') && !e.target.closest('#notification-btn')) notificationMenu.classList.add('hidden');
        if(sidebar && sidebarBtn && window.innerWidth<1024 && !e.target.closest('#sidebar') && !e.target.closest('#open-sidebar-btn')) sidebar.classList.add('-translate-x-full');
    });

    // --- Botões ---
    profileBtn?.addEventListener('click', e=>{
        e.stopPropagation();
        profileMenu?.classList.toggle('hidden');
        notificationMenu?.classList.add('hidden');
    });

    notificationBtn?.addEventListener('click', e=>{
        e.stopPropagation();
        notificationMenu?.classList.toggle('hidden');
        profileMenu?.classList.add('hidden');
        if(!notificationMenu?.classList.contains('hidden')) carregarNotificacoes();
    });

    markAsReadBtn?.addEventListener('click', async e=>{
        e.preventDefault(); e.stopPropagation();
        try{
            const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php', { method:'POST' });
            const data = await resp.json();
            if(data.success) carregarNotificacoes();
            else console.error('Falha:', data.error);
        }catch(err){ console.error(err); }
    });

    sidebarBtn?.addEventListener('click', e=>{ e.stopPropagation(); sidebar?.classList.toggle('-translate-x-full'); });
    closeSidebarBtn?.addEventListener('click', ()=>{ sidebar?.classList.add('-translate-x-full'); });

    logoutBtn?.addEventListener('click', function(e){
        e.preventDefault();
        logoutOverlay?.classList.remove('hidden');
        setTimeout(()=>window.location.href=this.href, 1500);
    });

    // --- Função de Notificações ---
    async function carregarNotificacoes(){
        const notificationList = document.getElementById('notification-list');
        const notificationBadge = document.getElementById('notification-badge');
        if(!notificationList || !notificationBadge) return;
        try{
            const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php');
            const data = await resp.json();
            if(data.error) throw new Error(data.error);

            notificationBadge.textContent = data.count>0 ? data.count : '0';
            notificationBadge.classList.toggle('hidden', data.count<=0);

            notificationList.innerHTML='';
            if(data.items?.length>0){
                data.items.forEach(item=>{
                    const a=document.createElement('a');
                    a.href=item.link||'#';
                    a.className='block px-4 py-3 hover:bg-gray-100 border-b last:border-b-0';
                    a.innerHTML=`<p class="text-sm text-gray-800 truncate">${item.mensagem}</p>
                                   <p class="text-xs text-blue-500 mt-1">${item.data}</p>`;
                    notificationList.appendChild(a);
                });
            } else {
                notificationList.innerHTML='<div class="px-4 py-3 text-center text-sm text-gray-500" data-i18n="header.no_notifications">Nenhuma notificação nova.</div>';
            }
        }catch(err){ console.error(err); notificationList.innerHTML='<div class="px-4 py-3 text-center text-sm text-red-500">Erro ao carregar.</div>'; }
    }

    // --- Inicialização ---
    async function initializeLayout(){
        const lang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
        document.documentElement.lang = lang;
        localStorage.setItem('calendarLang', lang);
        await loadLayoutTranslations(lang);
        applyLayoutTranslations();
        carregarNotificacoes();
    }
    initializeLayout();
});
</script>
</body>
</html>
