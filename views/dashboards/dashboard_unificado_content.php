<div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">Dashboard - <?= htmlspecialchars($permissao) ?></h2>
</div>
<!--TODO : AQUI FICARÁ OS DASHBOARDS---->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cartão exemplo -->
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Usuário logado</span>
            <i class="fas fa-user text-blue-600"></i>
        </div>
        <p class="text-xl font-bold text-gray-800"><?= htmlspecialchars($nomeUsuario) ?></p>
    </div>

    <?php if ($permissao === 'Admin'): ?>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Tarefas</span>
                <i class="fas fa-tasks text-green-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700">12 tarefas pendentes</p>
        </div>
    <?php endif; ?>

    <?php if ($permissao === 'Coordenador'): ?>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Relatórios</span>
                <i class="fas fa-chart-bar text-purple-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700">4 relatórios novos</p>
        </div>
    <?php endif; ?>

    <?php if ($permissao === 'Corretor'): ?>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Contatos</span>
                <i class="fas fa-address-book text-indigo-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700">8 novos leads</p>
        </div>
    <?php endif; ?>
</div>