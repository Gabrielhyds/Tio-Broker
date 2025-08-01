<?php
/**
 * Controller para lidar com requisições relacionadas a visitas de imóveis.
 */

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

// Requisitos
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php'; // Reutilizaremos o AgendaModel

// Função de resposta JSON
function responder_json($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Validação de Segurança
if (!isset($_SESSION['usuario']['id_usuario'])) {
    responder_json(['success' => false, 'message' => 'Acesso não autorizado.'], 401);
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$action = $_GET['action'] ?? '';

// O Model da Agenda já tem o que precisamos, mas vamos adicionar um método específico.
// Para este exemplo, vamos adicionar o método `buscarVisitasPorImovel` no AgendaModel.
$agendaModel = new AgendaModel($connection);


if ($action === 'buscar_por_imovel') {
    $id_imovel = filter_input(INPUT_GET, 'id_imovel', FILTER_SANITIZE_NUMBER_INT);

    if (!$id_imovel) {
        responder_json(['success' => false, 'message' => 'ID do imóvel não fornecido.'], 400);
    }

    // Você precisará adicionar o método 'buscarVisitasPorImovel' ao seu AgendaModel.
    // Veja o próximo bloco de código para a implementação.
    $visitas = $agendaModel->buscarVisitasPorImovel($id_imovel, $id_usuario);

    responder_json(['success' => true, 'visitas' => $visitas]);
} else {
    responder_json(['success' => false, 'message' => 'Ação desconhecida.'], 400);
}
