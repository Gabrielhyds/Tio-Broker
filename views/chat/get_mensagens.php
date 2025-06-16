<?php
// get_mensagens.php

session_start();
require_once '../../config/config.php';
require_once '../../models/Chat.php';

// Validações de segurança
if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401); // Unauthorized
    exit;
}

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

$chat = new Chat($connection);
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-muted text-center mt-3">Nenhuma mensagem ainda. Inicie a conversa.</p>';
    exit;
}

// Monta o HTML das mensagens para ser retornado
foreach ($mensagens as $m) {
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);
    $classe = $isMinha ? 'mensagem-direita' : 'mensagem-esquerda';
    $statusLida = $isMinha ? ($m['lida'] ? '<span class="status-lida visto" title="Visto">✔️</span>' : '<span class="status-lida" title="Enviado">✔️</span>') : '';

    // Container principal da mensagem e suas reações
    echo '<div class="mensagem-wrapper ' . ($isMinha ? 'minha-mensagem' : 'outra-mensagem') . '">';

    // O balão da mensagem
    echo '<div class="mensagem ' . $classe . '" data-id-mensagem="' . $m['id_mensagem'] . '">';

    // BOTÃO DE REAÇÃO (A CORREÇÃO PRINCIPAL ESTÁ AQUI)
    echo '<button class="btn-reagir" title="Reagir">💬</button>';

    echo '<div><strong>' . htmlspecialchars($m['nome_usuario']) . ':</strong></div>';
    echo '<div>' . nl2br(htmlspecialchars($m['mensagem'])) . '</div>';
    echo '<small class="text-muted d-block text-end w-100">';
    echo date('H:i', strtotime($m['data_envio']));
    echo $statusLida;
    echo '</small>';
    echo '</div>'; // Fim da div .mensagem

    // Container para exibir as reações existentes
    if (!empty($m['reacoes'])) {
        echo '<div class="reacoes-container">';
        foreach ($m['reacoes'] as $reacao) {
            echo '<span class="badge rounded-pill bg-light text-dark me-1" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">';
            echo htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'];
            echo '</span>';
        }
        echo '</div>'; // Fim da div .reacoes-container
    }

    echo '</div>'; // Fim da div .mensagem-wrapper
}