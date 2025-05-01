<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Chat.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$chat = new Chat($connection);
$usuarioModel = new Usuario($connection);
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];

// Lista todos os usuários (exceto o logado)
$usuariosDisponiveis = $usuarioModel->listarTodosComImobiliaria();
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);

$id_destino = $_GET['id_destino'] ?? null;
$id_conversa_ativa = null;
$mensagens = [];

if ($id_destino) {
    $id_conversa_ativa = $chat->buscarConversaPrivadaEntre($id_usuario_logado, $id_destino);

    if (!$id_conversa_ativa) {
        $id_conversa_ativa = $chat->criarConversaPrivada($id_usuario_logado, $id_destino);
    }

    // Marcar mensagens do outro usuário como lidas
    $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);

    // Recupera mensagens da conversa
    $mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat Interno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-box {
            border: 1px solid #dee2e6;
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #f8f9fa;
        }
        .mensagem {
            margin-bottom: 10px;
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 15px;
            clear: both;
        }
        .mensagem-direita {
            background-color: #d1e7dd;
            float: right;
            text-align: right;
        }
        .mensagem-esquerda {
            background-color: #e9ecef;
            float: left;
            text-align: left;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row">
        <!-- Lista de usuários -->
        <div class="col-md-4">
            <h4>Usuários</h4>
            <ul class="list-group">
                <?php foreach ($usuariosDisponiveis as $u): ?>
                    <?php if ($u['id_usuario'] != $id_usuario_logado): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>" class="text-decoration-none flex-grow-1">
                                <?= htmlspecialchars($u['nome']) ?>
                                <small id="ultima-msg-<?= $u['id_usuario'] ?>" class="d-block text-muted">
                                    <?= isset($ultimasMensagens[$u['id_usuario']]) 
                                        ? htmlspecialchars($ultimasMensagens[$u['id_usuario']]['mensagem']) 
                                        : 'Sem conversa ainda' ?>
                                </small>
                            </a>
                            <span id="badge-<?= $u['id_usuario'] ?>" class="badge bg-danger rounded-pill <?= isset($notificacoes[$u['id_usuario']]) ? '' : 'd-none' ?>">
                                <?= $notificacoes[$u['id_usuario']] ?? '' ?>
                            </span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
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
                    <p class="text-muted">Selecione um usuário ao lado para iniciar uma conversa.</p>
                <?php endif; ?>
            </div>

            <?php if ($id_conversa_ativa): ?>
                <form action="../../controllers/ChatController.php" method="POST">
                    <input type="hidden" name="action" value="enviar_mensagem">
                    <input type="hidden" name="id_conversa" value="<?= $id_conversa_ativa ?>">
                    <input type="hidden" name="id_destino" value="<?= $id_destino ?>">
                    <div class="input-group">
                        <input type="text" name="mensagem" class="form-control" placeholder="Digite sua mensagem..." required>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function carregarMensagens() {
    const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
    if (!idConversa) return;

    fetch("get_mensagens.php?id_conversa=" + idConversa + "&t=" + new Date().getTime())
        .then(res => res.text())
        .then(html => {
            document.getElementById("mensagens").innerHTML = html;
        });
}

function atualizarNotificacoes() {
    fetch("get_notificacoes.php?t=" + new Date().getTime())
        .then(res => res.json())
        .then(data => {
            Object.entries(data).forEach(([userId, dados]) => {
                // Atualiza badge
                const badge = document.getElementById("badge-" + userId);
                if (badge) {
                    if (dados.total_nao_lidas > 0) {
                        badge.textContent = dados.total_nao_lidas;
                        badge.classList.remove("d-none");
                    } else {
                        badge.textContent = '';
                        badge.classList.add("d-none");
                    }
                }

                // Atualiza última mensagem
                const msgEl = document.getElementById("ultima-msg-" + userId);
                if (msgEl) {
                    msgEl.textContent = dados.mensagem || "Sem conversa ainda";
                }
            });
        });
}
function carregarMensagens() {
    const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
    if (!idConversa) return;

    fetch("get_mensagens.php?id_conversa=" + idConversa + "&t=" + new Date().getTime())
        .then(res => res.text())
        .then(html => {
            const box = document.getElementById("mensagens");
            box.innerHTML = html;
            box.scrollTop = box.scrollHeight; // 👈 força o scroll para o final
        });
}   

// Executa a cada 3 segundos
setInterval(() => {
    carregarMensagens();
    atualizarNotificacoes();
}, 100);
</script>
</body>
</html>
