<?php

// Inicia a sessão para acesso à variável $_SESSION
session_start();

// Importa os arquivos de configuração e o modelo de chat
require_once '../config/config.php';
require_once '../models/Chat.php';

// Verifica se o usuário está autenticado (existe na sessão)
if (!isset($_SESSION['usuario']['id_usuario'])) {

    // Se a requisição for AJAX, retorna erro em formato JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    } else {
        // Se não for AJAX, redireciona para a página de login
        header('Location: ../views/auth/login.php');
    }

    // Encerra o script após redirecionamento ou resposta JSON
    exit;
}

// Cria uma instância do modelo de chat passando a conexão com o banco
$chat = new Chat($connection);

// Obtém o ID do usuário logado
$id_usuario = $_SESSION['usuario']['id_usuario'];

// Verifica se a requisição é POST e se existe a variável 'action'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Define as ações possíveis com base no valor de 'action'
    switch ($_POST['action']) {

        // --- AÇÃO: Enviar mensagem ---
        case 'enviar_mensagem':
            // Captura os dados enviados pelo formulário
            $id_conversa = $_POST['id_conversa'] ?? null;
            $mensagem = trim($_POST['mensagem'] ?? '');
            $id_destino = $_POST['id_destino'] ?? null;
            $id_imobiliaria = $_POST['id_imobiliaria'] ?? null;

            // Se tudo estiver preenchido, envia a mensagem
            if ($id_usuario && $id_conversa && $mensagem !== '') {
                $chat->enviarMensagem($id_conversa, $id_usuario, $mensagem);
            }

            // Monta a URL de retorno para a interface do chat
            $url = "../views/chat/chat.php?id_destino=$id_destino";
            if ($id_imobiliaria) {
                $url .= "&id_imobiliaria=$id_imobiliaria";
            }

            // Redireciona de volta para a interface do chat
            header("Location: $url");
            exit;

            // --- AÇÃO: Reagir a uma mensagem ---
        case 'reagir_mensagem':
            // Define que o retorno será JSON
            header('Content-Type: application/json');

            // Captura os dados enviados
            $id_mensagem = $_POST['id_mensagem'] ?? null;
            $reacao = $_POST['reacao'] ?? null;

            // Verifica se os dados necessários estão presentes
            if ($id_mensagem && $reacao) {
                // Tenta adicionar ou atualizar a reação à mensagem
                $sucesso = $chat->adicionarOuAtualizarReacao($id_mensagem, $id_usuario, $reacao);

                // Retorna o resultado da operação
                if ($sucesso) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Não foi possível salvar a reação.']);
                }
            } else {
                // Dados insuficientes para processar a reação
                echo json_encode(['success' => false, 'error' => 'Dados insuficientes.']);
            }

            // Encerra o script após resposta JSON
            exit;
    }
}
