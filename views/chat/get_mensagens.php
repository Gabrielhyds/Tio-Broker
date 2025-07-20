<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/get_mensagens.php (VERSÃO ATUALIZADA E MAIS ROBUSTA)
|--------------------------------------------------------------------------
| - Adicionada verificação para garantir que os dados da mensagem existam
|   antes de tentar exibi-los. Isso evita erros e bolhas de mensagem vazias.
| - Adicionado um bloco de depuração (comentado) para facilitar a
|   identificação de problemas com os dados vindos do banco.
| - A formatação de data foi encapsulada em um try-catch para mais segurança.
*/

// Arquivo: get_mensagens.php

require_once '../../config/config.php';
require_once '../../models/Chat.php';
session_start();

if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

// --- TRADUÇÃO (Sem alterações) ---
$lang = $_GET['lang'] ?? 'pt-br';
$translations = [];
$translationFile = __DIR__ . "/../../lang/chat/{$lang}.json";
if (file_exists($translationFile)) {
    $translations = json_decode(file_get_contents($translationFile), true);
}
$t = function ($key, $default = '') use ($translations) {
    $keys = explode('.', $key);
    $value = $translations;
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }
    return is_string($value) ? $value : $default;
};


// --- LÓGICA PRINCIPAL ---
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

$chat = new Chat($connection);
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-center text-gray-400 self-center h-full flex items-center justify-center">' . $t('messages.noMessages', 'Nenhuma mensagem ainda.') . '</p>';
    exit;
}

// --- FUNÇÃO DE DATA MELHORADA ---
$formatarDataSeparador = function ($dateString, $lang) use ($t) {
    try {
        $date = new DateTime($dateString);
        $hoje = new DateTime('today');
        $ontem = new DateTime('yesterday');

        if ($date->format('Y-m-d') === $hoje->format('Y-m-d')) {
            return $t('dates.today', 'Hoje');
        }
        if ($date->format('Y-m-d') === $ontem->format('Y-m-d')) {
            return $t('dates.yesterday', 'Ontem');
        }

        if (class_exists('IntlDateFormatter')) {
            $locale = ($lang === 'en') ? 'en_US' : 'pt_BR';
            $pattern = $t('dates.dateFormat', 'd \'de\' MMMM');
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, $pattern);
            return $formatter->format($date);
        } else {
            // Fallback simples caso a extensão intl falhe mesmo assim
            return $date->format('d/m/Y');
        }
    } catch (Exception $e) {
        // Se houver qualquer erro com a data, retorna um formato seguro
        return date('d/m/Y', strtotime($dateString));
    }
};

$dataAnterior = null;

// --- LOOP DE MENSAGENS ATUALIZADO ---
foreach ($mensagens as $key => $m) {

    // ======================= BLOCO DE DEPURAÇÃO =======================
    // Se o problema persistir, remova as duas barras "//" da linha abaixo.
    // Isso vai quebrar o layout, mas mostrará na tela os dados da primeira mensagem.
    // if ($key === 0) { var_dump($m); exit; }
    // ==================================================================

    // **NOVO**: Verificação para pular mensagens com texto vazio ou nulo
    $mensagem_texto = trim($m['mensagem'] ?? '');
    if (empty($mensagem_texto)) {
        continue; // Pula para a próxima mensagem no loop
    }

    // **NOVO**: Acessando dados de forma segura com valores padrão
    $nome_usuario = htmlspecialchars($m['nome_usuario'] ?? 'Usuário');
    $data_envio_str = $m['data_envio'] ?? 'now';
    $id_usuario_msg = $m['id_usuario'] ?? null;
    $id_mensagem = $m['id_mensagem'] ?? null;
    $lida = $m['lida'] ?? false;
    $com_assinatura = !empty($m['com_assinatura']);
    $reacoes = $m['reacoes'] ?? [];


    $dataAtual = date('Y-m-d', strtotime($data_envio_str));
    if ($dataAtual !== $dataAnterior) {
        echo '<div class="flex justify-center my-4">
                  <span class="bg-gray-200 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">' . $formatarDataSeparador($dataAtual, $lang) . '</span>
              </div>';
        $dataAnterior = $dataAtual;
    }

    // Lógica para agrupar mensagens
    $mensagemAnterior = $mensagens[$key - 1] ?? null;
    $remetenteAnterior = null;
    if ($mensagemAnterior) {
        $dataMsgAnterior = date('Y-m-d', strtotime($mensagemAnterior['data_envio'] ?? 'now'));
        if ($dataMsgAnterior === $dataAtual) {
            $remetenteAnterior = $mensagemAnterior['id_usuario'] ?? null;
        }
    }

    $isMinha = ($id_usuario_msg == $id_usuario_logado);
    $isAgrupada = ($id_usuario_msg !== null && $id_usuario_msg === $remetenteAnterior);

    // Lógica de classes e HTML
    $containerClasses = $isAgrupada ? 'mt-1' : 'mt-4';
    $bolhaClasses = $isMinha ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border';
    if ($isAgrupada) {
        $bolhaClasses .= $isMinha ? ' rounded-tr-lg' : ' rounded-tl-lg';
    }
    $avatar_inicial = mb_strtoupper(mb_substr($nome_usuario, 0, 1));
    $avatar_url = "https://placehold.co/100x100/7c3aed/ffffff?text={$avatar_inicial}";
    $statusLida = $isMinha ? '<span class="text-xs ml-2 ' . ($lida ? 'text-blue-400' : 'text-gray-300') . '">✓✓</span>' : '';
    
    $reacoesHtml = '';
    if (!empty($reacoes)) {
        $reacoesHtml .= '<div class="flex gap-1 mt-1.5 ' . ($isMinha ? 'justify-end' : 'justify-start') . '">';
        foreach ($reacoes as $reacao) {
            $nomes = htmlspecialchars($reacao['nomes_usuarios'] ?? '');
            $emoji = htmlspecialchars($reacao['reacao'] ?? '');
            $total = (int)($reacao['total'] ?? 0);
            if (!empty($emoji) && $total > 0) {
                 $reacoesHtml .= "<div class=\"text-sm bg-white border rounded-full px-2 py-0.5 shadow-sm cursor-pointer\" title=\"{$nomes}\">{$emoji} {$total}</div>";
            }
        }
        $reacoesHtml .= '</div>';
    }
?>
    <!-- O HTML de renderização da bolha -->
    <div class="flex items-start gap-3 max-w-xl <?= $isMinha ? 'flex-row-reverse ml-auto' : 'mr-auto' ?> <?= $containerClasses ?>">
        <div class="w-10 h-10 flex-shrink-0">
            <?php if (!$isAgrupada): ?>
                <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full" alt="Avatar de <?= $nome_usuario ?>">
            <?php endif; ?>
        </div>
        <div class="flex flex-col <?= $isMinha ? 'items-end' : 'items-start' ?>">
            <div class="mensagem-bolha group relative rounded-2xl p-3 shadow-sm <?= $bolhaClasses ?>">
                <?php if (!$isAgrupada && !$isMinha && $com_assinatura): ?>
                    <p class="font-bold text-sm mb-1 text-violet-600"><?= $nome_usuario ?></p>
                <?php endif; ?>
                <div class="flex items-end">
                    <p class="text-sm pr-2"><?= nl2br(htmlspecialchars($mensagem_texto)) ?></p>
                    <span class="text-xs <?= $isMinha ? 'text-blue-200' : 'text-gray-500' ?> whitespace-nowrap"><?= date('H:i', strtotime($data_envio_str)) ?></span>
                    <?= $statusLida ?>
                </div>
                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="btn-opcoes bg-white rounded-full p-1 shadow border text-gray-500 hover:text-gray-800" data-id-mensagem="<?= $id_mensagem ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                        </svg>
                    </button>
                    <div class="dropdown-opcoes absolute z-40 right-0 mt-1 w-36 bg-white border rounded-lg shadow-xl hidden">
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 btn-reagir translating" data-i18n="options.react" data-id-mensagem="<?= $id_mensagem ?>"><?= $t('options.react', 'Reagir') ?></button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 translating" data-i18n="options.edit"><?= $t('options.edit', 'Editar') ?></button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 translating" data-i18n="options.reply"><?= $t('options.reply', 'Responder') ?></button>
                        <div class="border-t my-1"></div>
                        <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 translating" data-i18n="options.delete"><?= $t('options.delete', 'Excluir') ?></button>
                    </div>
                </div>
            </div>
            <?= $reacoesHtml ?>
        </div>
    </div>
<?php
} // Fim do loop
?>
