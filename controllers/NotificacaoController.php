<?php
// controllers/NotificacaoController.php

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Carrega o config.php (que deve definir $connection)
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../models/Notificacao.php';

header('Content-Type: application/json');

// Resposta padrão
$resposta = [];

try {
    // 2. CORREÇÃO: Verifica a sessão (necessário para GET e POST)
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        throw new Exception('Usuário não autenticado (Sessão não encontrada em usuario.id_usuario)');
    }
    
    $id_usuario = $_SESSION['usuario']['id_usuario']; 

    // 3. CORREÇÃO: Usa a variável $connection (necessário para GET e POST)
    if (!isset($connection) || $connection->connect_error) { 
        throw new Exception('Erro na conexão MySQLi: ' . (isset($connection) ? $connection->connect_error : 'Variável $connection não definida.'));
    }

    // 4. Instancia o Model com a conexão correta
    $notificacaoModel = new Notificacao($connection);

    // --- NOVO: Lógica para "Marcar como Lidas" (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $sucesso = $notificacaoModel->marcarTodasComoLidas($id_usuario);
        $resposta['success'] = $sucesso;
        if (!$sucesso) {
            // Isso não vai parar o script, mas é bom para debug se falhar
            $resposta['error'] = 'Falha ao executar a query de update no modelo.';
        }

    } 
    // --- Lógica Antiga: "Buscar Notificações" (GET) ---
    else { 
        // 5. Busca os dados
        $items = $notificacaoModel->buscarNaoLidasPorUsuario($id_usuario);
        $count = $notificacaoModel->contarNaoLidasPorUsuario($id_usuario);

        // 6. Formata os itens
        $itemsFormatados = [];
        foreach ($items as $item) {
            $itemsFormatados[] = [
                'mensagem' => htmlspecialchars($item['mensagem']),
                'link' => '#', 
                'data' => date('d/m/Y H:i', strtotime($item['data_envio'])) 
            ];
        }

        // 7. Prepara a resposta
        $resposta['count'] = $count;
        $resposta['items'] = $itemsFormatados;
    }

} catch (Exception $e) {
    http_response_code(500);
    $resposta['error'] = $e->getMessage();
}

// 8. Envia a resposta JSON
echo json_encode($resposta);

// 9. Fecha a conexão MySQLi
if (isset($connection)) {
    $connection->close();
}

exit;
?>

