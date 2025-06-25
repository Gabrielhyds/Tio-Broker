<?php
// controllers/api_eventos.php

header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AgendaModel.php';

if (!isset($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

$agendaModel = new AgendaModel($connection);
$id_usuario = $_SESSION['usuario']['id_usuario'];
$eventos_db = $agendaModel->buscarEventosPorUsuario($id_usuario);
$eventos_calendario = [];

foreach ($eventos_db as $evento) {
    $color = '#3788d8'; // Cor padrão
    if ($evento['tipo_evento'] === 'reuniao') $color = '#0D9488';
    if ($evento['tipo_evento'] === 'visita') $color = '#D97706';

    $eventos_calendario[] = [
        'id'        => $evento['id_evento'],
        'title'     => $evento['titulo'],
        'start'     => $evento['data_inicio'],
        'end'       => $evento['data_fim'],
        'color'     => $color,
        // (NOVO) Adiciona os novos campos nas propriedades extendidas para o frontend
        'extendedProps' => [
            'cliente'   => $evento['nome_cliente'],
            'tipo'      => ucfirst($evento['tipo_evento']),
            'id_imovel' => $evento['id_imovel'],
            'feedback'  => $evento['feedback']
        ]
    ];
}

echo json_encode($eventos_calendario);
