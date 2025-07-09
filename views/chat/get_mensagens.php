<?php
// Arquivo: get_mensagens.php (Layout Premium com Agrupamento e Datas)

// Inclui o arquivo de configuração e o modelo de Chat.
require_once '../../config/config.php';
require_once '../../models/Chat.php';

// Inicia ou resume a sessão para verificar se o usuário está logado.
session_start();

// Verifica se o usuário está logado e se o ID da conversa foi fornecido.
if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_GET['id_conversa'])) {
    http_response_code(401);
    exit;
}

// Obtém os IDs.
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_conversa_ativa = $_GET['id_conversa'];

// Cria uma nova instância do modelo Chat.
$chat = new Chat($connection);
// Busca todas as mensagens e suas reações da conversa ativa.
// **IMPORTANTE**: Seu método listarMensagensDaConversa() precisa retornar o campo 'com_assinatura'.
$mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);

// Se não houver mensagens, exibe uma mensagem padrão.
if (empty($mensagens)) {
    echo '<p class="text-center text-gray-400 self-center h-full flex items-center justify-center">Nenhuma mensagem ainda. Inicie a conversa.</p>';
    exit;
}

/**
 * Função auxiliar para formatar a data do separador.
 * @param string $dateString A data no formato 'Y-m-d'.
 * @return string A data formatada (Hoje, Ontem, ou dd de Mês).
 */
function formatarDataSeparador($dateString)
{
    $date = new DateTime($dateString);
    $hoje = new DateTime('today');
    $ontem = new DateTime('yesterday');

    if ($date->format('Y-m-d') === $hoje->format('Y-m-d')) {
        return 'Hoje';
    }
    if ($date->format('Y-m-d') === $ontem->format('Y-m-d')) {
        return 'Ontem';
    }
    // Formato de data localizado em português
    $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'd \'de\' MMMM');
    return $formatter->format($date);
}

$dataAnterior = null; // Para controlar o separador de data

// Itera sobre cada mensagem para exibi-la.
foreach ($mensagens as $key => $m) {
    // --- LÓGICA DE AGRUPAMENTO E DATA ---
    $dataAtual = date('Y-m-d', strtotime($m['data_envio']));

    // Exibe o separador de data se o dia mudou.
    if ($dataAtual !== $dataAnterior) {
        echo '<div class="flex justify-center my-4">
                <span class="bg-gray-200 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">' . formatarDataSeparador($dataAtual) . '</span>
              </div>';
        $dataAnterior = $dataAtual;
    }

    $mensagemAnterior = $mensagens[$key - 1] ?? null;
    $remetenteAnterior = $mensagemAnterior ? date('Y-m-d', strtotime($mensagemAnterior['data_envio'])) === $dataAtual ? $mensagemAnterior['id_usuario'] : null : null;

    $isMinha = ($m['id_usuario'] == $id_usuario_logado);
    $isAgrupada = ($m['id_usuario'] === $remetenteAnterior);

    // **NOVO**: Verifica se a mensagem foi enviada com assinatura.
    // Assumimos que o banco de dados retorna 1 para sim e 0 ou null para não.
    $comAssinatura = !empty($m['com_assinatura']);

    // --- DEFINIÇÃO DE CLASSES CSS ---
    $containerClasses = $isAgrupada ? 'mt-1' : 'mt-4';
    $bolhaClasses = $isMinha ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border';

    // Ajusta o arredondamento dos cantos para criar o efeito de agrupamento
    if ($isAgrupada) {
        $bolhaClasses .= $isMinha ? ' rounded-tr-lg' : ' rounded-tl-lg';
    }

    // Gera um avatar placeholder com a inicial do nome.
    $avatar_inicial = mb_strtoupper(mb_substr($m['nome_usuario'], 0, 1));
    $avatar_url = "https://placehold.co/100x100/7c3aed/ffffff?text={$avatar_inicial}";

    // Define o status de leitura com o duplo check azul/cinza.
    $statusLida = $isMinha ? '<span class="text-xs ml-2 ' . ($m['lida'] ? 'text-blue-400' : 'text-gray-300') . '">✓✓</span>' : '';

    // Monta o HTML para as reações, se existirem.
    $reacoesHtml = '';
    if (!empty($m['reacoes'])) {
        $reacoesHtml .= '<div class="flex gap-1 mt-1.5 ' . ($isMinha ? 'justify-end' : 'justify-start') . '">';
        foreach ($m['reacoes'] as $reacao) {
            $reacoesHtml .= '<div class="text-sm bg-white border rounded-full px-2 py-0.5 shadow-sm cursor-pointer" title="' . htmlspecialchars($reacao['nomes_usuarios']) . '">' . htmlspecialchars($reacao['reacao']) . ' ' . $reacao['total'] . '</div>';
        }
        $reacoesHtml .= '</div>';
    }
?>
    <!-- Contêiner principal da mensagem -->
    <div class="flex items-start gap-3 max-w-xl <?= $isMinha ? 'flex-row-reverse ml-auto' : 'mr-auto' ?> <?= $containerClasses ?>">

        <!-- Avatar (só mostra se não for agrupado) -->
        <div class="w-10 h-10 flex-shrink-0">
            <?php if (!$isAgrupada): ?>
                <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full" alt="Avatar de <?= htmlspecialchars($m['nome_usuario']) ?>">
            <?php endif; ?>
        </div>

        <div class="flex flex-col <?= $isMinha ? 'items-end' : 'items-start' ?>">
            <!-- Bolha da Mensagem -->
            <div class="mensagem-bolha group relative rounded-2xl p-3 shadow-sm <?= $bolhaClasses ?>">

                <!-- **ALTERADO**: Nome do Remetente (só mostra se não for agrupado, não for minha E tiver assinatura) -->
                <?php if (!$isAgrupada && !$isMinha && $comAssinatura): ?>
                    <p class="font-bold text-sm mb-1 text-violet-600"><?= htmlspecialchars($m['nome_usuario']) ?></p>
                <?php endif; ?>

                <!-- Conteúdo da Mensagem (texto e hora) -->
                <div class="flex items-end">
                    <p class="text-sm pr-2"><?= nl2br(htmlspecialchars($m['mensagem'])) ?></p>
                    <span class="text-xs <?= $isMinha ? 'text-blue-200' : 'text-gray-500' ?> whitespace-nowrap"><?= date('H:i', strtotime($m['data_envio'])) ?></span>
                    <?= $statusLida ?>
                </div>

                <!-- Botão de Opções (3 pontinhos) que aparece no hover -->
                <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="btn-opcoes bg-white rounded-full p-1 shadow border text-gray-500 hover:text-gray-800" data-id-mensagem="<?= $m['id_mensagem'] ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                        </svg>
                    </button>
                    <!-- Menu Dropdown -->
                    <div class="dropdown-opcoes absolute z-40 right-0 mt-1 w-36 bg-white border rounded-lg shadow-xl hidden fade-in">
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 btn-reagir" data-id-mensagem="<?= $m['id_mensagem'] ?>">Reagir</button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar</button>
                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Responder</button>
                        <div class="border-t my-1"></div>
                        <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Excluir</button>
                    </div>
                </div>
            </div>

            <!-- Reações -->
            <?= $reacoesHtml ?>
        </div>
    </div>
<?php
} // Fim do loop foreach
?>