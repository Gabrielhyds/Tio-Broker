<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Chat.php';

if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

$chat = new Chat($connection);
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-center text-gray-400">Nenhuma mensagem ainda. Inicie a conversa.</p>';
    exit;
}

foreach ($mensagens as $m) {
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);

    $alinhamento = $isMinha ? 'items-end' : 'items-start';
    $bgColor = $isMinha ? 'bg-green-100' : 'bg-gray-200';
    $textAlign = 'text-left';
    $floatDir = $isMinha ? 'ml-auto flex-row-reverse' : 'mr-auto flex-row';
    $orderBtn = $isMinha ? 'order-1' : 'order-2'; // INVERTE a ordem do botão ⋮ para quem envia

    $statusLida = $isMinha
        ? ($m['lida']
            ? '<span class="text-green-600 ml-2">✔️</span>'
            : '<span class="text-gray-500 ml-2">✔️</span>')
        : '';

    echo '<div class="flex flex-col ' . $alinhamento . ' mb-2">';
    echo '<div class="flex items-start gap-2 ' . $floatDir . '">';

    // Botão de opções
    echo '<div class="relative ' . $orderBtn . '">';
    echo '<button class="btn-opcoes text-gray-500 hover:text-black px-1">⋮</button>';
    echo '<div class="dropdown-opcoes absolute z-50 mt-1 w-32 bg-white border rounded shadow hidden">';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100 btn-reagir" data-id-mensagem="' . $m['id_mensagem'] . '">Reagir</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100">Editar</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100">Responder</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">Excluir</button>';
    echo '</div>';
    echo '</div>';

    // Bolha da mensagem
    echo '<div class="mensagem group relative max-w-[75%] rounded-xl px-4 py-2 ' . $bgColor . ' ' . $textAlign . ' whitespace-pre-wrap break-words" data-id-mensagem="' . $m['id_mensagem'] . '">';
    echo '<div class="text-sm font-semibold">' . htmlspecialchars($m['nome_usuario']) . ':</div>';
    echo '<div class="text-sm">' . nl2br(htmlspecialchars($m['mensagem'])) . '</div>';
    echo '<div class="text-xs text-gray-500 mt-1">' . date('H:i', strtotime($m['data_envio'])) . $statusLida . '</div>';
    echo '</div>';

    echo '</div>'; // fecha linha (botão + bolha)

    // Reações
    if (!empty($m['reacoes'])) {
        echo '<div class="flex gap-1 mt-1 ' . ($isMinha ? 'justify-end' : 'justify-start') . '">';
        foreach ($m['reacoes'] as $reacao) {
            echo '<span class="text-sm bg-white border rounded-full px-2 py-0.5 shadow" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">';
            echo htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'];
            echo '</span>';
        }
        echo '</div>';
    }

    echo '</div>'; // fecha coluna principal
}
