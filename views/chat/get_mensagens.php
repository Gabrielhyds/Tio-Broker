<?php
require_once '../../config/config.php';
require_once '../../models/Chat.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    exit;
}

$chat = new Chat($connection);
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa = $_GET['id_conversa'] ?? null;

if (!$id_conversa) exit;

// Marca mensagens como lidas ao abrir
$chat->marcarComoLidas($id_conversa, $id_usuario_logado);
$mensagens = $chat->listarMensagensDaConversa($id_conversa);

// Renderiza HTML das mensagens
foreach ($mensagens as $m) {
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);
    $classe = $isMinha ? 'mensagem-direita' : 'mensagem-esquerda';
    $status = $isMinha
        ? ($m['lida'] ? '<span class="text-success">✔️</span>' : '<span class="text-muted">✔️</span>')
        : '';

    echo "
    <div class='mensagem $classe'>
        <div><strong>" . htmlspecialchars($m['nome_usuario']) . ":</strong></div>
        <div>" . nl2br(htmlspecialchars($m['mensagem'])) . "</div>
        <small class='text-muted d-block'>" . date('d/m/Y H:i', strtotime($m['data_envio'])) . " $status</small>
    </div>
    ";
}
