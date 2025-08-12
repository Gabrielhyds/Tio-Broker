<?php
// Caminho: views/chat/get_mensagens.php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';
session_start();

use App\Models\Chat;

/* ---------- Helpers de avatar (foto real ou inicial do nome) ---------- */
function avatar_placeholder(string $nome = 'U'): string {
    $ini = mb_strtoupper(mb_substr($nome ?: 'U', 0, 1));
    return 'https://placehold.co/100x100/c4b5fd/4c1d95?text=' . urlencode($ini);
}
function avatar_url(?string $foto, string $nome = 'U'): string {
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
if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

$id_usuario_logado = (int)$_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = (int)$_GET['id_conversa'];

/* ------------------------------ Dados ---------------------------------- */
$chat = new Chat($connection); // $connection deve ser \mysqli vindo do config.php
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-center text-gray-500">Nenhuma mensagem ainda. Envie a primeira!</p>';
    exit;
}

/* ---------------------------- Renderização ----------------------------- */
$remetenteAnterior = null;

foreach ($mensagens as $m) {
    $isMinha     = ((int)$m['id_usuario'] === $id_usuario_logado);
    $isAgrupada  = ($remetenteAnterior === (int)$m['id_usuario']);
    $nomeUser    = $m['nome_usuario'] ?? 'U';
    $avatar_url  = avatar_url($m['foto'] ?? null, $nomeUser);

    // Reações (agrupadas)
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
    ?>
    <div class="w-full flex <?= $isMinha ? 'justify-end' : 'justify-start' ?> <?= $isAgrupada ? 'mt-1' : 'mt-4' ?> message-wrapper" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">
        <div class="flex <?= $isMinha ? 'flex-row-reverse' : 'flex-row' ?> items-start gap-3 max-w-[80%] group">
            <div class="w-10 h-10 flex-shrink-0">
                <?php if (!$isAgrupada): ?>
                    <img src="<?= htmlspecialchars($avatar_url) ?>" class="w-full h-full rounded-full object-cover" alt="">
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-1 <?= $isMinha ? 'items-end' : 'items-start' ?>">
                <?php if (!empty($m['apagada'])): ?>
                    <div class="p-3 rounded-2xl bg-gray-100 text-gray-500 italic text-sm">Esta mensagem foi apagada.</div>
                <?php else: ?>
                    <div class="relative p-3 rounded-2xl shadow-sm <?= $isMinha ? 'bg-violet-600 text-white rounded-br-lg' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-lg' ?>">
                        <p class="text-sm message-content" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">
                            <?= nl2br(htmlspecialchars($m['mensagem'] ?? '')) ?>
                            <?php if (!empty($m['editada_em'])): ?>
                                <span class="text-xs text-gray-200 md:text-gray-400 italic ml-2">(editado)</span>
                            <?php endif; ?>
                        </p>

                        <?php if ($isMinha): ?>
                        <div class="absolute -top-3 right-8 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="btn-editar text-xs" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">✏️</button>
                            <button class="btn-apagar text-xs" data-id-mensagem="<?= (int)$m['id_mensagem'] ?>">🗑️</button>
                        </div>
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
