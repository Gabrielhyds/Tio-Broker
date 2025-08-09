<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/get_mensagens.php (CORRIGIDO)
|--------------------------------------------------------------------------
| - Garante que as classes CSS para mensagens enviadas ('bg-violet-600')
|   são as mesmas usadas pelo JavaScript, resolvendo a inconsistência de cores.
*/

require_once '../../config/config.php';
// Lembre-se de ajustar o caminho para o seu modelo se ele usar namespaces
require_once '../../models/Chat.php'; 
session_start();

if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

// Assumindo que seu modelo está no namespace App\Models
$chat = new \App\Models\Chat($connection); 
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-center text-gray-500">Nenhuma mensagem ainda. Envie a primeira!</p>';
    exit;
}

$remetenteAnterior = null;

foreach ($mensagens as $m) {
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);
    $isAgrupada = ($remetenteAnterior === $m['id_usuario']);
    $avatar_url = !empty($m['foto']) ? '../../uploads/' . htmlspecialchars($m['foto']) : 'https://placehold.co/100x100/c4b5fd/4c1d95?text=' . mb_strtoupper(mb_substr($m['nome_usuario'], 0, 1));
?>
    <!-- Container da linha inteira, que alinha a mensagem à esquerda ou à direita -->
    <div class="w-full flex <?= $isMinha ? 'justify-end' : 'justify-start' ?> <?= $isAgrupada ? 'mt-1' : 'mt-4' ?>">
        
        <!-- Container da mensagem (avatar + bolha) com largura máxima de 80% -->
        <div class="flex <?= $isMinha ? 'flex-row-reverse' : 'flex-row' ?> items-start gap-3 max-w-[80%]">
            
            <!-- Avatar (com espaço reservado se a mensagem for agrupada) -->
            <div class="w-10 h-10 flex-shrink-0">
                <?php if (!$isAgrupada): ?>
                    <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full object-cover">
                <?php endif; ?>
            </div>
            
            <!-- Bolha da mensagem e timestamp -->
            <div class="flex flex-col gap-1">
                <!-- **CORREÇÃO**: A classe aqui agora é idêntica à do JavaScript -->
                <div class="p-3 rounded-2xl shadow-sm <?= $isMinha ? 'bg-violet-600 text-white rounded-br-lg' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-lg' ?>">
                    <p class="text-sm"><?= nl2br(htmlspecialchars($m['mensagem'])) ?></p>
                </div>
                <span class="text-xs text-gray-500 px-2 <?= $isMinha ? 'self-end' : 'self-start' ?>">
                    <?= date('H:i', strtotime($m['data_envio'])) ?>
                </span>
            </div>

        </div>
    </div>
<?php
    $remetenteAnterior = $m['id_usuario'];
}
?>
