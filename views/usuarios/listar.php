<?php
// Garante que a sessão seja iniciada se ainda não estiver ativa.
if (session_status() === PHP_SESSION_NONE) session_start();

// Verifica se o usuário não está logado. Se não estiver, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
  // O caminho para o login deve ser absoluto a partir da raiz do site para evitar erros.
  header('Location: /auth/login.php');
  exit;
}

// Define qual item do menu de navegação deve ser marcado como ativo.
$activeMenu = 'usuario_listar';

// Inclui os arquivos necessários de configuração e o modelo de dados do usuário.
require_once '../../config/config.php';
require_once '../../models/Usuario.php';

// Cria uma instância do modelo de usuário, passando a conexão com o banco de dados.
$usuarioModel = new Usuario($connection);

// --- LÓGICA DE CONTROLE DE ACESSO ---
// Obtém os dados do usuário logado a partir da sessão.
$usuarioLogado = $_SESSION['usuario'];
// Define a permissão do usuário (padrão 'Admin' se não estiver definida).
$permissao = $usuarioLogado['permissao'] ?? 'Admin';
// Define o ID da imobiliária do usuário (null se não estiver definido).
$id_imobiliaria = $usuarioLogado['id_imobiliaria'] ?? null;

// --- LÓGICA DE FILTRO E PAGINAÇÃO ---
// Define o número de itens a serem exibidos por página.
$itens_por_pagina = 10;
// Obtém o número da página atual da URL, ou assume a página 1 como padrão.
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
// Garante que o número da página não seja menor que 1.
if ($pagina_atual < 1) $pagina_atual = 1;

// Obtém o termo de filtro da URL, se houver.
$filtro = $_GET['filtro'] ?? '';

// Conta o número total de itens (usuários) aplicando os filtros e as restrições de permissão.
$total_itens = $usuarioModel->contarTotal($filtro, $permissao, $id_imobiliaria);
// Calcula o número total de páginas necessárias para exibir todos os itens.
$total_paginas = ceil($total_itens / $itens_por_pagina);

// Busca a lista de usuários para a página atual, aplicando os filtros e as restrições.
$lista = $usuarioModel->listarPaginadoComImobiliaria(
  $pagina_atual,
  $itens_por_pagina,
  $filtro,
  $permissao,
  $id_imobiliaria
);

// Define o nome do arquivo que contém o HTML da lista de usuários.
$conteudo = 'listar_content.php';
// Inclui o template base da página, que usará a variável $conteudo para carregar o HTML correto.
include_once '../layout/template_base.php';
