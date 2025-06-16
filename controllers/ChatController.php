<?php
session_start();

require_once '../config/config.php';
require_once '../models/Chat.php';

// Verifica se há um usuário na sessão
if (!isset($_SESSION['usuario']['id_usuario'])) {
    // Para requisições AJAX, retorna um erro JSON. Para outras, redireciona.
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    } else {
        header('Location: ../views/auth/login.php');
    }
    exit;
}

$chat = new Chat($connection);
$id_usuario = $_SESSION['usuario']['id_usuario'];

// Roteamento de ações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    switch ($_POST['action']) {

        // --- AÇÃO DE ENVIAR MENSAGEM (sem alterações) ---
        case 'enviar_mensagem':
            $id_conversa = $_POST['id_conversa'] ?? null;
            $mensagem = trim($_POST['mensagem'] ?? '');
            $id_destino = $_POST['id_destino'] ?? null;
            $id_imobiliaria = $_POST['id_imobiliaria'] ?? null;

            if ($id_usuario && $id_conversa && $mensagem !== '') {
                $chat->enviarMensagem($id_conversa, $id_usuario, $mensagem);
            }

            $url = "../views/chat/chat.php?id_destino=$id_destino";
            if ($id_imobiliaria) {
                $url .= "&id_imobiliaria=$id_imobiliaria";
            }
            header("Location: $url");
            exit;

            // --- NOVA AÇÃO PARA REAGIR A UMA MENSAGEM ---
        case 'reagir_mensagem':
            header('Content-Type: application/json'); // Sempre retorna JSON

            $id_mensagem = $_POST['id_mensagem'] ?? null;
            $reacao = $_POST['reacao'] ?? null;

            if ($id_mensagem && $reacao) {
                $sucesso = $chat->adicionarOuAtualizarReacao($id_mensagem, $id_usuario, $reacao);
                if ($sucesso) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Não foi possível salvar a reação.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Dados insuficientes.']);
            }
            exit;
    }
}