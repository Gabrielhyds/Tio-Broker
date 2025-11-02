<?php
// controllers/NotificacaoController.php

if (session_status() === PHP_SESSION_NONE) session_start();

// ATENÇÃO: Ajuste o caminho para seu arquivo de conexão mysqli
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../models/Notificacao.php';

header('Content-Type: application/json');

$resposta = [
    'count' => 0,
    'items' => []
];

try {
    // 1. Verifica se o usuário está logado
    if (!isset($_SESSION['usuario']['id'])) {
        throw new Exception('Usuário não autenticado');
    }
    
    $id_usuario = $_SESSION['usuario']['id']; 

    // 2. Conecta ao banco (MySQLi)
    // Garanta que seu 'database.php' define a variável $db (ou $conn)
    
    // Vou presumir que a variável de conexão se chama $db
    if (!isset($db) || $db->connect_error) { 
        throw new Exception('Erro na conexão MySQLi: ' . (isset($db) ? $db->connect_error : 'Variável $db não definida.'));
    }

    // 3. Instancia o Model com a conexão mysqli
    $notificacaoModel = new Notificacao($db);

    // 4. Busca os dados
    $items = $notificacaoModel->buscarNaoLidasPorUsuario($id_usuario);
    $count = $notificacaoModel->contarNaoLidasPorUsuario($id_usuario);

    // 5. Formata os itens (idêntico ao que fizemos antes)
    $itemsFormatados = [];
    foreach ($items as $item) {
        $itemsFormatados[] = [
            'mensagem' => htmlspecialchars($item['mensagem']),
            'link' => '#', 
            'data' => date('d/m/Y H:i', strtotime($item['data_envio'])) 
        ];
    }

    // 6. Prepara a resposta
    $resposta['count'] = $count;
    $resposta['items'] = $itemsFormatados;

} catch (Exception $e) {
    http_response_code(500);
    $resposta['error'] = $e->getMessage();
}

// 7. Envia a resposta JSON
echo json_encode($resposta);

// 8. Fecha a conexão MySQLi
if (isset($db)) {
    $db->close();
}

exit;
?>