<?php

// Define que a resposta será no formato JSON
header('Content-Type: application/json');

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// Inclui o arquivo de configuração do sistema (contém a variável $connection)
require_once __DIR__ . '/../config/config.php';

// Inclui o model responsável por lidar com a agenda
require_once __DIR__ . '/../models/AgendaModel.php';

// Verifica se o usuário está logado, caso contrário encerra a execução
if (!isset($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// Instancia o model da Agenda, passando a conexão com o banco
$agendaModel = new AgendaModel($connection);

// Obtém o ID do usuário logado
$id_usuario = $_SESSION['usuario']['id_usuario'];

// Busca os eventos do banco de dados associados a esse usuário
$eventos_db = $agendaModel->buscarEventosPorUsuario($id_usuario);

// Inicializa o array que será enviado ao FullCalendar
$eventos_calendario = [];

// Percorre os eventos retornados do banco para formatá-los
foreach ($eventos_db as $evento) {
    // Define uma cor padrão para o evento
    $color = '#3788d8';

    // Altera a cor com base no tipo de evento
    if ($evento['tipo_evento'] === 'reuniao') $color = '#0D9488';
    if ($evento['tipo_evento'] === 'visita') $color = '#D97706';

    // Formata os dados do evento para o formato esperado pelo FullCalendar
    $eventos_calendario[] = [
        'id'        => $evento['id_evento'],
        'title'     => $evento['titulo'],
        'start'     => $evento['data_inicio'],
        'end'       => $evento['data_fim'],
        'color'     => $color,
        'extendedProps' => [ // Propriedades adicionais personalizadas
            'cliente' => $evento['nome_cliente'],
            'tipo'    => ucfirst($evento['tipo_evento']) // Capitaliza o tipo (ex: "Reunião")
        ]
    ];
}

// Retorna o array de eventos formatados em JSON
echo json_encode($eventos_calendario);
