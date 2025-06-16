<?php
// Inclui os arquivos necessários de configuração e models
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Chat.php';

// IMPORTANTE: Para o funcionamento correto dos emojis, certifique-se de que a conexão
// com o banco de dados e as tabelas ('mensagens') estejam utilizando o charset 'utf8mb4'.

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

// Define a lista de usuários visíveis com base na permissão
if ($permissao === 'SuperAdmin') {
    // SuperAdmin pode ver o seletor de imobiliárias para filtrar
    $listaImobiliarias = $usuarioModel->listarImobiliariasComUsuarios();

    if ($id_imobiliaria_filtro) {
        // Se um filtro foi selecionado, lista os usuários da imobiliária escolhida
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_filtro, $id_usuario_logado);
    } else {
        // ALTERAÇÃO: Se nenhum filtro foi selecionado, a lista de usuários permanece vazia.
        $usuariosDisponiveis = [];
    }
} else {
    // Para os demais perfis (Admin, Coordenador, Corretor), mostra apenas usuários da mesma imobiliária
    if ($id_imobiliaria_usuario) {
        // Usa o ID da imobiliária do próprio usuário logado para buscar os colegas
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_usuario, $id_usuario_logado);
    }
}

// Busca as últimas mensagens e notificações de não lidas
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);

// Ordena a lista de usuários para que as conversas mais recentes apareçam no topo
// Esta função funciona corretamente mesmo se $usuariosDisponiveis for um array vazio.
usort($usuariosDisponiveis, function ($a, $b) use ($ultimasMensagens) {
    // Pega o timestamp da última mensagem para cada usuário
    $timestampA = isset($ultimasMensagens[$a['id_usuario']]) ? strtotime($ultimasMensagens[$a['id_usuario']]['data_envio']) : 0;
    $timestampB = isset($ultimasMensagens[$b['id_usuario']]) ? strtotime($ultimasMensagens[$b['id_usuario']]['data_envio']) : 0;

    // Se os timestamps são iguais, mantém a ordem original
    if ($timestampA == $timestampB) {
        return 0;
    }

    // Compara os timestamps para ordenar do mais recente para o mais antigo (descendente)
    return ($timestampA > $timestampB) ? -1 : 1;
});

// Lógica para abrir uma conversa ativa
$id_destino = $_GET['id_destino'] ?? null;
$id_conversa_ativa = null;
$mensagens = [];

if ($id_destino) {
    // Busca ou cria uma conversa privada com o usuário de destino
    $id_conversa_ativa = $chat->buscarConversaPrivadaEntre($id_usuario_logado, $id_destino);
    if (!$id_conversa_ativa) {
        $id_conversa_ativa = $chat->criarConversaPrivada($id_usuario_logado, $id_destino);
    }
    // Marca as mensagens como lidas e carrega o histórico
    $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);
    $mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Chat Interno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
    <!-- Adiciona a biblioteca de seletor de emojis -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
</head>

<body class="bg-light">
    <?php
    // Inclui o dashboard correspondente ao perfil do usuário
    if ($_SESSION['usuario']['permissao'] === 'SuperAdmin') {
        include_once '../dashboards/dashboard_superadmin.php';
    } elseif ($_SESSION['usuario']['permissao'] === 'Admin') {
        include_once '../dashboards/dashboard_admin.php';
    } elseif ($_SESSION['usuario']['permissao'] === 'Coordenador') {
        include_once '../dashboards/dashboard_coordenador.php';
    } else {
        include_once '../dashboards/dashboard_corretor.php';
    }
    ?>
    <div class="container mt-5">
        <a href="<?= htmlspecialchars($dashboardUrl ?? '../../index.php') ?>" class="btn btn-secondary mb-2">Voltar</a>
        <div class="row">

            <!-- Lista de usuários -->
            <div class="col-md-4">
                <h4>Usuários</h4>

                <?php if ($permissao === 'SuperAdmin'): ?>
                <!-- Formulário de filtro de imobiliária (apenas para SuperAdmin) -->
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
                // ALTERAÇÃO: Lógica de exibição da lista ou de mensagens de status
                if ($permissao === 'SuperAdmin' && !$id_imobiliaria_filtro) :
                ?>
                <div class="alert alert-info" role="alert">
                    Selecione uma imobiliária para filtrar os usuários.
                </div>
                <?php
                elseif (empty($usuariosDisponiveis)) :
                ?>
                <div class="alert alert-secondary" role="alert">
                    Nenhum usuário para exibir.
                </div>
                <?php
                else :
                ?>
                <ul class="list-group">
                    <?php foreach ($usuariosDisponiveis as $u): ?>
                    <?php
                            // Pula a exibição do próprio usuário na lista para evitar auto-chat.
                            if ($u['id_usuario'] == $id_usuario_logado) continue;
                            ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>"
                            class="text-decoration-none flex-grow-1">
                            <?= htmlspecialchars($u['nome']) ?>
                            <small id="ultima-msg-<?= $u['id_usuario'] ?>" class="d-block text-muted">
                                <?= isset($ultimasMensagens[$u['id_usuario']])
                                            ? htmlspecialchars(substr($ultimasMensagens[$u['id_usuario']]['mensagem'], 0, 30)) . '...'
                                            : 'Nenhuma conversa iniciada' ?>
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
                    <?php if ($id_conversa_ativa): ?>
                    <?php if (!empty($mensagens)): ?>
                    <?php foreach ($mensagens as $m): ?>
                    <?php
                                $isMinha = ($m['id_usuario'] == $id_usuario_logado);
                                $classe = $isMinha ? 'mensagem-direita' : 'mensagem-esquerda';
                                $status = $isMinha
                                    ? ($m['lida'] ? '<span class="text-success">✔️</span>' : '<span class="text-muted">✔️</span>')
                                    : '';
                                ?>
                    <div class="mensagem <?= $classe ?>">
                        <div><strong><?= htmlspecialchars($m['nome_usuario']) ?>:</strong></div>
                        <div><?= nl2br(htmlspecialchars($m['mensagem'])) ?></div>
                        <small class="text-muted d-block">
                            <?= date('d/m/Y H:i', strtotime($m['data_envio'])) ?>
                            <?= $status ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p class="text-muted">Nenhuma mensagem ainda. Inicie a conversa.</p>
                    <?php endif; ?>
                    <?php else: ?>
                    <p class="text-muted">Selecione um colaborador ao lado para iniciar uma conversa.</p>
                    <?php endif; ?>
                </div>

                <?php if ($id_conversa_ativa): ?>
                <form action="../../controllers/ChatController.php" method="POST">
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

    <script>
    // Mantém uma referência ao temporizador para poder limpá-lo se necessário
    let pollingInterval;

    // Função para carregar dinamicamente as mensagens da conversa ativa
    function carregarMensagens() {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
        if (!idConversa) return;

        fetch(`get_mensagens.php?id_conversa=${idConversa}&t=${new Date().getTime()}`)
            .then(res => res.text())
            .then(html => {
                const chatBox = document.getElementById("mensagens");
                const shouldScroll = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 20;
                const scrollPosition = chatBox.scrollTop;
                chatBox.innerHTML = html;
                if (shouldScroll) {
                    scrollParaFimDoChat();
                } else {
                    chatBox.scrollTop = scrollPosition;
                }
            });
    }

    // Função para atualizar as notificações e última mensagem na lista de usuários
    function atualizarStatusListaUsuarios() {
        fetch(`get_notificacoes.php?t=${new Date().getTime()}`)
            .then(res => res.ok ? res.json() : Promise.reject('Network response was not ok.'))
            .then(data => {
                if (!data) return;
                document.querySelectorAll('.list-group-item a').forEach(link => {
                    const userId = new URLSearchParams(link.href.split('?')[1]).get('id_destino');
                    if (!userId) return;

                    const badge = document.getElementById(`badge-${userId}`);
                    const msgEl = document.getElementById(`ultima-msg-${userId}`);
                    if (!badge || !msgEl) return;

                    const userData = data[userId];
                    if (userData) {
                        let mensagem = userData.mensagem || "Nenhuma conversa iniciada";
                        msgEl.textContent = mensagem.length > 30 ? mensagem.substring(0, 30) + '...' :
                            mensagem;
                        const totalNaoLidas = parseInt(userData.total_nao_lidas, 10) || 0;
                        if (totalNaoLidas > 0) {
                            badge.textContent = totalNaoLidas;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    } else {
                        badge.classList.add('d-none');
                    }
                });
            })
            .catch(error => console.error('Erro ao buscar notificações:', error));
    }

    // Função para rolar a caixa de chat para a última mensagem
    function scrollParaFimDoChat() {
        const box = document.getElementById('mensagens');
        if (box) box.scrollTop = box.scrollHeight;
    }

    // Executa as funções quando a página é carregada
    window.onload = function() {
        scrollParaFimDoChat();

        pollingInterval = setInterval(() => {
            carregarMensagens();
            atualizarStatusListaUsuarios();
        }, 3000);

        // --- LÓGICA DO SELETOR DE EMOJIS ---
        const emojiBtn = document.getElementById('emoji-btn');
        const emojiPicker = document.querySelector('emoji-picker');
        const messageInput = document.getElementById('mensagem-input');

        if (emojiBtn && emojiPicker && messageInput) {
            // Mostra/esconde o seletor de emojis
            emojiBtn.addEventListener('click', () => {
                emojiPicker.classList.toggle('d-none');
            });

            // Insere o emoji no campo de texto ao ser clicado
            emojiPicker.addEventListener('emoji-click', event => {
                messageInput.value += event.detail.emoji.unicode;
                messageInput.focus();
            });

            // Opcional: esconde o picker se clicar fora dele
            document.addEventListener('click', (event) => {
                if (!emojiPicker.contains(event.target) && !emojiBtn.contains(event.target)) {
                    emojiPicker.classList.add('d-none');
                }
            });
        }
    };
    </script>
</body>

</html>