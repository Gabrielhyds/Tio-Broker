<aside id="sidebar" class="w-64 flex-shrink-0 bg-white border-r border-gray-200 p-4 transform lg:transform-none lg:relative fixed h-full z-30 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div class="flex items-center justify-between mb-8">
        <a href="/" class="flex items-center space-x-2">
            <i class="fas fa-chart-pie text-2xl text-blue-600"></i>
            <span class="text-xl font-bold text-gray-800">Tio Broker</span>
        </a>
        <button id="close-sidebar-btn" class="lg:hidden text-gray-500 hover:text-gray-800">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <nav class="space-y-4 sidebar-scroll h-full pb-20 overflow-y-auto">
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Início</h3>
            <a href="/dashboards/index.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-plus w-6 text-center"></i><span class="ml-2">Resumo</span>
            </a>
        </div>
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Imobiliária</h3>
            <a href="/imobiliarias/cadastrar_imobiliaria.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-plus w-6 text-center"></i><span class="ml-2">Cadastrar</span>
            </a>
            <a href="/imobiliarias/listar_imobiliaria.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-building w-6 text-center"></i><span class="ml-2">Ver Imobiliárias</span>
            </a>
        </div>
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Usuário</h3>
            <a href="../usuarios/cadastrar.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-user-plus w-6 text-center"></i><span class="ml-2">Cadastrar Usuário</span>
            </a>
            <a href="../usuarios/listar.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-users w-6 text-center"></i><span class="ml-2">Gerenciar Usuários</span>
            </a>
        </div>
        <div>
            <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Ferramentas</h3>
            <a href="../chat/chat.php" class="sidebar-link flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-comments w-6 text-center"></i><span class="ml-2">Chat</span>
            </a>
        </div>
    </nav>
</aside>