<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin Responsivo com IA</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
  />

  <style>
    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #2d3748; /* bg-gray-800 */
    }
    ::-webkit-scrollbar-thumb {
      background: #4a5568; /* bg-gray-700 */
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #718096; /* bg-gray-600 */
    }

    body {
      font-family: 'Inter', 'Segoe UI', sans-serif;
      background-color: #f7fafc; /* bg-gray-100 */
      margin: 0;
      padding: 0;
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      min-width: 250px;
      height: 100vh;
      background-color: #1a202c; /* bg-gray-900 */
      color: #a0aec0; /* text-gray-400 */
      position: fixed;
      top: 0;
      left: 0;
      z-index: 50;
      overflow-y: auto;
      transition: width 0.3s ease, min-width 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
      padding-top: 1rem;
    }

    .sidebar.minimized {
      width: 70px;
      min-width: 70px;
    }

    .sidebar.minimized .sidebar-header h4,
    .sidebar.minimized .nav-link span,
    .sidebar.minimized .submenu-title,
    .sidebar.minimized hr:not(.hr-condensed) {
      display: none;
    }

    .sidebar.minimized .nav-link i {
      margin-right: 0;
      font-size: 1.25rem;
    }
    .sidebar.minimized .nav-link {
        justify-content: center;
    }

    .sidebar.minimized .submenu-item {
        padding-left: 0;
        justify-content: center;
    }
    .sidebar.minimized .submenu-item i {
        margin-right: 0;
    }

    .sidebar.minimized .hr-condensed {
        display: block;
        margin: 0.5rem 0.5rem;
    }

    .sidebar-header {
      padding: 0 1rem;
      margin-bottom: 0.5rem;
    }
    .sidebar-header h4 {
      color: #e2e8f0; /* text-gray-200 */
      text-align: center;
      margin-bottom: 0.75rem;
      font-size: 1.25rem;
      font-weight: 600;
    }

    .sidebar hr {
      border-color: #4a5568; /* border-gray-700 */
      margin: 0.75rem 1rem;
    }
    .sidebar .hr-condensed {
        display: none;
    }

    .sidebar .nav-list {
        padding: 0 0.5rem;
    }
    .sidebar .nav-link {
      color: #a0aec0; /* text-gray-400 */
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
      font-size: 0.95rem;
      border-radius: 0.375rem; /* rounded-md */
      margin-bottom: 0.25rem;
      transition: background-color 0.2s ease, color 0.2s ease;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #2d3748; /* bg-gray-800 */
      color: #e2e8f0; /* text-gray-200 */
    }
    .sidebar .nav-link i {
      width: 1.5rem;
      text-align: center;
      margin-right: 0.75rem;
      color: #718096; /* text-gray-500 */
      font-size: 1rem;
      transition: color 0.2s ease;
    }
    .sidebar .nav-link:hover i,
    .sidebar .nav-link.active i {
      color: #e2e8f0; /* text-gray-200 */
    }

    .sidebar .submenu-title {
      font-size: 0.8rem;
      text-transform: uppercase;
      padding: 0.75rem 1rem;
      color: #718096; /* text-gray-500 */
      margin-top: 1rem;
      letter-spacing: 0.05em;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .sidebar .submenu-item {
      padding-left: 2.75rem;
    }

    /* Main Content Area - Wrapper for dynamic content */
    .main-content-wrapper {
      flex-grow: 1;
      margin-left: 250px;
      padding: 1.5rem;
      transition: margin-left 0.3s ease;
      width: calc(100% - 250px);
      overflow-y: auto;
      height: 100vh;
    }

    .main-content-wrapper.sidebar-minimized {
      margin-left: 70px;
      width: calc(100% - 70px);
    }



    /* Modal styles */
    .modal {
        transition: opacity 0.25s ease;
    }
    .modal-active {
        overflow-x: hidden;
        overflow-y: auto;
    }
    .modal-content {
        max-height: 80vh; /* Limit modal height */
        overflow-y: auto;
    }
    /* Loading spinner */
    .spinner {
        border-top-color: #3498db; /* Blue */
        animation: spinner 1.5s linear infinite;
    }
    @keyframes spinner {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


 
  </style>
</head>
<body>

<button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle sidebar">
  <i id="menuIcon" class="fas fa-chevron-left"></i>
</button>

<?php
// Incluir o dashboard de acordo com o perfil do usuário (Navbar lateral ou topo, conforme seu layout)
if ($_SESSION['usuario']['permissao'] === 'SuperAdmin') {
    include_once 'dashboard_superadmin.php';
} elseif ($_SESSION['usuario']['permissao'] === 'Admin') {
    include_once 'dashboard_admin.php';
} elseif ($_SESSION['usuario']['permissao'] === 'Coordenador') {
    include_once 'dashboard_coordenador.php';
} else {
    include_once 'dashboard_corretor.php';
}
?>

<div id="mainContentWrapper" class="main-content-wrapper">
  
  <header class="topbar">
    <h1 class="topbar-title">Relatório Geral da Plataforma</h1>
    <input type="search" class="form-control hidden sm:block" placeholder="Pesquisar...">
  </header>

  <main class="content" id="pageContent"> <div class="mb-6 flex justify-end">
        <button id="generateSummaryBtn" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
            <i class="fas fa-lightbulb mr-2"></i> Gerar Resumo Inteligente ✨
        </button>
    </div>

    <div id="statsCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-blue-500 p-3 rounded-full">
          <i class="fas fa-building fa-2x text-white"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500" data-stat-name="Total de Imobiliárias">Total de Imobiliárias</p>
          <p class="text-2xl font-semibold text-gray-800" data-stat-value="125">125</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-green-500 p-3 rounded-full">
          <i class="fas fa-users fa-2x text-white"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500" data-stat-name="Total de Usuários">Total de Usuários</p>
          <p class="text-2xl font-semibold text-gray-800" data-stat-value="873">873</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-yellow-500 p-3 rounded-full">
          <i class="fas fa-home fa-2x text-white"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500" data-stat-name="Imóveis Cadastrados">Imóveis Cadastrados</p>
          <p class="text-2xl font-semibold text-gray-800" data-stat-value="5420">5,420</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
        <div class="bg-red-500 p-3 rounded-full">
          <i class="fas fa-hand-holding-usd fa-2x text-white"></i>
        </div>
        <div>
          <p class="text-sm text-gray-500" data-stat-name="Vendas no Mês">Vendas no Mês</p>
          <p class="text-2xl font-semibold text-gray-800" data-stat-value="R$ 1.2M">R$ 1.2M</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Crescimento de Usuários (Últimos 6 Meses)</h3>
        <div class="bg-gray-200 h-64 rounded-md flex items-center justify-center">
          <p class="text-gray-500">[Placeholder para Gráfico de Linha]</p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Distribuição de Imóveis</h3>
        <div class="bg-gray-200 h-64 rounded-md flex items-center justify-center">
          <p class="text-gray-500">[Placeholder para Gráfico de Pizza]</p>
        </div>
      </div>
    </div>

    <div id="recentActivity" class="bg-white p-6 rounded-xl shadow-lg">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Atividades Recentes</h3>
      <ul class="space-y-4">
        <li class="flex items-center space-x-3 pb-3 border-b border-gray-200 last:border-b-0" data-activity="Novo usuário Carlos Silva cadastrado. (Há 15 minutos)">
          <div class="bg-blue-100 p-2 rounded-full"><i class="fas fa-user-plus text-blue-500"></i></div>
          <div>
            <p class="text-sm text-gray-700">Novo usuário <span class="font-semibold">Carlos Silva</span> cadastrado.</p>
            <p class="text-xs text-gray-500">Há 15 minutos</p>
          </div>
        </li>
        <li class="flex items-center space-x-3 pb-3 border-b border-gray-200 last:border-b-0" data-activity="Imobiliária Horizonte Ltda atualizou seu perfil. (Há 1 hora)">
          <div class="bg-green-100 p-2 rounded-full"><i class="fas fa-building text-green-500"></i></div>
          <div>
            <p class="text-sm text-gray-700">Imobiliária <span class="font-semibold">Horizonte Ltda</span> atualizou seu perfil.</p>
            <p class="text-xs text-gray-500">Há 1 hora</p>
          </div>
        </li>
        <li class="flex items-center space-x-3 pb-3 border-b border-gray-200 last:border-b-0" data-activity="Novo imóvel Apartamento Centro adicionado por Imobiliária Sol Nascente. (Há 3 horas)">
          <div class="bg-yellow-100 p-2 rounded-full"><i class="fas fa-home text-yellow-500"></i></div>
          <div>
            <p class="text-sm text-gray-700">Novo imóvel <span class="font-semibold">Apartamento Centro</span> adicionado por <span class="font-semibold">Imobiliária Sol Nascente</span>.</p>
            <p class="text-xs text-gray-500">Há 3 horas</p>
          </div>
        </li>
         <li class="flex items-center space-x-3" data-activity="Venda registrada para o imóvel Casa Lagoa. (Ontem)">
          <div class="bg-red-100 p-2 rounded-full"><i class="fas fa-file-invoice-dollar text-red-500"></i></div>
          <div>
            <p class="text-sm text-gray-700">Venda registrada para o imóvel <span class="font-semibold">Casa Lagoa</span>.</p>
            <p class="text-xs text-gray-500">Ontem</p>
          </div>
        </li>
      </ul>
    </div>
  </main>
  </div>

<div id="summaryModal" class="modal fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 hidden opacity-0">
    <div class="modal-content bg-white p-6 sm:p-8 rounded-xl shadow-2xl w-11/12 md:w-2/3 lg:w-1/2 transform transition-all scale-95">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Resumo Inteligente ✨</h2>
            <button id="closeSummaryModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <div id="summaryLoading" class="flex flex-col items-center justify-center min-h-[200px]">
            <div class="spinner w-12 h-12 rounded-full border-4 border-gray-300"></div>
            <p class="mt-4 text-gray-600">Gerando resumo... Por favor, aguarde.</p>
        </div>
        <div id="summaryResult" class="text-gray-700 leading-relaxed hidden prose max-w-none">
            </div>
         <div id="summaryError" class="text-red-600 bg-red-100 p-4 rounded-md hidden">
            Ocorreu um erro ao gerar o resumo. Tente novamente mais tarde.
        </div>
        <div class="mt-6 flex justify-end">
            <button id="copySummaryBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg mr-2 hidden">
                <i class="fas fa-copy mr-2"></i> Copiar Resumo
            </button>
            <button id="closeSummaryModalBtnBottom" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">
                Fechar
            </button>
        </div>
    </div>
</div>


<div id="mobileOverlay" class="mobile-overlay"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContentWrapper = document.getElementById('mainContentWrapper');
    const menuIcon = document.getElementById('menuIcon');
    const mobileOverlay = document.getElementById('mobileOverlay');

    const generateSummaryBtn = document.getElementById('generateSummaryBtn');
    const summaryModal = document.getElementById('summaryModal');
    const closeSummaryModal = document.getElementById('closeSummaryModal');
    const closeSummaryModalBtnBottom = document.getElementById('closeSummaryModalBtnBottom');
    const summaryLoading = document.getElementById('summaryLoading');
    const summaryResult = document.getElementById('summaryResult');
    const summaryError = document.getElementById('summaryError');
    const copySummaryBtn = document.getElementById('copySummaryBtn');

    const isSmallScreen = () => window.innerWidth <= 768;
    let isSidebarMinimized = localStorage.getItem('sidebarMinimized') === 'true';
    let isMobileSidebarOpen = false;

    function applySidebarState() {
      if (isSmallScreen()) {
        sidebarToggle.classList.remove('minimized');
        menuIcon.className = 'fas fa-bars';
        if (isMobileSidebarOpen) {
          sidebar.classList.add('open');
          sidebarToggle.classList.add('minimized');
          menuIcon.className = 'fas fa-times';
          mobileOverlay.style.display = 'block';
        } else {
          sidebar.classList.remove('open');
          sidebarToggle.classList.remove('minimized');
          menuIcon.className = 'fas fa-bars';
          mobileOverlay.style.display = 'none';
        }
        mainContentWrapper.classList.remove('sidebar-minimized');
        sidebar.classList.remove('minimized');
        sidebarToggle.style.left = '0.5rem';
      } else {
        mobileOverlay.style.display = 'none';
        sidebar.classList.remove('open');
        if (isSidebarMinimized) {
          sidebar.classList.add('minimized');
          mainContentWrapper.classList.add('sidebar-minimized');
          sidebarToggle.classList.add('minimized');
          menuIcon.className = 'fas fa-chevron-right';
          sidebarToggle.style.left = '70px';
        } else {
          sidebar.classList.remove('minimized');
          mainContentWrapper.classList.remove('sidebar-minimized');
          sidebarToggle.classList.remove('minimized');
          menuIcon.className = 'fas fa-chevron-left';
          sidebarToggle.style.left = '250px';
        }
      }
    }

    sidebarToggle.addEventListener('click', function () {
      if (isSmallScreen()) {
        isMobileSidebarOpen = !isMobileSidebarOpen;
      } else {
        isSidebarMinimized = !isSidebarMinimized;
        localStorage.setItem('sidebarMinimized', isSidebarMinimized);
      }
      applySidebarState();
    });

    mobileOverlay.addEventListener('click', function() {
        if (isSmallScreen() && isMobileSidebarOpen) {
            isMobileSidebarOpen = false;
            applySidebarState();
        }
    });

    window.addEventListener('resize', applySidebarState);
    applySidebarState();

    // --- Lógica do Modal e API Gemini ---
    function openModal() {
        summaryModal.classList.remove('hidden');
        setTimeout(() => summaryModal.classList.remove('opacity-0'), 10);
        setTimeout(() => summaryModal.querySelector('.modal-content').classList.remove('scale-95'), 10);
        document.body.classList.add('modal-active');
    }

    function closeModal() {
        summaryModal.querySelector('.modal-content').classList.add('scale-95');
        summaryModal.classList.add('opacity-0');
        setTimeout(() => {
            summaryModal.classList.add('hidden');
            document.body.classList.remove('modal-active');
            summaryLoading.style.display = 'flex';
            summaryResult.style.display = 'none';
            summaryResult.innerHTML = '';
            summaryError.style.display = 'none';
            copySummaryBtn.style.display = 'none';
        }, 250);
    }

    if(generateSummaryBtn) { // Verifica se o botão existe na página carregada
        generateSummaryBtn.addEventListener('click', async function() {
            openModal();
            summaryLoading.style.display = 'flex';
            summaryResult.style.display = 'none';
            summaryError.style.display = 'none';
            copySummaryBtn.style.display = 'none';

            let reportData = "Dados do Relatório da Plataforma:\n\nEstatísticas Principais:\n";
            // Coleta dados dos stats cards DENTRO da área de conteúdo atual (#pageContent)
            const statElements = document.querySelectorAll('#pageContent #statsCards [data-stat-name]');
            statElements.forEach(el => {
                const name = el.getAttribute('data-stat-name');
                const value = el.nextElementSibling.getAttribute('data-stat-value');
                reportData += `- ${name}: ${value}\n`;
            });

            reportData += "\nAtividades Recentes:\n";
            // Coleta dados das atividades recentes DENTRO da área de conteúdo atual (#pageContent)
            const activityElements = document.querySelectorAll('#pageContent #recentActivity [data-activity]');
            activityElements.forEach(el => {
                reportData += `- ${el.getAttribute('data-activity')}\n`;
            });

            const prompt = `Por favor, gere um resumo conciso e informativo em português brasileiro sobre o estado atual da plataforma, com base nos seguintes dados:\n\n${reportData}\n\nDestaque os pontos mais importantes e forneça uma visão geral.`;
            const apiKey = ""; // Chave será injetada pelo Canvas
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`;
            let chatHistory = [{ role: "user", parts: [{ text: prompt }] }];
            const payload = { contents: chatHistory };

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Erro da API Gemini:', errorData);
                    throw new Error(`Erro na API: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.candidates && result.candidates.length > 0 &&
                    result.candidates[0].content && result.candidates[0].content.parts &&
                    result.candidates[0].content.parts.length > 0) {
                    const summaryText = result.candidates[0].content.parts[0].text;
                    summaryResult.innerHTML = summaryText.replace(/\n/g, '<br>');
                    summaryResult.style.display = 'block';
                    copySummaryBtn.style.display = 'inline-flex';
                } else {
                    console.error('Resposta inesperada da API Gemini:', result);
                    throw new Error('Formato de resposta inesperado.');
                }
            } catch (error) {
                console.error('Falha ao gerar resumo:', error);
                summaryError.textContent = `Ocorreu um erro ao gerar o resumo: ${error.message}. Tente novamente.`;
                summaryError.style.display = 'block';
            } finally {
                summaryLoading.style.display = 'none';
            }
        });
    }


    closeSummaryModal.addEventListener('click', closeModal);
    closeSummaryModalBtnBottom.addEventListener('click', closeModal);
    summaryModal.addEventListener('click', function(event) {
        if (event.target === summaryModal) {
            closeModal();
        }
    });

    copySummaryBtn.addEventListener('click', function() {
        const textToCopy = summaryResult.innerText;
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            copySummaryBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Copiado!';
            setTimeout(() => {
                 copySummaryBtn.innerHTML = '<i class="fas fa-copy mr-2"></i> Copiar Resumo';
            }, 2000);
        } catch (err) {
            console.error('Falha ao copiar texto: ', err);
            alert('Não foi possível copiar o texto. Por favor, selecione e copie manually.');
        }
        document.body.removeChild(textArea);
    });

    // Exemplo de como você poderia carregar conteúdo dinamicamente (simples)
    // function loadPageContent(url) {
    //   const pageContentDiv = document.getElementById('pageContent');
    //   if (!pageContentDiv) return;
    //
    //   fetch(url)
    //     .then(response => response.text())
    //     .then(html => {
    //       pageContentDiv.innerHTML = html;
    //       // Re-inicializar scripts ou event listeners específicos da nova página, se necessário
    //       // Por exemplo, se a nova página tiver seu próprio botão "generateSummaryBtn",
    //       // o event listener precisaria ser re-adicionado a ele.
    //       // A lógica atual do generateSummaryBtn já está fora desta função,
    //       // mas ela busca por IDs. Se os IDs mudarem com o conteúdo, precisará de ajuste.
    //     })
    //     .catch(error => {
    //       console.error('Erro ao carregar conteúdo da página:', error);
    //       pageContentDiv.innerHTML = '<p class="text-red-500">Erro ao carregar o conteúdo.</p>';
    //     });
    // }
    // Exemplo de uso:
    // No clique de um link da sidebar: loadPageContent('relatorio_vendas.html');
  });
</script>

</body>
</html>
