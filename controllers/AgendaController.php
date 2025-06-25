<?php
/**
 * Controller da Agenda
 * Lida com todas as ações CRUD (Criar, Ler, Atualizar, Deletar) para os eventos.
 */

// Define o cabeçalho da resposta como JSON para todas as saídas.
header('Content-Type: application/json');

// Inicia a sessão se ainda não houver uma.
if (session_status() === PHP_SESSION_NONE) session_start();

// Importa a configuração do banco e o Model da Agenda.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php';

/**
 * Função auxiliar para padronizar as respostas JSON e encerrar o script.
 * @param array $data O payload da resposta.
 * @param int $statusCode O código de status HTTP (ex: 200 para sucesso, 400 para erro do cliente).
 */
function responder_json($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// ---- Validação de Segurança ----
// Garante que apenas usuários logados possam executar qualquer ação neste controller.
if (!isset($_SESSION['usuario']['id_usuario'])) {
    responder_json(['success' => false, 'message' => 'Acesso negado. A sua sessão pode ter expirado.'], 401);
}

// ---- Inicialização ----
$id_usuario = $_SESSION['usuario']['id_usuario'];
$agendaModel = new AgendaModel($connection);
// Usa $_REQUEST para capturar o parâmetro 'action' de requisições GET ou POST.
$action = $_REQUEST['action'] ?? '';

// ---- Roteador de Ações ----
// O 'switch' direciona a requisição para o bloco de código correto com base na ação.
switch ($action) {
    case 'agendar':
    case 'atualizar':
        // Valida que a requisição é do tipo POST, que é apropriado para enviar dados.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            responder_json(['success' => false, 'message' => 'Método de requisição inválido para esta ação.'], 405);
        }
        
        // Validações de campos obrigatórios.
        if (empty($_POST['titulo']) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) || empty($_POST['tipo_evento'])) {
            responder_json(['success' => false, 'message' => 'Por favor, preencha os campos obrigatórios: Título, Início, Fim e Tipo.'], 400);
        }
        if ($_POST['data_fim'] < $_POST['data_inicio']) {
            responder_json(['success' => false, 'message' => 'A data final do evento não pode ser anterior à data inicial.'], 400);
        }

        // (CORRIGIDO) Sanitiza e prepara os dados, tratando explicitamente valores que podem ser nulos.
        $id_cliente_raw = filter_input(INPUT_POST, 'id_cliente', FILTER_SANITIZE_NUMBER_INT);
        $id_imovel_raw = filter_input(INPUT_POST, 'id_imovel', FILTER_SANITIZE_NUMBER_INT);

        $dadosEvento = [
            'id_usuario'  => $id_usuario,
            'id_cliente'  => $id_cliente_raw ?: null, // Garante que seja NULL se o valor for 0 ou vazio.
            'id_imovel'   => $id_imovel_raw ?: null,  // Garante que seja NULL se o valor for 0 ou vazio.
            'titulo'      => htmlspecialchars($_POST['titulo'], ENT_QUOTES, 'UTF-8'),
            'descricao'   => htmlspecialchars($_POST['descricao'] ?? '', ENT_QUOTES, 'UTF-8'),
            'data_inicio' => str_replace('T', ' ', $_POST['data_inicio']),
            'data_fim'    => str_replace('T', ' ', $_POST['data_fim']),
            'tipo_evento' => htmlspecialchars($_POST['tipo_evento'], ENT_QUOTES, 'UTF-8'),
            'lembrete'    => isset($_POST['lembrete']) ? 1 : 0,
            'feedback'    => htmlspecialchars($_POST['feedback'] ?? '', ENT_QUOTES, 'UTF-8')
        ];

        // Direciona para o método de criar ou atualizar no Model.
        if ($action === 'agendar') {
            if ($agendaModel->criarEvento($dadosEvento)) {
                responder_json(['success' => true, 'message' => 'Evento agendado com sucesso!']);
            } else {
                responder_json(['success' => false, 'message' => 'Ocorreu um erro ao agendar o evento.'], 500);
            }
        } else { // ação 'atualizar'
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

    case 'atualizar_data':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { responder_json(['success' => false, 'message' => 'Método inválido.'], 405); }
        
        $id_evento = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $data_inicio = $_POST['start'] ?? null;
        $data_fim = $_POST['end'] ?? $data_inicio; 
        
        // (CORRIGIDO) Adicionada validação para garantir que os dados necessários foram recebidos.
        if (empty($id_evento) || empty($data_inicio)) {
            responder_json(['success' => false, 'message' => 'Dados insuficientes para atualizar a data.'], 400);
        }

        if ($agendaModel->atualizarDataEvento($id_evento, $data_inicio, $data_fim, $id_usuario)) {
            responder_json(['success' => true, 'message' => 'Data do evento atualizada.']);
        } else {
            responder_json(['success' => false, 'message' => 'Falha ao atualizar a data.'], 500);
        }
        break;
        
    case 'excluir':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { responder_json(['success' => false, 'message' => 'Método inválido.'], 405); }
        
        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_SANITIZE_NUMBER_INT);

        // (CORRIGIDO) Adicionada validação para garantir que um ID foi fornecido antes de tentar excluir.
        if (empty($id_evento)) {
            responder_json(['success' => false, 'message' => 'ID do evento não fornecido para exclusão.'], 400);
        }

        if ($agendaModel->excluirEvento($id_evento, $id_usuario)) {
            responder_json(['success' => true, 'message' => 'Evento excluído com sucesso.']);
        } else {
            responder_json(['success' => false, 'message' => 'Erro ao excluir o evento.'], 500);
        }
        break;

    case 'buscar_evento':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') { responder_json(['success' => false, 'message' => 'Método inválido.'], 405); }
        
        $id_evento = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (empty($id_evento)) {
            responder_json(['success' => false, 'message' => 'ID do evento não fornecido.'], 400);
        }

        $evento = $agendaModel->buscarEventoPorId($id_evento, $id_usuario);
        if ($evento) {
            responder_json(['success' => true, 'data' => $evento]);
        } else {
            responder_json(['success' => false, 'message' => 'Evento não encontrado ou você não tem permissão para acessá-lo.'], 404);
        }
        break;
        
    default:
        // Caso nenhuma ação conhecida seja fornecida.
        responder_json(['success' => false, 'message' => 'Ação desconhecida.'], 400);
        break;
}
