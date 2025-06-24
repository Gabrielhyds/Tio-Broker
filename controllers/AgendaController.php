<?php

// Define que o retorno da resposta será em formato JSON
header('Content-Type: application/json');

// Inicia a sessão, se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// Importa as configurações e o modelo de agenda
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php';

// Função auxiliar para retornar uma resposta JSON e encerrar o script
function responder_json($sucesso, $mensagem)
{
    echo json_encode(['success' => $sucesso, 'message' => $mensagem]);
    exit;
}

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Garante que o usuário está autenticado
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        responder_json(false, 'Acesso negado. A sua sessão pode ter expirado.');
    }

    $action = $_POST['action'] ?? '';

    // Processa a ação de agendar evento
    if ($action === 'agendar') {

        // Valida os campos obrigatórios
        if (empty($_POST['titulo']) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) || empty($_POST['tipo_evento'])) {
            responder_json(false, 'Por favor, preencha os campos obrigatórios: Título, Início, Fim e Tipo.');
        }

        // Verifica se a data de fim é posterior à data de início
        if ($_POST['data_fim'] < $_POST['data_inicio']) {
            responder_json(false, 'A data final do evento não pode ser anterior à data inicial.');
        }

        // Instancia o modelo de agenda com a conexão ao banco
        $agendaModel = new AgendaModel($connection);

        // Prepara os dados do evento com sanitização
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

        // Tenta salvar o evento no banco de dados
        if ($agendaModel->criarEvento($dadosEvento)) {
            responder_json(true, 'Evento agendado com sucesso!');
        } else {
            responder_json(false, 'Ocorreu um erro ao guardar no banco. Verifique os dados e tente novamente.');
        }
    }
}

// Se não for uma requisição POST válida
responder_json(false, 'Método de requisição inválido.');
