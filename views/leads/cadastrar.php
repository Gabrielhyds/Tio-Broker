<?php
// Importa o arquivo de configuração do sistema (conexão com o banco, etc)
require_once '../../config/config.php';

// Importa o arquivo do modelo de Usuário (classe que lida com usuários no banco)
require_once '../../models/Usuario.php';

// Se precisar de outros modelos (ex: Cliente), é só descomentar ou adicionar aqui
// require_once '../../models/Cliente.php';

// Inicia a sessão (para poder acessar dados do usuário logado)
session_start();

// Verifica se o usuário está logado
// Se não estiver, manda ele de volta pra página de login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php'); // Redireciona pro login
    exit; // Para o código aqui mesmo
}

// Pega o ID da imobiliária do usuário logado
// Se não tiver nada, usa "1" como padrão
$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 1;

// Pega o ID do usuário logado (ou "1" se não existir)
$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? 1;

// Pega o nome do usuário logado (ou "Usuário" se não existir)
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Usuário';

// Cria um novo objeto da classe Usuario, passando a conexão com o banco
$usuarioModel = new Usuario($connection);

// Pede pro modelo listar todos os usuários da mesma imobiliária do usuário logado
$usuarios = $usuarioModel->listarTodos($id_imobiliaria_logada);

// Define qual menu da barra lateral deve aparecer como "ativo"
$activeMenu = 'leads';

// Define o título que vai aparecer na parte de cima da página
$pageTitle = 'Cadastrar Novo Lead';

// Diz qual arquivo contém o conteúdo principal dessa página
$conteudo = 'cadastrar_content.php';

// Inclui o template base (estrutura padrão do site: cabeçalho, menu, rodapé, etc)
include '../layout/template_base.php';
?>