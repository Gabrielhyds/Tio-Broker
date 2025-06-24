<?php
// Inicia ou resume a sessão para acessar os dados do usuário e mensagens de feedback.
session_start();
// Inclui os arquivos de configuração e os modelos necessários.
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Imobiliaria.php';

// Obtém o ID do usuário a ser editado da URL, ou define como nulo se não for fornecido.
$id = $_GET['id'] ?? null;
// Verifica se há um parâmetro de sucesso na URL (usado após salvar).
$salvoComSucesso = $_GET['sucesso'] ?? null;

// Se nenhum ID de usuário foi especificado na URL, define um erro e redireciona.
if (!$id) {
  $_SESSION['mensagem_erro'] = "Usuário não especificado.";
  header("Location: listar.php");
  exit;
}

// Cria uma instância do modelo de Usuário e de Imobiliária.
$usuarioModel = new Usuario($connection);
$imobiliariaModel = new Imobiliaria($connection);

// Busca os dados do usuário que será editado pelo ID.
$usuario = $usuarioModel->buscarPorId($id);
// Se o usuário não for encontrado no banco de dados, define um erro e redireciona.
if (!$usuario) {
  $_SESSION['mensagem_erro'] = "Usuário não encontrado.";
  header("Location: listar.php");
  exit;
}

// Armazena os dados do usuário encontrado em uma variável para ser usada no formulário.
$dados = $usuario;

// Obtém os dados do usuário que está logado para controle de acesso.
$usuarioLogado = $_SESSION['usuario'];
// Obtém a permissão e o ID da imobiliária do usuário logado.
$permissao = $usuarioLogado['permissao'];
$id_imobiliaria_usuario = $usuarioLogado['id_imobiliaria'];

// Lógica de permissão para carregar a lista de imobiliárias.
if ($permissao === 'SuperAdmin') {
  // Se for SuperAdmin, carrega todas as imobiliárias existentes.
  $listaImobiliarias = $imobiliariaModel->listarTodas();
} else {
  // Se não for SuperAdmin, busca apenas a imobiliária do próprio usuário.
  $imob = $imobiliariaModel->buscarPorId($id_imobiliaria_usuario);
  // Cria uma lista contendo apenas a imobiliária do usuário (se encontrada).
  $listaImobiliarias = $imob ? [$imob] : [];
}

// Define o item do menu de navegação que deve ser marcado como ativo.
$activeMenu = 'usuario_listar';
// Define o arquivo de conteúdo que contém o formulário de edição.
$conteudo = 'editar_conteudo.php';
// Inclui o template base da página, que montará o layout com o conteúdo de edição.
include '../layout/template_base.php';
