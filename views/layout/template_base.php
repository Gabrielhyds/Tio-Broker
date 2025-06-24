<!DOCTYPE html>
<!-- O elemento raiz da página, com o atributo 'lang' definindo o idioma como português do Brasil. -->
<html lang="pt-br">

<head>
    <!-- Define o conjunto de caracteres como UTF-8, que suporta a maioria dos caracteres e símbolos. -->
    <meta charset="UTF-8">
    <!-- Configura a viewport para garantir que a página seja renderizada corretamente em diferentes dispositivos. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Painel CRM - Tio Broker</title>
    <!-- Importa o Tailwind CSS via CDN para estilização rápida e utilitária. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Importa a biblioteca Font Awesome para usar ícones. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Pré-conecta com o servidor de fontes do Google para otimizar o carregamento. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Importa a fonte 'Inter' do Google Fonts. -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Importa a biblioteca Chart.js para criar gráficos. -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Inicia a seção de estilos CSS customizados para a página. -->
    <style>
        /* Estilos aplicados ao corpo da página. */
        body {
            font-family: 'Inter', sans-serif;
            /* Define a fonte padrão. */
            background-color: #f8fafc;
            /* Define uma cor de fundo cinza bem claro. */
        }

        /* Estilização da barra de rolagem da sidebar para um visual mais sutil. */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
            /* Largura da barra. */
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background-color: transparent;
            /* Fundo transparente. */
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            /* Cor do "polegar" da barra. */
            border-radius: 10px;
            /* Bordas arredondadas. */
        }

        /* Estilo para o link ativo na sidebar. */
        .sidebar-link.active {
            background-color: #eff6ff;
            /* Fundo azul claro. */
            color: #2563eb;
            /* Cor do texto azul. */
            border-left: 4px solid #2563eb;
            /* Borda esquerda azul para destaque. */
        }
    </style>
</head>

<!-- Corpo da página com uma cor de fundo padrão. -->

<body class="bg-slate-50">
    <!-- Contêiner principal usando Flexbox que ocupa toda a altura da tela. -->
    <div class="flex h-screen bg-white">
        <!-- Inclui o componente da barra lateral (sidebar.php). -->
        <?php include 'sidebar.php'; ?>
        <!-- Contêiner para o conteúdo principal, que cresce para preencher o espaço restante. -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Inclui o componente do cabeçalho (header.php). -->
            <?php include 'header.php'; ?>
            <!-- A área principal do conteúdo, com rolagem vertical e horizontal. -->
            <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                <?php
                // Lógica principal para carregar o conteúdo da página dinamicamente.
                if (isset($conteudo) && file_exists($conteudo)) {
                    // Se a variável $conteudo foi definida e o arquivo existe, ele é incluído aqui.
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    include $conteudo;
                } else {
                    // Se o arquivo de conteúdo não for encontrado, exibe uma mensagem de erro.
                    echo "<div class='text-center text-red-600'>Erro: conteúdo não encontrado.</div>";
                }
                ?>
            </main>
            <!-- Inclui o componente do rodapé (footer.php). -->
            <?php include 'footer.php'; ?>
        </div>
    </div>
    <!-- Bloco de script para interatividade da página. -->
    <script>
        // Executa o script quando o conteúdo da página estiver totalmente carregado.
        document.addEventListener('DOMContentLoaded', function() {
            // Obtém referências aos elementos interativos da página.
            const profileBtn = document.getElementById('profile-btn');
            const profileMenu = document.getElementById('profile-menu');
            const sidebar = document.getElementById('sidebar');
            const sidebarBtn = document.getElementById('open-sidebar-btn');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');

            // Adiciona um ouvinte de evento ao botão de perfil para mostrar/ocultar o menu.
            profileBtn?.addEventListener('click', (e) => {
                e.stopPropagation(); // Impede que o clique se propague para outros elementos.
                profileMenu.classList.toggle('hidden');
            });

            // Adiciona um ouvinte de evento global para fechar menus ao clicar fora deles.
            document.addEventListener('click', (e) => {
                // Fecha o menu de perfil se o clique for fora dele.
                if (profileMenu && !profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }

                // Fecha a sidebar em telas móveis se o clique for fora dela.
                const isMobile = window.innerWidth < 1024;
                if (isMobile && sidebar && !sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // Adiciona um ouvinte de evento ao botão de abrir a sidebar (ícone de hambúrguer).
            sidebarBtn?.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            });

            // Adiciona um ouvinte de evento ao botão de fechar a sidebar (ícone 'X').
            closeSidebarBtn?.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
            });
        });
    </script>
</body>

</html>