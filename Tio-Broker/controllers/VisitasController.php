<?php
/**
 * Controller para lidar com requisições relacionadas a visitas de imóveis.
 */

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

// Requisitos
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php'; 

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
$action = $_REQUEST['action'] ?? ''; // Usamos $_REQUEST para pegar de GET ou POST

$agendaModel = new AgendaModel($connection);

// Roteador de Ações
switch ($action) {
    case 'buscar_por_imovel':
        $id_imovel = filter_input(INPUT_GET, 'id_imovel', FILTER_SANITIZE_NUMBER_INT);
        if (!$id_imovel) {
            responder_json(['success' => false, 'message' => 'ID do imóvel não fornecido.'], 400);
        }
        // Este método precisa ser criado no seu AgendaModel
        $visitas = $agendaModel->buscarVisitasPorImovel($id_imovel, $id_usuario);
        responder_json(['success' => true, 'visitas' => $visitas]);
        break;

    case 'atualizar_feedback':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido.'], 405);
        }

        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_SANITIZE_NUMBER_INT);
        $feedback = htmlspecialchars($_POST['feedback'] ?? '', ENT_QUOTES, 'UTF-8');

        if (empty($id_evento)) {
            responder_json(['success' => false, 'message' => 'ID do evento não fornecido.'], 400);
        }

        if ($agendaModel->atualizarFeedback($id_evento, $feedback, $id_usuario)) {
            responder_json(['success' => true, 'message' => 'Feedback atualizado com sucesso.']);
        } else {
            responder_json(['success' => false, 'message' => 'Falha ao atualizar o feedback.'], 500);
        }
        break;

    default:
        responder_json(['success' => false, 'message' => 'Ação desconhecida.'], 400);
        break;
}
