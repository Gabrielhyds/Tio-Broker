<?php
session_start();
require_once '../config/config.php';
require_once '../models/Chat.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/auth/login.php');
    exit;
}

$chat = new Chat($connection);

// Ação: enviar mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'enviar_mensagem') {
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $id_conversa = $_POST['id_conversa'];
    $id_destino = $_POST['id_destino'];
    $mensagem = trim($_POST['mensagem']);

    if ($mensagem !== '') {
        $chat->enviarMensagem($id_conversa, $id_usuario, $mensagem);
    }

    header("Location: ../views/chat/chat.php?id_destino=" . $id_destino);
    exit;
}
