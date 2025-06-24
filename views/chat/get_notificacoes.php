<?php
// Inclui os arquivos de configuração e os modelos de Chat e Usuário.
require_once '../../config/config.php';
require_once '../../models/Chat.php';
require_once '../../models/Usuario.php';

// Inicia ou resume a sessão para acessar os dados do usuário logado.
session_start();
// Se não houver um usuário na sessão, encerra o script para proteger o endpoint.
if (!isset($_SESSION['usuario'])) exit;

// Cria uma nova instância do modelo Chat, passando a conexão com o banco.
$chat = new Chat($connection);
// Cria uma nova instância do modelo Usuario.
$usuarioModel = new Usuario($connection);
// Obtém o ID do usuário logado a partir da sessão.
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];

// Busca o número de mensagens não lidas, agrupadas por quem enviou.
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);
// Busca a última mensagem trocada com cada usuário.
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);

// Inicializa um array para montar a resposta final.
$retorno = [];
// Itera sobre as últimas mensagens para construir a estrutura de dados.
foreach ($ultimasMensagens as $id_user => $msg) {
    // Para cada usuário com quem houve conversa, cria uma entrada no array de retorno.
    $retorno[$id_user] = [
        // Adiciona o conteúdo da última mensagem.
        'mensagem' => $msg['mensagem'],
        // Adiciona a contagem de mensagens não lidas daquele usuário (ou 0 se não houver).
        'total_nao_lidas' => $notificacoes[$id_user] ?? 0
    ];
}

// Converte o array de retorno para o formato JSON e o envia como resposta.
echo json_encode($retorno);
