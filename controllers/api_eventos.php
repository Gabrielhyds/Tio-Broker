<?php
// Define o cabeçalho como JSON para a resposta da API
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

// Requer os arquivos de configuração e modelo
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AgendaModel.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario']['id_usuario'])) {
    // Retorna um erro em JSON se não estiver logado
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// Conecta ao banco de dados e instancia o modelo
$database = new Database();
$pdo = $database->getConnection();
$agendaModel = new AgendaModel($pdo);

// Busca os eventos do usuário logado
$id_usuario = $_SESSION['usuario']['id_usuario'];
$eventos_db = $agendaModel->buscarEventosPorUsuario($id_usuario);

$eventos_calendario = [];
foreach ($eventos_db as $evento) {
    // Define a cor do evento com base no tipo
    $color = '#3788d8'; // Cor padrão (Azul)
    if ($evento['tipo_evento'] === 'reuniao') {
        $color = '#0D9488'; // Verde-azulado
    } elseif ($evento['tipo_evento'] === 'visita') {
        $color = '#D97706'; // Laranja
    }

    // Monta o array no formato que o FullCalendar entende
    $eventos_calendario[] = [
        'id'        => $evento['id_evento'],
        'title'     => $evento['titulo'],
        'start'     => $evento['data_inicio'],
        'end'       => $evento['data_fim'],
        'color'     => $color, // Define a cor do evento
        'extendedProps' => [
            'cliente' => $evento['nome_cliente'],
            'tipo'    => ucfirst($evento['tipo_evento']) // Capitaliza a primeira letra
        ]
    ];
}

// Retorna os eventos em formato JSON
echo json_encode($eventos_calendario);
