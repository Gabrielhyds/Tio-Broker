<?php
// Inclui os arquivos necessários de configuração e models
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Chat.php';

// Inicia a sessão para obter os dados do usuário logado
session_start();

// Redireciona para o login se não houver um usuário na sessão
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Instancia os models
$chat = new Chat($connection);
$usuarioModel = new Usuario($connection);

// Obtém os dados do usuário logado a partir da sessão
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$permissao = $_SESSION['usuario']['permissao'];
$id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;

$id_imobiliaria_filtro = $_GET['id_imobiliaria'] ?? null;
$listaImobiliarias = [];
$usuariosDisponiveis = [];

// Lógica de filtro para SuperAdmin
if ($permissao === 'SuperAdmin') {
    $listaImobiliarias = $usuarioModel->listarImobiliariasComUsuarios();
    if ($id_imobiliaria_filtro) {
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_filtro, $id_usuario_logado);
    }
} else {
    if ($id_imobiliaria_usuario) {
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_usuario, $id_usuario_logado);
    }
}

// Busca as últimas mensagens e notificações de não lidas
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);

// Ordena a lista de usuários
usort($usuariosDisponiveis, function ($a, $b) use ($ultimasMensagens) {
    $timestampA = isset($ultimasMensagens[$a['id_usuario']]) ? strtotime($ultimasMensagens[$a['id_usuario']]['data_envio']) : 0;
    $timestampB = isset($ultimasMensagens[$b['id_usuario']]) ? strtotime($ultimasMensagens[$b['id_usuario']]['data_envio']) : 0;
    if ($timestampA == $timestampB) return 0;
    return ($timestampA > $timestampB) ? -1 : 1;
});

// Lógica para abrir uma conversa ativa
$id_destino = $_GET['id_destino'] ?? null;
$id_conversa_ativa = null;
if ($id_destino) {
    $id_conversa_ativa = $chat->buscarConversaPrivadaEntre($id_usuario_logado, $id_destino);
    if (!$id_conversa_ativa) {
        $id_conversa_ativa = $chat->criarConversaPrivada($id_usuario_logado, $id_destino);
    }
    $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Chat Interno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <link href="assets/style.css" rel="stylesheet">

    <!-- CSS CORRIGIDO para Reações -->
    <style>
    .mensagem-wrapper {
        position: relative;
        margin-bottom: 5px;
        display: flex;
        flex-direction: column;
    }

    .minha-mensagem {
        align-items: flex-end;
    }

    .outra-mensagem {
        align-items: flex-start;
    }

    /* Adiciona um padding para o botão de reagir não sobrepor a mensagem */
    .minha-mensagem .mensagem {
        margin-left: 40px;
    }

    .outra-mensagem .mensagem {
        margin-right: 40px;
    }

    .mensagem {
        position: relative;
    }

    .mensagem .btn-reagir {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background-color: rgba(0, 0, 0, 0.05);
        color: #6c757d;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .minha-mensagem .mensagem .btn-reagir {
        left: -38px;
    }

    .outra-mensagem .mensagem .btn-reagir {
        right: -38px;
    }

    .mensagem-wrapper:hover .btn-reagir {
        opacity: 1;
    }

    /* Seletor agora é FIXO para evitar problemas com scroll */
    .seletor-reacao {
        position: fixed;
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 5px;
        display: flex;
        gap: 5px;
        z-index: 1051;
        transform: scale(0.9);
        opacity: 0;
        transition: transform 0.1s ease-out, opacity 0.1s ease-out;
        pointer-events: none;
    }

    .seletor-reacao.visivel {
        transform: scale(1);
        opacity: 1;
        pointer-events: auto;
    }

    .seletor-reacao .emoji-reacao {
        font-size: 24px;
        cursor: pointer;
        padding: 3px;
        border-radius: 50%;
        transition: transform 0.1s, background-color 0.1s;
    }

    .seletor-reacao .emoji-reacao:hover {
        background-color: #eee;
        transform: scale(1.2);
    }

    .reacoes-container {
        padding: 0 8px;
        margin-top: -8px;
        z-index: 1;
    }

    .minha-mensagem .reacoes-container {
        align-self: flex-end;
        margin-right: 10px;
    }

    .outra-mensagem .reacoes-container {
        align-self: flex-start;
        margin-left: 10px;
    }

    .reacoes-container .badge {
        cursor: help;
    }

    .status-lida {
        font-size: 14px;
        margin-left: 5px;
        color: #6c757d;
    }

    .status-lida.visto {
        color: #198754;
    }
    </style>
</head>

<body class="bg-light">
    <?php
    if ($permissao === 'SuperAdmin') include_once '../dashboards/dashboard_superadmin.php';
    elseif ($permissao === 'Admin') include_once '../dashboards/dashboard_admin.php';
    elseif ($permissao === 'Coordenador') include_once '../dashboards/dashboard_coordenador.php';
    else include_once '../dashboards/dashboard_corretor.php';
    ?>

    <div class="container mt-5">
        <a href="<?= htmlspecialchars($dashboardUrl ?? '../../index.php') ?>" class="btn btn-secondary mb-2">Voltar</a>
        <div class="row">

            <!-- Lista de usuários -->
            <div class="col-md-4">
                <h4>Usuários</h4>
                <?php if ($permissao === 'SuperAdmin'): ?>
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <select name="id_imobiliaria" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Filtrar por Imobiliária --</option>
                            <?php foreach ($listaImobiliarias as $imob): ?>
                            <option value="<?= $imob['id_imobiliaria'] ?>"
                                <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($imob['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <?php endif; ?>

                <?php
                if ($permissao === 'SuperAdmin' && !$id_imobiliaria_filtro) :
                    echo '<div class="alert alert-info">Selecione uma imobiliária para filtrar.</div>';
                elseif (empty($usuariosDisponiveis)) :
                    echo '<div class="alert alert-secondary">Nenhum usuário para exibir.</div>';
                else :
                ?>
                <ul class="list-group">
                    <?php foreach ($usuariosDisponiveis as $u): ?>
                    <?php if ($u['id_usuario'] == $id_usuario_logado) continue; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>"
                            class="text-decoration-none flex-grow-1">
                            <?= htmlspecialchars($u['nome']) ?>
                            <small id="ultima-msg-<?= $u['id_usuario'] ?>" class="d-block text-muted">
                                <?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars(substr($ultimasMensagens[$u['id_usuario']]['mensagem'], 0, 30)) . '...' : 'Nenhuma conversa iniciada' ?>
                            </small>
                        </a>
                        <span id="badge-<?= $u['id_usuario'] ?>"
                            class="badge bg-danger rounded-pill <?= isset($notificacoes[$u['id_usuario']]) && $notificacoes[$u['id_usuario']] > 0 ? '' : 'd-none' ?>">
                            <?= $notificacoes[$u['id_usuario']] ?? '' ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>

            <!-- Conversa ativa -->
            <div class="col-md-8">
                <h4>Mensagens</h4>
                <div class="chat-box mb-3" id="mensagens">
                    <?php if (!$id_conversa_ativa): ?>
                    <p class="text-muted text-center mt-3">Selecione um colaborador ao lado para iniciar uma conversa.
                    </p>
                    <?php endif; ?>
                </div>

                <?php if ($id_conversa_ativa): ?>
                <form id="form-mensagem" action="../../controllers/ChatController.php" method="POST">
                    <input type="hidden" name="action" value="enviar_mensagem">
                    <input type="hidden" name="id_conversa" value="<?= $id_conversa_ativa ?>">
                    <input type="hidden" name="id_destino" value="<?= $id_destino ?>">
                    <?php if (isset($id_imobiliaria_filtro)): ?>
                    <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_filtro) ?>">
                    <?php endif; ?>

                    <div class="input-group position-relative">
                        <input type="text" name="mensagem" id="mensagem-input" class="form-control"
                            placeholder="Digite sua mensagem..." required autocomplete="off">
                        <button type="button" id="emoji-btn" class="btn btn-outline-secondary">😊</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                        <emoji-picker class="light position-absolute d-none"
                            style="bottom: 100%; right: 0; z-index: 1000;"></emoji-picker>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Seletor de reação (HTML único, controlado por JS) -->
    <div class="seletor-reacao" id="seletor-reacao-global">
        <span class="emoji-reacao" data-reacao="👍">👍</span>
        <span class="emoji-reacao" data-reacao="❤️">❤️</span>
        <span class="emoji-reacao" data-reacao="😂">😂</span>
        <span class="emoji-reacao" data-reacao="😮">😮</span>
        <span class="emoji-reacao" data-reacao="😢">😢</span>
        <span class="emoji-reacao" data-reacao="🙏">🙏</span>
    </div>


    <script>
    let pollingInterval;

    // --- Lógica de Reações (Refatorada) ---
    const seletorReacao = document.getElementById('seletor-reacao-global');

    function mostrarSeletorReacao(btnReagir) {
        const idMensagem = btnReagir.closest('.mensagem').dataset.idMensagem;
        seletorReacao.dataset.idMensagem = idMensagem; // Armazena o ID da mensagem no seletor

        const rect = btnReagir.getBoundingClientRect();

        // Calcula a posição do seletor para ficar acima do botão
        let top = rect.top - seletorReacao.offsetHeight - 5;
        let left = rect.left + (rect.width / 2) - (seletorReacao.offsetWidth / 2);

        // Ajusta para não sair da tela
        if (top < 0) top = rect.bottom + 5; // Se não couber em cima, põe embaixo
        if (left < 5) left = 5;
        if (left + seletorReacao.offsetWidth > window.innerWidth) {
            left = window.innerWidth - seletorReacao.offsetWidth - 5;
        }

        seletorReacao.style.top = `${top}px`;
        seletorReacao.style.left = `${left}px`;
        seletorReacao.classList.add('visivel');
    }

    function esconderSeletorReacao() {
        seletorReacao.classList.remove('visivel');
    }

    function enviarReacao(idMensagem, reacao) {
        const formData = new FormData();
        formData.append('action', 'reagir_mensagem');
        formData.append('id_mensagem', idMensagem);
        formData.append('reacao', reacao);

        fetch('../../controllers/ChatController.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    carregarMensagens();
                } else {
                    console.error('Erro ao reagir:', data.error || 'Erro desconhecido');
                }
            })
            .catch(err => console.error('Fetch error:', err));

        esconderSeletorReacao();
    }

    // --- Funções do Chat ---
    function carregarMensagens() {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
        if (!idConversa) return;

        fetch(`get_mensagens.php?id_conversa=${idConversa}&t=${new Date().getTime()}`)
            .then(res => res.text())
            .then(html => {
                const chatBox = document.getElementById("mensagens");
                if (!chatBox) return;

                const isScrolledToBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 30;
                const scrollPosition = chatBox.scrollTop;

                chatBox.innerHTML = html;

                if (isScrolledToBottom) {
                    scrollParaFimDoChat();
                } else {
                    chatBox.scrollTop = scrollPosition;
                }
            });
    }

    function scrollParaFimDoChat() {
        const box = document.getElementById('mensagens');
        if (box) box.scrollTop = box.scrollHeight;
    }

    // --- EVENT LISTENERS ---
    document.addEventListener('DOMContentLoaded', function() {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;

        if (idConversa) {
            carregarMensagens();
            scrollParaFimDoChat();
            pollingInterval = setInterval(carregarMensagens, 3000);
        }

        document.body.addEventListener('click', function(e) {
            const btnReagir = e.target.closest('.btn-reagir');
            if (btnReagir) {
                e.stopPropagation();
                const idMensagemAtual = btnReagir.closest('.mensagem').dataset.idMensagem;
                if (seletorReacao.classList.contains('visivel') && seletorReacao.dataset.idMensagem ===
                    idMensagemAtual) {
                    esconderSeletorReacao();
                } else {
                    mostrarSeletorReacao(btnReagir);
                }
                return;
            }

            const emojiClicado = e.target.closest('.emoji-reacao');
            if (emojiClicado && seletorReacao.classList.contains('visivel')) {
                const idMensagem = seletorReacao.dataset.idMensagem;
                const reacao = emojiClicado.dataset.reacao;
                enviarReacao(idMensagem, reacao);
                return;
            }

            if (!e.target.closest('.seletor-reacao')) {
                esconderSeletorReacao();
            }

            const emojiBtn = document.getElementById('emoji-btn');
            const emojiPicker = document.querySelector('emoji-picker');
            if (emojiBtn && emojiPicker) {
                if (emojiBtn.contains(e.target)) {
                    emojiPicker.classList.toggle('d-none');
                } else if (!emojiPicker.contains(e.target) && !e.target.closest('emoji-picker')) {
                    emojiPicker.classList.add('d-none');
                }
            }
        });

        const emojiPicker = document.querySelector('emoji-picker');
        const messageInput = document.getElementById('mensagem-input');
        if (emojiPicker && messageInput) {
            emojiPicker.addEventListener('emoji-click', event => {
                messageInput.value += event.detail.emoji.unicode;
                messageInput.focus();
            });
        }
    });
    </script>
</body>

</html>