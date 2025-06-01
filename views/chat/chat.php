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
$permissao = $_SESSION['usuario']['permissao'];

// Lista de imobiliárias para o select (somente para SuperAdmin)
$listaImobiliarias = ($permissao === 'SuperAdmin') ? $usuarioModel->listarImobiliariasComUsuarios() : [];
$id_imobiliaria_filtro = $_GET['id_imobiliaria'] ?? null;

// Filtra usuários por imobiliária (caso SuperAdmin tenha selecionado uma)
if ($permissao === 'SuperAdmin' && $id_imobiliaria_filtro) {
    $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_filtro, $id_usuario_logado);
} else {
    $usuariosDisponiveis = $usuarioModel->listarTodosComImobiliaria($id_usuario_logado);
}

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
    $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);
    $mensagens = $chat->listarMensagensDaConversa($id_conversa_ativa);
}
?>

<!-- A PARTIR DAQUI É O HTML ORIGINAL COM ALTERAÇÕES MÍNIMAS -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat Interno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php
        //incluir o dashboard de acordo com o perfil do usuário
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
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <select name="id_imobiliaria" class="form-select" onchange="this.form.submit()">
                            <option value="#">-- Filtrar por Imobiliária --</option>
                            <?php foreach ($listaImobiliarias as $imob): ?>
                                <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($imob['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            <?php endif; ?>

            <ul class="list-group">
                <?php foreach ($usuariosDisponiveis as $u): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="text-decoration-none flex-grow-1">
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
                    <p class="text-muted">Selecione uma Imobiliaria e um colaborador ao lado para iniciar uma conversa.</p>
                <?php endif; ?>
            </div>

            <?php if ($id_conversa_ativa): ?>
                <form action="../../controllers/ChatController.php" method="POST">
                    <input type="hidden" name="action" value="enviar_mensagem">
                    <input type="hidden" name="id_conversa" value="<?= $id_conversa_ativa ?>">
                    <input type="hidden" name="id_destino" value="<?= $id_destino ?>">
                    
                    <!-- Preserva o filtro de imobiliária, se estiver ativo -->
                    <?php if (isset($id_imobiliaria_filtro) || isset($_GET['id_imobiliaria'])): ?>
                        <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_filtro ?? $_GET['id_imobiliaria']) ?>">
                    <?php endif; ?>
                    
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
                const badge = document.getElementById("badge-" + userId);
                if (badge) {
                    badge.textContent = dados.total_nao_lidas || '';
                    badge.classList.toggle("d-none", dados.total_nao_lidas == 0);
                }

                const msgEl = document.getElementById("ultima-msg-" + userId);
                if (msgEl) {
                    msgEl.textContent = dados.mensagem || "Sem conversa ainda";
                }
            });
        });
}

function scrollParaFimDoChat() {
    const box = document.getElementById('mensagens');
    if (box) box.scrollTop = box.scrollHeight;
}

window.onload = scrollParaFimDoChat;
setInterval(() => {
    carregarMensagens();
    atualizarNotificacoes();
}, 3000);
</script>
</body>
</html>
