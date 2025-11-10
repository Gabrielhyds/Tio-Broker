<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/config.php';
require_once '../../models/Empreendimento.php';

$empreendimentoModel = new Empreendimento($connection);
$empreendimentos = $empreendimentoModel->listarTodos();
?>

<div class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Empreendimentos</h1>
            <a href="cadastrar_empreendimento.php" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                + Novo Empreendimento
            </a>
        </div>

        <?php if (empty($empreendimentos)): ?>
            <div class="text-center py-12 bg-white rounded-3xl shadow-md">
                <p class="text-gray-500 text-lg">Nenhum empreendimento encontrado.</p>
                <p class="text-gray-400 mt-2">Clique em "+ Novo Empreendimento" para começar.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($empreendimentos as $empreendimento): ?>
                    <div class="bg-white rounded-3xl shadow-md overflow-hidden flex flex-col">
                        
                        <!-- CAMINHO DA IMAGEM CORRIGIDO -->
                        <img src="<?= !empty($empreendimento['imagem_principal']) ? '../../' . htmlspecialchars($empreendimento['imagem_principal']) : '../../views/assets/img/placeholder.png' ?>" 
                             alt="Imagem de <?= htmlspecialchars($empreendimento['nome']) ?>" class="w-full h-48 object-cover">
                        
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 mb-2 truncate"><?= htmlspecialchars($empreendimento['nome']) ?></h2>
                                <p class="text-gray-500 text-sm mb-4 h-10 overflow-hidden"><?= htmlspecialchars($empreendimento['descricao']) ?></p>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li><strong>Status:</strong> <span class="font-semibold"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $empreendimento['status']))) ?></span></li>
                                    <li><strong>Categoria:</strong> <span class="font-semibold"><?= htmlspecialchars(ucfirst($empreendimento['categoria'])) ?></span></li>
                                    <li><strong>Cidade:</strong> <span class="font-semibold"><?= htmlspecialchars($empreendimento['cidade']) ?> - <?= htmlspecialchars($empreendimento['estado']) ?></span></li>
                                </ul>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="ver.php?id=<?= $empreendimento['id_empreendimento'] ?>" 
                                   class="flex-1 px-3 py-2 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition text-center text-sm font-semibold">
                                   Ver Detalhes
                                </a>
                                <a href="editar_empreendimento.php?id=<?= $empreendimento['id_empreendimento'] ?>" 
                                   class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-center text-sm font-semibold">
                                   Editar
                                </a>
                                <a href="../../controllers/EmpreendimentoController.php?action=deletar&id=<?= $empreendimento['id_empreendimento'] ?>" 
                                   class="flex-1 px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition text-center text-sm font-semibold"
                                   onclick="return confirm('Tem certeza que deseja deletar este empreendimento?');">
                                   Deletar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
