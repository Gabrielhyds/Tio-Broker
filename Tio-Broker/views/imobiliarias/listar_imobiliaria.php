<?php
// Inicia ou resume a sessão PHP.
session_start();
// Verifica se o usuário não está logado OU se a permissão não é 'SuperAdmin'.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
  // Se qualquer uma das condições for verdadeira, redireciona para a página de login.
  header('Location: /auth/login.php');
  // Encerra a execução do script.
  exit;
}

// Define qual item do menu de navegação deve ser marcado como "ativo".
$activeMenu = 'imobiliaria_listar';

// Inclui os arquivos de configuração e o modelo de dados da Imobiliária.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

// Cria uma nova instância do modelo Imobiliaria, passando a conexão com o banco de dados.
$imobiliariaModel = new Imobiliaria($connection);

// Lógica de paginação e filtro.
// Define o número de itens a serem exibidos por página.
$itens_por_pagina = 10;
// Obtém o número da página atual da URL, ou assume a página 1 como padrão.
$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
// Obtém o termo de filtro da URL, se houver.
$filtro = $_GET['filtro'] ?? '';

// Conta o número total de imobiliárias, aplicando o filtro de busca se houver.
$total_itens = $imobiliariaModel->contarTotal($filtro);
// Calcula o número total de páginas necessárias para exibir todos os itens.
$total_paginas = ceil($total_itens / $itens_por_pagina);
// Busca a lista de imobiliárias para a página atual, aplicando o filtro.
$lista = $imobiliariaModel->listarPaginado($pagina_atual, $itens_por_pagina, $filtro);

// Redefine a variável de menu ativo (embora já definida, é uma boa prática garantir).
$activeMenu = 'imobiliaria_listar';
// Define o nome do arquivo que contém o HTML da lista de imobiliárias.
$conteudo = __DIR__ . '/listar_imobiliaria_content.php';
// Inclui o template base da página, que usará a variável $conteudo para carregar o HTML correto.
include '../layout/template_base.php';
