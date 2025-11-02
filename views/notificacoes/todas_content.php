<?php
// views/notificacoes/todas_content.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Notificacao.php';

$mysqli = $connection;
$id_usuario = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$notificacaoModel = new Notificacao($mysqli);
$notificacoes = $notificacaoModel->buscarTodasPorUsuario($id_usuario, 100); // Limite maior

// Função para gerar timestamp relativo
function tempoRelativo($data) {
    $agora = new DateTime();
    $dataNotificacao = new DateTime($data);
    $diff = $agora->diff($dataNotificacao);

    if ($diff->y > 0) return $diff->y . " ano" . ($diff->y > 1 ? "s" : "") . " atrás";
    if ($diff->m > 0) return $diff->m . " mês" . ($diff->m > 1 ? "es" : "") . " atrás";
    if ($diff->d > 0) return $diff->d . " dia" . ($diff->d > 1 ? "s" : "") . " atrás";
    if ($diff->h > 0) return $diff->h . " hora" . ($diff->h > 1 ? "s" : "") . " atrás";
    if ($diff->i > 0) return $diff->i . " minuto" . ($diff->i > 1 ? "s" : "") . " atrás";
    return "Agora mesmo";
}

// Agrupar notificações por dia
$notificacoesPorDia = [];
foreach ($notificacoes as $n) {
    $dia = date('d/m/Y', strtotime($n['data_envio']));
    if (!isset($notificacoesPorDia[$dia])) {
        $notificacoesPorDia[$dia] = [];
    }
    $notificacoesPorDia[$dia][] = $n;
}

?>
<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Todas as Notificações</h1>
        <p class="text-gray-500">Seu histórico de notificações recentes.</p>
    </div>

    <!-- Lista de Notificações -->
    <div class="space-y-6">
        <?php if (!empty($notificacoes)): ?>
            <?php foreach ($notificacoesPorDia as $dia => $notificacoesDia): ?>
                <!-- Data -->
                <div class="text-gray-400 font-semibold text-sm mb-2"><?= $dia ?></div>

                <div class="space-y-4">
                    <?php foreach ($notificacoesDia as $notificacao): ?>
                        <?php
                        $lida = $notificacao['lida'];
                        $corFundo = $lida ? 'bg-white' : 'bg-blue-50';
                        $corBorda = $lida ? 'border-gray-200' : 'border-blue-300';
                        $corTexto = $lida ? 'text-gray-700' : 'text-gray-900';
                        // Ícone por tipo (exemplo genérico)
                        $icone = 'fa-bell';
                        ?>
                        <div class="notification-card flex items-start p-4 rounded-xl border <?= $corBorda ?> <?= $corFundo ?> shadow-sm hover:shadow-md transition-all duration-200 relative cursor-pointer">
                            <!-- Ícone -->
                            <div class="flex-shrink-0 mr-4">
                                <i class="fas <?= $icone ?> text-blue-500 text-xl"></i>
                            </div>

                            <!-- Conteúdo -->
                            <div class="flex-1">
                                <p class="font-semibold <?= $corTexto ?>"><?= htmlspecialchars($notificacao['mensagem']) ?></p>
                                <p class="text-gray-400 text-xs mt-2"><?= tempoRelativo($notificacao['data_envio']) ?></p>
                            </div>

                            <!-- Badge nova -->
                            <?php if (!$lida): ?>
                                <div class="absolute top-3 right-3">
                                    <span class="h-2 w-2 bg-blue-500 rounded-full animate-pulse"></span>
                                </div>
                            <?php endif; ?>

                            <!-- Botão marcar como lida -->
                            <?php if (!$lida): ?>
                                <button class="mark-read-btn absolute bottom-3 right-3 text-blue-500 hover:underline text-sm" data-id="<?= $notificacao['id_notificacao'] ?>">Marcar como lida</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum histórico</h3>
                <p class="mt-1 text-sm text-gray-500">Você ainda não recebeu nenhuma notificação.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marcar como lida via AJAX
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const card = this.closest('.notification-card');

            try {
                const resp = await fetch('<?= BASE_URL ?>controllers/NotificacaoController.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({acao: 'marcarComoLida', id_notificacao: id})
                });
                const data = await resp.json();
                if (data.success) {
                    // Remove badge e altera visual do card
                    card.querySelector('.animate-pulse')?.remove();
                    card.classList.remove('bg-blue-50', 'border-blue-300');
                    card.classList.add('bg-white', 'border-gray-200');
                    card.querySelector('p.font-semibold').classList.remove('text-gray-900');
                    card.querySelector('p.font-semibold').classList.add('text-gray-700');
                    this.remove();
                }
            } catch (err) {
                console.error('Erro ao marcar como lida:', err);
            }
        });
    });
});
</script>
