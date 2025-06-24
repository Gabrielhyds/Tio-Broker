<?php
// Inclui o arquivo de configuração, o modelo de Usuário e o modelo de Chat.
require_once '../../config/config.php';
require_once '../../models/Chat.php';

// Inicia ou resume a sessão para verificar se o usuário está logado.
session_start();

// Verifica se o usuário está logado e se o ID da conversa foi fornecido.
if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    // Se não, retorna um código de erro HTTP 401 (Não Autorizado) e encerra o script.
    http_response_code(401);
    exit;
}

// Obtém o ID do usuário logado e o ID da conversa da URL.
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

// Cria uma nova instância do modelo Chat.
$chat = new Chat($connection);
// Busca todas as mensagens e suas reações da conversa ativa.
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

// Se não houver mensagens, exibe uma mensagem padrão e encerra o script.
if (empty($mensagens)) {
    echo '<p class="text-center text-gray-400">Nenhuma mensagem ainda. Inicie a conversa.</p>';
    exit;
}

// Itera sobre cada mensagem para exibi-la.
foreach ($mensagens as $m) {
    // Verifica se a mensagem foi enviada pelo usuário logado.
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);

    // Define classes CSS com base em quem enviou a mensagem para alinhamento (direita/esquerda).
    $alinhamento = $isMinha ? 'items-end' : 'items-start';
    $bgColor = $isMinha ? 'bg-green-100' : 'bg-gray-200'; // Verde para minhas mensagens, cinza para as dos outros.
    $textAlign = 'text-left'; // Mantém o texto alinhado à esquerda dentro da bolha.
    $floatDir = $isMinha ? 'ml-auto flex-row-reverse' : 'mr-auto flex-row'; // Inverte a ordem do botão de opções e da bolha.
    $orderBtn = $isMinha ? 'order-1' : 'order-2'; // Inverte a ordem do botão ⋮ dentro do flex container.

    // Define o status de leitura (duplo check). Só aparece nas minhas mensagens.
    $statusLida = $isMinha
        ? ($m['lida']
            ? '<span class="text-green-600 ml-2">✔️</span>' // Verde se lida.
            : '<span class="text-gray-500 ml-2">✔️</span>') // Cinza se não lida.
        : '';

    // Inicia o contêiner principal da mensagem.
    echo '<div class="flex flex-col ' . $alinhamento . ' mb-2">';
    // Inicia o contêiner que alinha o botão de opções e a bolha da mensagem.
    echo '<div class="flex items-start gap-2 ' . $floatDir . '">';

    // Início do botão de opções (menu de 3 pontinhos).
    echo '<div class="relative ' . $orderBtn . '">';
    echo '<button class="btn-opcoes text-gray-500 hover:text-black px-1">⋮</button>';
    // O menu dropdown, inicialmente oculto.
    echo '<div class="dropdown-opcoes absolute z-50 mt-1 w-32 bg-white border rounded shadow hidden">';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100 btn-reagir" data-id-mensagem="' . $m['id_mensagem'] . '">Reagir</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100">Editar</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100">Responder</button>';
    echo '<button class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">Excluir</button>';
    echo '</div>';
    echo '</div>'; // Fim do botão de opções.

    // A bolha da mensagem.
    echo '<div class="mensagem group relative max-w-[75%] rounded-xl px-4 py-2 ' . $bgColor . ' ' . $textAlign . ' whitespace-pre-wrap break-words" data-id-mensagem="' . $m['id_mensagem'] . '">';
    // Nome do remetente.
    echo '<div class="text-sm font-semibold">' . htmlspecialchars($m['nome_usuario']) . ':</div>';
    // Conteúdo da mensagem. `nl2br` converte quebras de linha em tags <br>.
    echo '<div class="text-sm">' . nl2br(htmlspecialchars($m['mensagem'])) . '</div>';
    // Hora do envio e status de leitura.
    echo '<div class="text-xs text-gray-500 mt-1">' . date('H:i', strtotime($m['data_envio'])) . $statusLida . '</div>';
    echo '</div>'; // Fim da bolha da mensagem.

    echo '</div>'; // Fim do contêiner da linha (botão + bolha).

    // Seção para exibir as reações.
    if (!empty($m['reacoes'])) {
        // Alinha as reações com a bolha da mensagem correspondente.
        echo '<div class="flex gap-1 mt-1 ' . ($isMinha ? 'justify-end' : 'justify-start') . '">';
        // Itera sobre cada tipo de reação na mensagem.
        foreach ($m['reacoes'] as $reacao) {
            // Exibe o emoji da reação e a contagem. `title` mostra os nomes de quem reagiu.
            echo '<span class="text-sm bg-white border rounded-full px-2 py-0.5 shadow" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">';
            echo htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'];
            echo '</span>';
        }
        echo '</div>'; // Fim da seção de reações.
    }

    echo '</div>'; // Fim do contêiner principal da mensagem.
}
