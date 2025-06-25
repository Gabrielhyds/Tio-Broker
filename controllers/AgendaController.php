<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php';

/**
 * Função auxiliar para retornar respostas JSON e encerrar o script.
 * @param array $data O payload da resposta.
 * @param int $statusCode O código de status HTTP.
 */
function responder_json($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuario']['id_usuario'])) {
    responder_json(['success' => false, 'message' => 'Acesso negado. A sua sessão pode ter expirado.'], 401);
}

$id_usuario = $_SESSION['usuario']['id_usuario'];
$agendaModel = new AgendaModel($connection);
// Usa $_REQUEST para aceitar o parâmetro 'action' tanto de GET quanto de POST
$action = $_REQUEST['action'] ?? '';

// Estrutura switch para lidar com diferentes ações
switch ($action) {
    case 'agendar':
    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido para esta ação.'], 405);
        }
        
        // Validações dos campos obrigatórios
        if (empty($_POST['titulo']) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) || empty($_POST['tipo_evento'])) {
            responder_json(['success' => false, 'message' => 'Por favor, preencha os campos obrigatórios: Título, Início, Fim e Tipo.'], 400);
        }
        if ($_POST['data_fim'] < $_POST['data_inicio']) {
            responder_json(['success' => false, 'message' => 'A data final do evento não pode ser anterior à data inicial.'], 400);
        }

        // Prepara os dados do evento com sanitização
        $dadosEvento = [
            'id_usuario'  => $id_usuario,
            'id_cliente'  => !empty($_POST['id_cliente']) ? filter_input(INPUT_POST, 'id_cliente', FILTER_SANITIZE_NUMBER_INT) : null,
            'titulo'      => htmlspecialchars($_POST['titulo'], ENT_QUOTES, 'UTF-8'),
            'descricao'   => htmlspecialchars($_POST['descricao'] ?? '', ENT_QUOTES, 'UTF-8'),
            'data_inicio' => str_replace('T', ' ', $_POST['data_inicio']),
            'data_fim'    => str_replace('T', ' ', $_POST['data_fim']),
            'tipo_evento' => htmlspecialchars($_POST['tipo_evento'], ENT_QUOTES, 'UTF-8'),
            'lembrete'    => isset($_POST['lembrete']) ? 1 : 0
        ];

        if ($action === 'agendar') {
            if ($agendaModel->criarEvento($dadosEvento)) {
                responder_json(['success' => true, 'message' => 'Evento agendado com sucesso!']);
            } else {
                responder_json(['success' => false, 'message' => 'Ocorreu um erro ao agendar o evento no banco de dados.'], 500);
            }
        } else { // Ação é 'atualizar'
            $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_SANITIZE_NUMBER_INT);
            if (empty($id_evento)) {
                 responder_json(['success' => false, 'message' => 'ID do evento não fornecido para atualização.'], 400);
            }
            if ($agendaModel->atualizarEvento($id_evento, $dadosEvento)) {
                responder_json(['success' => true, 'message' => 'Evento atualizado com sucesso!']);
            } else {
                responder_json(['success' => false, 'message' => 'Ocorreu um erro ao atualizar o evento.'], 500);
            }
        }
        break;

    case 'atualizar_data': // Ação para arrastar e redimensionar
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido.'], 405);
        }
        $id_evento = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $data_inicio = $_POST['start'] ?? null;
        // Se 'end' não for fornecido, usa 'start' (caso de eventos sem duração definida)
        $data_fim = $_POST['end'] ?? $data_inicio;

        if (empty($id_evento) || empty($data_inicio)) {
             responder_json(['success' => false, 'message' => 'Dados insuficientes para atualizar a data.'], 400);
        }

        if ($agendaModel->atualizarDataEvento($id_evento, $data_inicio, $data_fim, $id_usuario)) {
            responder_json(['success' => true, 'message' => 'Data do evento atualizada com sucesso.']);
        } else {
            responder_json(['success' => false, 'message' => 'Falha ao atualizar a data do evento.'], 500);
        }
        break;

    case 'excluir':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido.'], 405);
        }
        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_SANITIZE_NUMBER_INT);
        if (empty($id_evento)) {
             responder_json(['success' => false, 'message' => 'ID do evento não fornecido para exclusão.'], 400);
        }

        if ($agendaModel->excluirEvento($id_evento, $id_usuario)) {
            responder_json(['success' => true, 'message' => 'Evento excluído com sucesso!']);
        } else {
            responder_json(['success' => false, 'message' => 'Ocorreu um erro ao excluir o evento.'], 500);
        }
        break;
    
    case 'buscar_evento': // Nova ação para popular o modal de edição
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido.'], 405);
        }
        $id_evento = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if(empty($id_evento)) {
            responder_json(['success' => false, 'message' => 'ID do evento não fornecido.'], 400);
        }

        $evento = $agendaModel->buscarEventoPorId($id_evento, $id_usuario);
        if($evento) {
            responder_json(['success' => true, 'data' => $evento]);
        } else {
            responder_json(['success' => false, 'message' => 'Evento não encontrado ou você não tem permissão para acessá-lo.'], 404);
        }
        break;

    default:
        responder_json(['success' => false, 'message' => 'Ação desconhecida ou não fornecida.'], 400);
        break;
}

