<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/get_mensagens.php (VERSÃO CORRIGIDA)
|--------------------------------------------------------------------------
| O texto dos botões agora é gerado apenas pelo PHP no servidor.
| Os atributos 'data-i18n' e a classe 'translating' foram removidos
| para impedir que o JavaScript do lado do cliente os modifique,
| resolvendo o problema dos botões em branco.
*/

require_once '../../config/config.php';
require_once '../../models/Chat.php';
session_start();

if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

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

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

$chat = new Chat($connection);
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

if (empty($mensagens)) {
    echo '<p class="text-center text-gray-400 self-center h-full flex items-center justify-center">' . $t('messages.noMessages', 'Nenhuma mensagem ainda.') . '</p>';
    exit;
}

$formatarDataSeparador = function ($dateString, $lang) use ($t) {
    $date = new DateTime($dateString);
    $hoje = new DateTime('today');
    $ontem = new DateTime('yesterday');

    if ($date->format('Y-m-d') === $hoje->format('Y-m-d')) {
        return $t('dates.today', 'Hoje');
    }
    if ($date->format('Y-m-d') === $ontem->format('Y-m-d')) {
        return $t('dates.yesterday', 'Ontem');
    }

    $locale = ($lang === 'en') ? 'en_US' : 'pt_BR';
    $pattern = $t('dates.dateFormat', 'd \'de\' MMMM');
    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, $pattern);
    return $formatter->format($date);
};

$dataAnterior = null;

foreach ($mensagens as $key => $m) {
    $dataAtual = date('Y-m-d', strtotime($m['data_envio']));
    if ($dataAtual !== $dataAnterior) {
        echo '<div class="flex justify-center my-4">
                    <span class="bg-gray-200 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">' . $formatarDataSeparador($dataAtual, $lang) . '</span>
                  </div>';
        $dataAnterior = $dataAtual;
    }

    $mensagemAnterior = $mensagens[$key - 1] ?? null;
    $remetenteAnterior = $mensagemAnterior ? (date('Y-m-d', strtotime($mensagemAnterior['data_envio'])) === $dataAtual ? $mensagemAnterior['id_usuario'] : null) : null;
    $isMinha = ($m['id_usuario'] == $id_usuario_logado);
    $isAgrupada = ($m['id_usuario'] === $remetenteAnterior);
    $comAssinatura = !empty($m['com_assinatura']);
    $containerClasses = $isAgrupada ? 'mt-1' : 'mt-4';
    $bolhaClasses = $isMinha ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border';
    if ($isAgrupada) {
        $bolhaClasses .= $isMinha ? ' rounded-tr-lg' : ' rounded-tl-lg';
    }
    $avatar_inicial = mb_strtoupper(mb_substr($m['nome_usuario'], 0, 1));
    $avatar_url = "https://placehold.co/100x100/7c3aed/ffffff?text={$avatar_inicial}";
    $statusLida = $isMinha ? '<span class="text-xs ml-2 ' . ($m['lida'] ? 'text-blue-400' : 'text-gray-300') . '">✓✓</span>' : '';
    $reacoesHtml = '';
    if (!empty($m['reacoes'])) {
        $reacoesHtml .= '<div class="flex gap-1 mt-1.5 ' . ($isMinha ? 'justify-end' : 'justify-start') . '">';
        foreach ($m['reacoes'] as $reacao) {
            $reacoesHtml .= '<div class="text-sm bg-white border rounded-full px-2 py-0.5 shadow-sm cursor-pointer" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">' . htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'] . '</div>';
        }
        $reacoesHtml .= '</div>';
    }
?>
    <div class="flex items-start gap-3 max-w-xl <?= $isMinha ? 'flex-row-reverse ml-auto' : 'mr-auto' ?> <?= $containerClasses ?>">
        <div class="w-10 h-10 flex-shrink-0">
            <?php if (!$isAgrupada): ?>
                <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full" alt="Avatar de <?= htmlspecialchars($m['nome_usuario']) ?>">
            <?php endif; ?>
        </div>
        <div class="flex flex-col <?= $isMinha ? 'items-end' : 'items-start' ?>">
            <div class="mensagem-bolha group relative rounded-2xl p-3 shadow-sm <?= $bolhaClasses ?>">
                <?php if (!$isAgrupada && !$isMinha && $comAssinatura): ?>
                    <p class="font-bold text-sm mb-1 text-violet-600"><?= htmlspecialchars($m['nome_usuario']) ?></p>
                <?php endif; ?>
                <div class="flex items-end">
                    <p class="text-sm pr-2"><?= nl2br(htmlspecialchars($m['mensagem'])) ?></p>
                    <span class="text-xs <?= $isMinha ? 'text-blue-200' : 'text-gray-500' ?> whitespace-nowrap"><?= date('H:i', strtotime($m['data_envio'])) ?></span>
                    <?= $statusLida ?>
                </div>
                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="btn-opcoes bg-white rounded-full p-1 shadow border text-gray-500 hover:text-gray-800" data-id-mensagem="<?= $m['id_mensagem'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                        </svg>
                    </button>
                    <div class="dropdown-opcoes absolute z-40 right-0 mt-1 w-36 bg-white border rounded-lg shadow-xl hidden">
                        <!-- **CORREÇÃO**: Removidos data-i18n e a classe 'translating' -->
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 btn-reagir" data-id-mensagem="<?= $m['id_mensagem'] ?>"><?= $t('options.react', 'Reagir') ?></button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $t('options.edit', 'Editar') ?></button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $t('options.reply', 'Responder') ?></button>
                        <div class="border-t my-1"></div>
                        <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"><?= $t('options.delete', 'Excluir') ?></button>
                    </div>
                </div>
            </div>
            <?= $reacoesHtml ?>
        </div>
    </div>
<?php
} // Fim do loop
?>
