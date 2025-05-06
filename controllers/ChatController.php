<?php
// Inicia a sessão para acessar dados do usuário logado
session_start(); // Deve sempre estar no topo antes de qualquer saída

// Inclui arquivos de configuração e do model Chat
require_once '../config/config.php';
require_once '../models/Chat.php';

// Cria uma instância do model Chat passando a conexão com o banco
$chat = new Chat($connection);

// Verifica se a requisição é do tipo POST e se a ação é 'enviar_mensagem'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'enviar_mensagem') {
    
    // Captura o ID do usuário logado a partir da sessão
    $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;

    // Captura os dados do formulário enviados via POST
    $id_conversa = $_POST['id_conversa'] ?? null;
    $mensagem = trim($_POST['mensagem'] ?? ''); // Remove espaços em branco no início e fim
    $id_destino = $_POST['id_destino'] ?? null;
    $id_imobiliaria = $_POST['id_imobiliaria'] ?? null;

    // Valida se os dados mínimos para enviar mensagem estão presentes
    if ($id_usuario && $id_conversa && $mensagem !== '') {
        // Envia a mensagem para o banco
        $chat->enviarMensagem($id_conversa, $id_usuario, $mensagem);
    }

    // Monta a URL de redirecionamento para manter o chat aberto na conversa ativa
    $url = "../views/chat/chat.php?id_destino=$id_destino";

    // Se houver um filtro de imobiliária, adiciona como parâmetro na URL
    if ($id_imobiliaria) {
        $url .= "&id_imobiliaria=$id_imobiliaria";
    }

    // Redireciona o usuário de volta para o chat da conversa em questão
    header("Location: $url");
    exit;
}
?>
