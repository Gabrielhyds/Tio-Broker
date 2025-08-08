<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: template_base.php (VERSÃO CORRIGIDA)
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// **CORREÇÃO**: O caminho foi ajustado para subir dois níveis de diretório (de /views/layout/ para a raiz)
// e então entrar em /config/.
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.png">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background-color: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar-link.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-left: 4px solid #2563eb;
        }

        /* Esconde o texto que está aguardando tradução para evitar o "pisca-pisca" */
        .translating {
            visibility: hidden;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex h-screen bg-white">
        <?php include 'sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include 'header.php'; ?>
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                
                <!-- ✅ CORREÇÃO: A linha de inclusão dos alertas foi movida para o topo do conteúdo principal. -->
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
            let translations = {};

            async function loadLayoutTranslations(lang) {
                try {
                    const response = await fetch(`<?= BASE_URL ?>controllers/TraducaoController.php?modulo=layout&lang=${lang}`);
                    const result = await response.json();
                    if (result.success) {
                        translations = result.data;
                    }
                } catch (error) {
                    console.error('Failed to load layout translations:', error);
                }
            }

            function t(key) {
                return key.split('.').reduce((obj, i) => obj && obj[i], translations) || key;
            }

            function applyLayoutTranslations() {
                document.querySelectorAll('[data-i18n]').forEach(el => {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        if (el.hasAttribute('placeholder')) {
                            el.placeholder = translation;
                        } else {
                            el.innerText = translation;
                        }
                    }
                    el.classList.remove('translating');
                });
            }

            // ... (resto da sua lógica de interatividade do template) ...
            const profileBtn = document.getElementById('profile-btn');
            const profileMenu = document.getElementById('profile-menu');
            const sidebar = document.getElementById('sidebar');
            const sidebarBtn = document.getElementById('open-sidebar-btn');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');
            const logoutBtn = document.getElementById('logout-btn');
            const logoutOverlay = document.getElementById('logout-overlay');

            document.addEventListener('click', (e) => {
                if (profileMenu && !profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
                const isMobile = window.innerWidth < 1024;
                if (isMobile && sidebar && !sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            profileBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            sidebarBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            });
            closeSidebarBtn?.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });

            if (logoutBtn && logoutOverlay) {
                logoutBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    logoutOverlay.classList.remove('hidden');
                    const logoutUrl = this.href;
                    setTimeout(() => {
                        window.location.href = logoutUrl;
                    }, 1500);
                });
            }

            async function initializeLayout() {
                const lang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';
                document.documentElement.lang = lang;
                localStorage.setItem('calendarLang', lang);

                await loadLayoutTranslations(lang);
                applyLayoutTranslations();
            }

            initializeLayout();
        });
    </script>
</body>

</html>
