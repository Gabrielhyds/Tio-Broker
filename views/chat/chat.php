<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/chat.php (VERSÃO ATUALIZADA)
|--------------------------------------------------------------------------
| - Adicionada lógica para buscar o nome do usuário de destino.
| - A variável $nome_destino agora é passada para o template.
*/

// Inclui os arquivos de configuração, o modelo de Usuário e o modelo de Chat.
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Chat.php';
// Inicia ou resume a sessão PHP para acessar os dados do usuário logado.
session_start();

// Se não houver um usuário na sessão, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Cria uma nova instância do modelo Chat, passando a conexão com o banco de dados.
$chat = new Chat($connection);
// Cria uma nova instância do modelo Usuario.
$usuarioModel = new Usuario($connection);

// Obtém os dados do usuário logado a partir da sessão.
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$permissao = $_SESSION['usuario']['permissao'];
$id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;

// Obtém o ID da imobiliária para filtrar, se houver, a partir da URL (parâmetro GET).
$id_imobiliaria_filtro = $_GET['id_imobiliaria'] ?? null;
// Inicializa arrays para a lista de imobiliárias e usuários.
$listaImobiliarias = [];
$usuariosDisponiveis = [];

// Se o usuário for SuperAdmin, ele tem permissão para ver e filtrar por todas as imobiliárias.
if ($permissao === 'SuperAdmin') {
    // Busca a lista de todas as imobiliárias que têm usuários.
    $listaImobiliarias = $usuarioModel->listarImobiliariasComUsuarios();
    // Se um filtro de imobiliária foi aplicado.
    if ($id_imobiliaria_filtro) {
        // Lista apenas os usuários da imobiliária selecionada.
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_filtro, $id_usuario_logado);
    }
} else {
    // Se não for SuperAdmin, lista apenas os usuários da sua própria imobiliária.
    if ($id_imobiliaria_usuario) {
        $usuariosDisponiveis = $usuarioModel->listarPorImobiliaria($id_imobiliaria_usuario, $id_usuario_logado);
    }
}

// Busca a última mensagem trocada com cada usuário para exibir na lista de conversas.
$ultimasMensagens = $chat->buscarUltimaMensagemCom($id_usuario_logado);
// Conta o número de mensagens não lidas para exibir notificações (se aplicável).
$notificacoes = $chat->contarNaoLidasPorRemetente($id_usuario_logado);

// Ordena a lista de usuários para que as conversas mais recentes apareçam primeiro.
usort($usuariosDisponiveis, function ($a, $b) use ($ultimasMensagens) {
    // Obtém o timestamp da última mensagem para o usuário A (ou 0 se não houver).
    $timestampA = isset($ultimasMensagens[$a['id_usuario']]) ? strtotime($ultimasMensagens[$a['id_usuario']]['data_envio']) : 0;
    // Obtém o timestamp da última mensagem para o usuário B.
    $timestampB = isset($ultimasMensagens[$b['id_usuario']]) ? strtotime($ultimasMensagens[$b['id_usuario']]['data_envio']) : 0;
    // Compara os timestamps para ordenar em ordem decrescente (mais recente primeiro).
    return $timestampB <=> $timestampA;
});

// Obtém o ID do usuário de destino da URL para iniciar ou abrir uma conversa.
$id_destino = $_GET['id_destino'] ?? null;
$id_conversa_ativa = null;
$nome_destino = null; // **NOVO**: Inicializa a variável para o nome.

// Se um usuário de destino foi selecionado.
if ($id_destino) {
    // **NOVO**: Busca os dados do usuário de destino para obter o nome.
    // (Assumindo que seu modelo tem um método como 'buscarPorId')
    $usuarioDestino = $usuarioModel->buscarPorId($id_destino); 
    if ($usuarioDestino) {
        $nome_destino = $usuarioDestino['nome'];
    }

    // Busca se já existe uma conversa privada entre o usuário logado e o destino.
    $id_conversa_ativa = $chat->buscarConversaPrivadaEntre($id_usuario_logado, $id_destino);
    // Se não existir uma conversa.
    if (!$id_conversa_ativa) {
        // Cria uma nova conversa privada.
        $id_conversa_ativa = $chat->criarConversaPrivada($id_usuario_logado, $id_destino);
    }
    // Marca todas as mensagens da conversa como lidas para o usuário logado.
    $chat->marcarComoLidas($id_conversa_ativa, $id_usuario_logado);
}

// Define qual item do menu deve ficar ativo na barra lateral.
$activeMenu = 'chat';
// Define o caminho para o template base da página.
$templatePath = '../layout/template_base.php';
// Define o arquivo de conteúdo específico que será incluído no template.
$conteudo = 'chat_content.php';
// Inclui o template base, que montará a página final com as variáveis definidas.
include $templatePath;