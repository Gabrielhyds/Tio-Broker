<?php
/**
 * API endpoint para buscar os eventos da agenda do usuário logado.
 * Retorna os eventos em formato JSON compatível com o FullCalendar.
 */

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

// Dependências
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php';

// Proteção da rota: verifica se o usuário está autenticado
if (!isset($_SESSION['usuario']['id_usuario'])) {
    // Retorna um erro 401 (Não Autorizado) se não houver sessão
    http_response_code(401); 
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

$agendaModel = new AgendaModel($connection);
$id_usuario = $_SESSION['usuario']['id_usuario'];

// Busca todos os eventos associados ao ID do usuário
$eventos_db = $agendaModel->buscarEventosPorUsuario($id_usuario);

$eventos_calendario = [];

// Itera sobre os resultados do banco e formata para o padrão FullCalendar
foreach ($eventos_db as $evento) {

    // Define uma cor específica com base no tipo de evento para diferenciação visual
    $color = '#3788d8'; // Cor padrão (ex: 'outros')
    if ($evento['tipo_evento'] === 'reuniao') $color = '#0D9488'; // Verde/Azul (Teal)
    if ($evento['tipo_evento'] === 'visita') $color = '#D97706'; // Laranja/Âmbar

    // Mapeia os campos do banco para a estrutura que o FullCalendar espera
    $eventos_calendario[] = [
        'id'        => $evento['id_evento'],
        'title'     => $evento['titulo'],
        'start'     => $evento['data_inicio'], // Formato ISO8601 (ex: "2024-10-28T10:00:00")
        'end'       => $evento['data_fim'],
        'color'     => $color,
        
        // 'extendedProps' armazena dados customizados do evento.
        // O frontend (JavaScript) usará isso para exibir detalhes no modal.
        'extendedProps' => [
            'cliente'   => $evento['nome_cliente'],
            'tipo'      => ucfirst($evento['tipo_evento']), // Formata para "Reuniao", "Visita", etc.
            'id_imovel' => $evento['id_imovel'],
            'feedback'  => $evento['feedback']
        ]
    ];
}

// Retorna o array de eventos formatados como JSON
echo json_encode($eventos_calendario);
