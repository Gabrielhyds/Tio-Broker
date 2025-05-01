<?php
require_once '../../config/config.php';
require_once '../../models/Chat.php';
require_once '../../models/Usuario.php';

session_start();
if (!isset($_SESSION['usuario'])) exit;

$chat = new Chat($connection);
$usuarioModel = new Usuario($connection);
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];

$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);

// Juntar tudo
$retorno = [];
foreach ($ultimasMensagens as $id_user => $msg) {
    $retorno[$id_user] = [
        'mensagem' => $msg['mensagem'],
        'total_nao_lidas' => $notificacoes[$id_user] ?? 0
    ];
}

echo json_encode($retorno);
