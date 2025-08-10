<?php
// Caminho: views/chat/get_mensagens.php

require_once '../../config/config.php';
require_once '../../models/Chat.php'; 
session_start();

if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

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
    
    $reacoesHtml = '';
    // Verificamos se a chave 'reacoes' existe e não está vazia.
    if (!empty($m['reacoes'])) {
        $reacoesHtml .= '<div class="flex gap-1 mt-1.5 ' . ($isMinha ? 'justify-end' : '') . '">';
        foreach ($m['reacoes'] as $reacao) {
            $reacoesHtml .= '<div class="text-xs bg-white/70 backdrop-blur-sm border rounded-full px-2 py-0.5 shadow-sm cursor-pointer" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">';
            $reacoesHtml .= htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'];
            $reacoesHtml .= '</div>';
        }
        $reacoesHtml .= '</div>';
    }
?>
    <div class="w-full flex <?= $isMinha ? 'justify-end' : 'justify-start' ?> <?= $isAgrupada ? 'mt-1' : 'mt-4' ?>">
        <div class="flex <?= $isMinha ? 'flex-row-reverse' : 'flex-row' ?> items-start gap-3 max-w-[80%] group">
            <div class="w-10 h-10 flex-shrink-0">
                <?php if (!$isAgrupada): ?>
                    <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full object-cover">
                <?php endif; ?>
            </div>
            <div class="flex flex-col gap-1 <?= $isMinha ? 'items-end' : 'items-start' ?>">
                <div class="relative p-3 rounded-2xl shadow-sm <?= $isMinha ? 'bg-violet-600 text-white rounded-br-lg' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-lg' ?>">
                    <p class="text-sm"><?= nl2br(htmlspecialchars($m['mensagem'])) ?></p>
                    <button class="btn-reagir absolute -top-3 right-0 bg-white rounded-full p-1 shadow border opacity-0 group-hover:opacity-100 transition-opacity" data-id-mensagem="<?= $m['id_mensagem'] ?>">
                        😊
                    </button>
                </div>
                <span class="text-xs text-gray-500 px-2">
                    <?= date('H:i', strtotime($m['data_envio'])) ?>
                </span>
                <?= $reacoesHtml ?>
            </div>
        </div>
    </div>
<?php
    $remetenteAnterior = $m['id_usuario'];
}
?>
