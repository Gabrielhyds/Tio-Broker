<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php'; 
// CORRIGIDO: O nome do ficheiro agora está com as maiúsculas corretas.
require_once __DIR__ . '/../models/AgendaModel.php';

function responder_json($sucesso, $mensagem) {
    echo json_encode(['success' => $sucesso, 'message' => $mensagem]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        responder_json(false, 'Acesso negado. A sua sessão pode ter expirado.');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'agendar') {
        if (empty($_POST['titulo']) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) || empty($_POST['tipo_evento'])) {
            responder_json(false, 'Por favor, preencha os campos obrigatórios: Título, Início, Fim e Tipo.');
        }

        if ($_POST['data_fim'] < $_POST['data_inicio']) {
            responder_json(false, 'A data final do evento não pode ser anterior à data inicial.');
        }
        
        $agendaModel = new AgendaModel($connection); 

        $dadosEvento = [
            'id_usuario'  => $_SESSION['usuario']['id_usuario'],
            'id_cliente'  => !empty($_POST['id_cliente']) ? filter_input(INPUT_POST, 'id_cliente', FILTER_SANITIZE_NUMBER_INT) : null,
            'titulo'      => htmlspecialchars($_POST['titulo'], ENT_QUOTES, 'UTF-8'),
            'descricao'   => htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8'),
            'data_inicio' => str_replace('T', ' ', $_POST['data_inicio']),
            'data_fim'    => str_replace('T', ' ', $_POST['data_fim']),
            'tipo_evento' => htmlspecialchars($_POST['tipo_evento'], ENT_QUOTES, 'UTF-8'),
            'lembrete'    => isset($_POST['lembrete']) ? 1 : 0
        ];
        
        if ($agendaModel->criarEvento($dadosEvento)) {
            responder_json(true, 'Evento agendado com sucesso!');
        } else {
            responder_json(false, 'Ocorreu um erro ao guardar no banco. Verifique os dados e tente novamente.');
        }
    }
}

responder_json(false, 'Método de requisição inválido.');
