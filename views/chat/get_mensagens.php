<?php
// Caminho: views/chat/get_mensagens.php
require_once '../../config/config.php';
// ... (código existente requires/session) ...
require_once '../../vendor/autoload.php';
session_start();

use App\Models\Chat;

/* ---------- Helpers de avatar (foto real ou inicial do nome) ---------- */
function avatar_placeholder(string $nome = 'U'): string {
// ... (código existente do helper) ...
    $ini = mb_strtoupper(mb_substr($nome ?: 'U', 0, 1));
    return 'https://placehold.co/100x100/e0e0e0/555555?text=' . urlencode($ini);
}
function avatar_url(?string $foto, string $nome = 'U'): string {
// ... (código existente do helper) ...
    if (!empty($foto)) {
        // Se já vier URL absoluta (CDN/S3) ou data URI, usa direto
        if (preg_match('~^(https?://|data:image/)~i', $foto)) {
            return $foto;
        }
        // Caso contrário, monta caminho local
        return '../../uploads/' . ltrim($foto, '/');
    }
    return avatar_placeholder($nome);
}

/* ------------------ Autorização e parâmetros obrigatórios -------------- */
// ... (código existente da autorização) ...
if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

$id_usuario_logado = (int)$_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = (int)$_GET['id_conversa'];

/* ------------------------------ Dados ---------------------------------- */
// ... (código existente dos dados) ...
$chat = new Chat($connection); // $connection deve ser \mysqli vindo do config.php
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
// ... (código existente de empty) ...
    echo '<p class="text-center text-gray-500">Nenhuma mensagem ainda. Envie a primeira!</p>';
    exit;
}

/* ---------------------------- Renderização ----------------------------- */
$remetenteAnterior = null;

foreach ($mensagens as $m) {
    $isMinha = ((int)$m['id_usuario'] === $id_usuario_logado);
// ... (código existente $isAgrupada, $nomeUser, $avatar_url) ...
    $isAgrupada = ($remetenteAnterior === (int)$m['id_usuario']);
    $nomeUser = $m['nome_usuario'] ?? 'U';
    $avatar_url = avatar_url($m['foto'] ?? null, $nomeUser);

    // Reações (agrupadas)
// ... (código existente $reacoesHtml) ...
    $reacoesHtml = '';
    if (!empty($m['reacoes'])) {
        $reacoesHtml .= '<div class="flex gap-1 mt-1.5 ' . ($isMinha ? 'justify-end' : '') . '">';
        foreach ($m['reacoes'] as $reacao) {
            $reacoesHtml .= '<div class="text-xs bg-white/70 backdrop-blur-sm border rounded-full px-2 py-0.5 shadow-sm cursor-pointer" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">';
            $reacoesHtml .= htmlspecialchars($reacao['reacao']) . ' ' . (int)$reacao['total'];
            $reacoesHtml .= '</div>';
        }
        $reacoesHtml .= '</div>';
    }

    // --- LÓGICA DE CONTEÚDO (IMAGEM OU TEXTO) --- (ADICIONADO)
    $mensagemTexto = $m['mensagem'] ?? '';
    $isUrlImagem = preg_match('/\.(jpeg|jpg|gif|png|webp|bmp)$/i', $mensagemTexto);
    $conteudoHtml = '';
    $classesBolha = '';

    if ($isUrlImagem) {
        $conteudoHtml = '<img src="' . htmlspecialchars($mensagemTexto) . '" 
                              class="rounded-lg max-w-xs md:max-w-sm object-cover" 
                              alt="Imagem anexa" 
                              loading="lazy"
                              onerror="this.src=\'https://placehold.co/300x200/ef4444/white?text=Imagem+inválida\'">';
        
        $classesBolha = $isMinha 
            ? 'bg-green-200 rounded-2xl shadow-sm rounded-br-lg' 
            : 'bg-white border border-gray-200 rounded-2xl shadow-sm rounded-bl-lg';
    } else {
        $conteudoHtml = '<p class="text-sm message-content" data-id-mensagem="' . (int)$m['id_mensagem'] . '">'
                      . nl2br(htmlspecialchars($mensagemTexto))
                      . (!empty($m['editada_em']) ? ' <span class="text-xs text-gray-500 italic ml-2">(editado)</span>' : '')
                      . '</p>';
        
        $classesBolha = $isMinha 
            ? 'p-3 bg-green-200 text-gray-900 rounded-2xl shadow-sm rounded-br-lg' 
            : 'p-3 bg-white text-gray-800 border border-gray-200 rounded-2xl shadow-sm rounded-bl-lg';
    }
    // --- FIM LÓGICA DE CONTEÚDO ---

    ?>
    <div class="w-full flex <?= $isMinha ? 'justify-end' : 'justify-start' ?> <?= $isAgrupada ? 'mt-1' : 'mt-4' ?> message-wrapper" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>" data-sender-id="<?= (int)$m['id_usuario'] ?>">
        <div class="flex <?= $isMinha ? 'flex-row-reverse' : 'flex-row' ?> items-start gap-3 max-w-[80%] group">
            <div class="w-10 h-10 flex-shrink-0">
                <?php if (!$isAgrupada): ?>
                    <img src="<?= htmlspecialchars($avatar_url) ?>" class="w-full h-full rounded-full object-cover" alt="" onerror="this.src='<?= avatar_placeholder($nomeUser) ?>'">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-1 <?= $isMinha ? 'items-end' : 'items-start' ?>">
                <?php if (!empty($m['apagada'])): ?>
                    <div class="p-3 rounded-2xl bg-gray-100 text-gray-500 italic text-sm">Esta mensagem foi apagada.</div>
                <?php else: ?>
                    
                    <div class="relative <?= $classesBolha ?> overflow-hidden"> <!-- MODIFICADO -->
                        
                        <?= $conteudoHtml ?> <!-- MODIFICADO -->

                        <?php if (!$isUrlImagem): // Só mostra botões de editar/apagar para texto (MODIFICADO) ?>
                            <?php if ($isMinha): ?>
                            <div class="absolute -top-3 right-8 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="btn-editar text-xs" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">✏️</button>
                                <button class="btn-apagar text-xs" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">🗑️</button>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <button class="btn-reagir absolute -top-3 right-0 bg-white rounded-full p-1 shadow border opacity-0 group-hover:opacity-100 transition-opacity" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">😊</button>
                    </div>
                <?php endif; ?>

                <?php if (empty($m['apagada'])): ?>
                <span class="text-xs text-gray-500 px-2">
                    <?= date('H:i', strtotime($m['data_envio'])) ?>
                </span>
                <?php endif; ?>

                <?= $reacoesHtml ?>
            </div>
        </div>
    </div>
    <?php
    $remetenteAnterior = (int)$m['id_usuario'];
}

