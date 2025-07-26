<?php
// Inicia ou resume a sessão para acessar os dados do usuário logado.
session_start();

// Verifica se o usuário não está logado OU se não tem a permissão de 'SuperAdmin'.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    // Se não tiver permissão, redireciona para a página de login.
    header('Location: ../auth/login.php');
    exit;
}

// Inclui os arquivos de configuração e os modelos de dados necessários.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';
require_once __DIR__ . '/../../models/Usuario.php';

// Cria instâncias dos modelos para interagir com o banco de dados.
$imobiliariaModel = new Imobiliaria($connection);
$usuarioModel     = new Usuario($connection);

// Obtém o ID da imobiliária da URL e o converte para um inteiro.
$idImobiliaria = intval($_GET['id'] ?? 0);
// Busca os dados da imobiliária específica.
$dadosImob     = $imobiliariaModel->buscarPorId($idImobiliaria);
// Se a imobiliária não for encontrada, redireciona para a página de listagem.
if (!$dadosImob) {
    header('Location: listar.php');
    exit;
}

// Busca a lista de usuários que já estão vinculados a esta imobiliária.
// Presume-se que o método `listarPorImobiliaria` retorne também o campo `is_deleted`.
$usuarios = $usuarioModel->listarPorImobiliaria($idImobiliaria);

// Prepara uma consulta para buscar todos os usuários NÃO-DELETADOS (ativos) que não estão vinculados a esta imobiliária.
// ATUALIZAÇÃO: A condição foi alterada de "status = 'ativo'" para "is_deleted = 0".
$stmt = $connection->prepare("
    SELECT id_usuario, nome
    FROM usuario
    WHERE (id_imobiliaria IS NULL OR id_imobiliaria <> ?) AND is_deleted = 0
    ORDER BY nome ASC
");
// Associa o ID da imobiliária atual à consulta.
$stmt->bind_param("i", $idImobiliaria);
// Executa a consulta.
$stmt->execute();
// Obtém o resultado.
$resultDisponiveis = $stmt->get_result();
// Busca todos os usuários disponíveis e os armazena em um array.
$usuariosDisponiveis = $resultDisponiveis->fetch_all(MYSQLI_ASSOC);
// Fecha o statement para liberar recursos.
$stmt->close();

// Verifica se há flags na URL indicando que um usuário foi removido ou incluído com sucesso.
$usuarioRemovido = isset($_GET['removido']) && $_GET['removido'] == 1;
$usuarioIncluidoEscolhido = isset($_GET['incluidoUsuario']) && $_GET['incluidoUsuario'] == 1;

// Define o item do menu de navegação que deve ser marcado como "ativo".
$activeMenu = 'imobiliaria_listar';
// Define o nome do arquivo que contém o formulário de edição da imobiliária.
$conteudo = 'editar_imobiliaria_content.php';
// Inclui o template base da página, que montará o layout com o conteúdo de edição.
include '../layout/template_base.php';
?>
