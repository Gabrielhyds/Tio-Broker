<header class="flex items-center justify-between p-4 bg-white border-b border-gray-200">
    <div class="flex items-center space-x-4">
        <button id="open-sidebar-btn" class="lg:hidden text-gray-600 hover:text-gray-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="relative hidden md:block">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Buscar..." class="w-64 lg:w-96 pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
        </div>
    </div>
    <div class="flex items-center space-x-4">
        <button class="text-gray-500 hover:text-gray-800"><i class="fas fa-bell text-xl"></i></button>
        <div class="relative">
            <button id="profile-btn" class="flex items-center space-x-2">
                <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="Avatar do usuário" class="w-9 h-9 rounded-full">
            </button>
            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800">Admin</p>
                    <p class="text-xs text-gray-500 truncate">admin@tiobroker.com</p>
                </div>
                <hr class="my-1">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meu Perfil</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
                <a href="../../controllers/LogoutController.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sair</a>
            </div>
        </div>
    </div>
</header>