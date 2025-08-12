<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/get_lista_updates.php
|--------------------------------------------------------------------------
| Endpoint que retorna JSON com última mensagem e contagem de não lidas
| por usuário (conversas privadas) para o usuário logado.
*/

require_once '../../config/config.php';
require_once '../../vendor/autoload.php'; // usa o autoloader do Composer
session_start();

use App\Models\Chat;

header('Content-Type: application/json; charset=utf-8');

// Proteção: se não houver usuário logado, não faz nada.
if (empty($_SESSION['usuario']['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $id_usuario_logado = (int) $_SESSION['usuario']['id_usuario'];

    // $connection deve ser um \mysqli vindo do seu config.php
    $chatModel = new Chat($connection);

    // Traz últimas mensagens e contagem de não lidas
    $ultimasMensagens = $chatModel->buscarUltimaMensagemCom($id_usuario_logado);   // [outro_id => ['mensagem','data_envio']]
    $notificacoes     = $chatModel->contarNaoLidasPorRemetente($id_usuario_logado); // [remetente_id => total]

    $updates = [];

    foreach ($ultimasMensagens as $outro_usuario_id => $msg) {
        $outro_usuario_id = (int) $outro_usuario_id;
        $texto   = isset($msg['mensagem']) ? (string)$msg['mensagem'] : '';
        $dataTs  = isset($msg['data_envio']) ? strtotime($msg['data_envio']) : time();

        $updates[$outro_usuario_id] = [
            'ultima_mensagem' => mb_strimwidth($texto, 0, 30, '...'),
            'data_envio_ts'   => $dataTs,
            'nao_lidas'       => (int) ($notificacoes[$outro_usuario_id] ?? 0),
        ];
    }

    // Garante incluir quem só tem não lida mas não apareceu em $ultimasMensagens
    foreach ($notificacoes as $remetente_id => $total) {
        $remetente_id = (int) $remetente_id;
        if (!isset($updates[$remetente_id])) {
            $updates[$remetente_id] = [
                'ultima_mensagem' => 'Nova mensagem',
                'data_envio_ts'   => time(),
                'nao_lidas'       => (int) $total,
            ];
        }
    }

    echo json_encode(['success' => true, 'data' => $updates], JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no servidor.'], JSON_UNESCAPED_UNICODE);
}
