<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel CRM - Tio Broker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </style>
</head>

<body class="bg-slate-50">
    <div class="flex h-screen bg-white">
        <?php include 'sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include 'header.php'; ?>
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                <?php
                if (isset($conteudo) && file_exists($conteudo)) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
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
            const profileBtn = document.getElementById('profile-btn');
            const profileMenu = document.getElementById('profile-menu');
            const sidebar = document.getElementById('sidebar');
            const sidebarBtn = document.getElementById('open-sidebar-btn');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');

            // Toggle menu do perfil
            profileBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            // Fecha dropdown e sidebar ao clicar fora
            document.addEventListener('click', (e) => {
                if (profileMenu && !profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }

                const isMobile = window.innerWidth < 1024;
                if (isMobile && sidebar && !sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // Toggle sidebar
            sidebarBtn?.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            });

            // Fecha sidebar no botão X
            closeSidebarBtn?.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
            });
        });
    </script>
</body>

</html>