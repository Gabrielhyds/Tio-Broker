<!-- Contêiner para o cabeçalho do dashboard. -->
<div class="mb-6">
    <!-- Título que exibe dinamicamente o nível de permissão do usuário logado. -->
    <h2 class="text-2xl font-semibold text-gray-800">Dashboard - <?= htmlspecialchars($permissao) ?></h2>
</div>
<!-- TODO: Um comentário indicando que a área de dashboards será implementada aqui. -->
<!-- Grid para organizar os cartões (cards) do dashboard. -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cartão de exemplo que exibe o nome do usuário logado. -->
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-500">Usuário logado</span>
            <i class="fas fa-user text-blue-600"></i>
        </div>
        <!-- Exibe o nome do usuário com segurança, prevenindo ataques XSS. -->
        <p class="text-xl font-bold text-gray-800"><?= htmlspecialchars($nomeUsuario) ?></p>
    </div>

    <!-- Bloco PHP: Este cartão só será exibido se a permissão do usuário for 'Admin'. -->
    <?php if ($permissao === 'Admin'): ?>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Tarefas</span>
                <i class="fas fa-tasks text-green-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700">12 tarefas pendentes</p>
        </div>
    <?php endif; ?>

    <!-- Bloco PHP: Este cartão só será exibido se a permissão do usuário for 'Coordenador'. -->
    <?php if ($permissao === 'Coordenador'): ?>
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Relatórios</span>
                <i class="fas fa-chart-bar text-purple-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700">4 relatórios novos</p>
        </div>
    <?php endif; ?>

    <!-- Bloco PHP: Este cartão só será exibido se a permissão do usuário for 'Corretor'. -->
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