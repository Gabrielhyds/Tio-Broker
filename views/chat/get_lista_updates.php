<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/get_lista_updates.php
|--------------------------------------------------------------------------
| - Endpoint eficiente que retorna um JSON com a última mensagem e a contagem
|   de não lidas para cada conversa do usuário.
*/

require_once '../../config/config.php';
require_once '../../models/Chat.php';
session_start();

// Proteção: se não houver usuário logado, não faz nada.
if (!isset($_SESSION['usuario']['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado.']);
    exit;
}

header('Content-Type: application/json');

try {
    $id_usuario_logado = $_SESSION['usuario']['id_usuario'];
    $chatModel = new Chat($connection);

    // Busca os dados mais recentes do modelo
    $ultimasMensagens = $chatModel->buscarUltimaMensagemCom($id_usuario_logado);
    $notificacoes = $chatModel->contarNaoLidasPorRemetente($id_usuario_logado);

    $updates = [];

    // Monta um array otimizado para o frontend
    foreach ($ultimasMensagens as $outro_usuario_id => $msg) {
        $updates[$outro_usuario_id] = [
            'ultima_mensagem' => mb_strimwidth($msg['mensagem'], 0, 30, "..."), // Limita o texto
            'data_envio_ts' => strtotime($msg['data_envio']), // Timestamp para ordenação
            'nao_lidas' => $notificacoes[$outro_usuario_id] ?? 0
        ];
    }
    
    // Adiciona usuários com quem não há mensagens mas podem ter notificações (caso raro)
    foreach ($notificacoes as $remetente_id => $total) {
        if (!isset($updates[$remetente_id])) {
            $updates[$remetente_id] = [
                'ultima_mensagem' => 'Nova mensagem',
                'data_envio_ts' => time(),
                'nao_lidas' => $total
            ];
        }
    }

    echo json_encode(['success' => true, 'data' => $updates]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no servidor.']);
}
