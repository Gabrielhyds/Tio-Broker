<?php
// Este arquivo é o "loader" da view, similar ao seu exemplo "cadastrar_tarefa.php" (o segundo).

// Define o caminho base do projeto
$basePath = dirname(dirname(__DIR__)); // Sai de /views/leads para a raiz

require_once $basePath . '/config/config.php';
require_once $basePath . '/models/Lead.php';
require_once $basePath . '/models/Usuario.php'; // Para listar usuários (RF06)

// Modelos
$leadModel = new Lead($connection);
$usuarioModel = new Usuario($connection);

// 1. (RF04, RF07) Buscar dados para o pipeline
// Em um app grande, isso seria paginado ou filtrado. Aqui, trazemos todos.
$todosOsLeads = $leadModel->listarTodos();
$usuarios = $usuarioModel->listarTodos(); // Para o <select> de responsáveis (RF06)

// 2. (RF04) Organizar leads por status
$leadsAgrupados = [
    'Novo' => [],
    'Contato' => [],
    'Negociação' => [],
    'Fechado' => [],
    'Perdido' => []
];

foreach ($todosOsLeads as $lead) {
    if (isset($leadsAgrupados[$lead['status_pipeline']])) {
        $leadsAgrupados[$lead['status_pipeline']][] = $lead;
    }
}

// 3. Define as variáveis para o template
$activeMenu = 'leads'; // Para destacar o menu no template
$conteudo = 'pipeline_content.php'; // O arquivo de conteúdo real

// 4. Inclui o template base (que conterá o header, footer, sidebar, etc.)
include $basePath . '/views/layout/template_base.php';
?>
