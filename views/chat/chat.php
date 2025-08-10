<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/chat.php (RECONSTRUÍDO)
|--------------------------------------------------------------------------
| - Mantém a lógica de sessão, permissões e busca de usuários.
| - Garante que o objeto completo do usuário de destino ($usuarioDestino)
|   seja buscado e passado para a view.
*/

require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Chat.php';
session_start();

use App\Models\Chat;

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$chat = new Chat($connection);
$usuarioModel = new Usuario($connection);

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$permissao = $_SESSION['usuario']['permissao'];
$id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;

$id_imobiliaria_filtro = $_GET['id_imobiliaria'] ?? null;
$listaImobiliarias = [];
$usuariosDisponiveis = [];

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

$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);

usort($usuariosDisponiveis, function ($a, $b) use ($ultimasMensagens) {
    $timestampA = isset($ultimasMensagens[$a['id_usuario']]) ? strtotime($ultimasMensagens[$a['id_usuario']]['data_envio']) : 0;
    $timestampB = isset($ultimasMensagens[$b['id_usuario']]) ? strtotime($ultimasMensagens[$b['id_usuario']]['data_envio']) : 0;
    return $timestampB <=> $timestampA;
});

$id_destino = $_GET['id_destino'] ?? null;
$id_conversa_ativa = null;
$usuarioDestino = null; 

if ($id_destino) {
    $usuarioDestino = $usuarioModel->buscarPorId($id_destino); 
    if ($usuarioDestino) {
        $id_conversa_ativa = $chat->buscarConversaPrivadaEntre($id_usuario_logado, $id_destino);
        if (!$id_conversa_ativa) {
            $id_conversa_ativa = $chat->criarConversaPrivada($id_usuario_logado, $id_destino);
        }
        $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);
    }
}

$activeMenu = 'chat';
$templatePath = '../layout/template_base.php';
$conteudo = 'chat_content.php';
include $templatePath;
