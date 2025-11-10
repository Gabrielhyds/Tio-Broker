<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: relatorio_leads.php (CORRIGIDO)
|--------------------------------------------------------------------------
| 1. Carrega dependências e sessão.
| 2. Define variáveis de ambiente (usuário logado, permissão).
| 3. Instancia Models.
| 4. Executa o Controller para buscar os dados do relatório ($dados).
| 5. Executa a lógica para buscar os dados dos filtros ($lista_imobiliarias, $lista_usuarios).
| 6. Define a View e carrega o template.
*/

// Ajuste os caminhos conforme sua estrutura
require_once '../../config/config.php';
require_once '../../controllers/RelatorioController.php';
require_once '../../models/Imobiliaria.php'; // Model da Imobiliaria
require_once __DIR__ . '/../../models/Usuario.php'; // Model do Usuario

// Inicia sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// --- 1. Proteção e Variáveis de Ambiente ---

// Garante que o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php'); // Ajuste o caminho
    exit;
}

// Pega a conexão do config.php (assumindo que o nome é $connection)
global $connection; 

// Carrega dados da sessão
$idUsuarioLogado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$idImobiliariaLogada = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);
$permissao = $_SESSION['usuario']['permissao'] ?? '';


// --- 2. Instancia Models e Controller ---
$imobiliariaModel = new Imobiliaria($connection);
$usuarioModel = new Usuario($connection);
$controller = new RelatorioController();

// --- 3. Busca Dados do Relatório (Controller) ---
// O controller processa o POST e retorna os dados do relatório
$dados = $controller->leads(); 

// --- 4. Busca Dados para os Filtros (Models) ---

// Lógica para o filtro de Imobiliária
if ($permissao === 'SuperAdmin') {
    // SuperAdmin vê todas as imobiliárias
    // AJUSTE: Corrigido de listarTodos() para listarTodas(), conforme seu models/Imobiliaria.php
    $lista_imobiliarias = $imobiliariaModel->listarTodas(); 
} else {
    // Outros usuários veem apenas a sua
    // CORRETO: O método buscarPorId($id) existe no seu models/Imobiliaria.php
    $imob = $imobiliariaModel->buscarPorId($idImobiliariaLogada);
    $lista_imobiliarias = $imob ? [$imob] : [];
}

// Lógica para o filtro de Usuário (Responsável)
if ($permissao === 'SuperAdmin') {
     // SuperAdmin vê todos os usuários
     // CORRETO: O método listarTodos() existe no seu models/Usuario.php
    $lista_usuarios = $usuarioModel->listarTodos(); 
} else {
    // Outros usuários veem apenas os usuários da sua imobiliária
    // CORRETO: O método listarPorImobiliaria($id) existe no seu models/Usuario.php
    $lista_usuarios = $usuarioModel->listarPorImobiliaria($idImobiliariaLogada);
}


// --- 5. Define a View e Carrega o Template ---

// Define o arquivo de view (o conteúdo)
$conteudo = 'relatorio_leads_content.php'; 
// (Seu template_base.php irá procurar por este arquivo)

// Inclui o template base da página
// O template base terá acesso a $dados, $lista_imobiliarias e $lista_usuarios
include '../layout/template_base.php';
?>

