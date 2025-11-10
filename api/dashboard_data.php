<?php
// =========================================================================
// ETAPA 1: API (BACKEND)
// Versão atualizada para corresponder ao esquema SQL fornecido
// =========================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção da API
if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['erro' => 'Usuario nao autenticado']);
    exit;
}

// Inclui a configuração e estabelece a conexão
// CORREÇÃO: O caminho estava com um '../' a mais.
require_once __DIR__ . '/../config/config.php';
$mysqli = $connection;

// --- Início da Lógica de Negócios (Exatamente como estava) ---
$nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';
$idUsuarioLogado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$idImobiliaria = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);

// Buscar o nome da imobiliária
$nomeImobiliaria = '';
if ($permissao !== 'SuperAdmin' && $idImobiliaria > 0) {
    $stmt = $mysqli->prepare("SELECT nome FROM imobiliaria WHERE id_imobiliaria = ?");
    if ($stmt) {
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $resultadoImobiliaria = $stmt->get_result()->fetch_assoc();
        $nomeImobiliaria = $resultadoImobiliaria['nome'] ?? '';
        $stmt->close();
    }
}

// --- Lógica para os CARDS (Atualizada com novas tabelas) ---
$dadosCards = [];

// Queries comuns
$queryTotalImoveis = "SELECT COUNT(*) AS total FROM imovel WHERE id_imobiliaria = ?";
$queryTotalLeads = "SELECT COUNT(*) AS total FROM leads WHERE id_imobiliaria = ? AND is_deleted = 0";
$queryTotalUsuarios = "SELECT COUNT(*) AS total FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0";
$queryTotalClientes = "SELECT COUNT(*) AS total FROM cliente WHERE id_imobiliaria = ? AND is_deleted = 0"; // Assumindo is_deleted=0

if ($permissao === 'SuperAdmin') {
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM imobiliaria WHERE is_deleted = 0");
    $dadosCards['total_imobiliarias'] = $result->fetch_assoc()['total'] ?? 0;
    
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM usuario WHERE is_deleted = 0");
    $dadosCards['total_usuarios_sistema'] = $result->fetch_assoc()['total'] ?? 0;
    
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM imovel");
    $dadosCards['total_imoveis'] = $result->fetch_assoc()['total'] ?? 0;

    $result = $mysqli->query("SELECT COUNT(*) AS total FROM leads WHERE is_deleted = 0");
    $dadosCards['total_leads_sistema'] = $result->fetch_assoc()['total'] ?? 0;

} else {
    // Admin / Coordenador / Corretor (todos com id_imobiliaria)
    
    // Total de Imóveis da Imobiliária
    $stmt = $mysqli->prepare($queryTotalImoveis);
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $dadosCards['total_imoveis'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Total de Leads da Imobiliária (Admin/Coord)
    if ($permissao === 'Admin' || $permissao === 'Coordenador') {
        $stmt = $mysqli->prepare($queryTotalLeads);
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_leads'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
    
    // Total de Usuários da Imobiliária (Admin)
    if ($permissao === 'Admin') {
        $stmt = $mysqli->prepare($queryTotalUsuarios);
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_usuarios'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }

    // Total de Clientes (Admin/Coord)
    if ($permissao === 'Admin' || $permissao === 'Coordenador') {
         $stmt = $mysqli->prepare($queryTotalClientes);
         $stmt->bind_param("i", $idImobiliaria);
         $stmt->execute();
         $dadosCards['total_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
         $stmt->close();
    }
    
    // Meus Leads (Corretor)
    if ($permissao === 'Corretor') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM leads WHERE id_usuario_responsavel = ? AND is_deleted = 0");
        $stmt->bind_param("i", $idUsuarioLogado);
        $stmt->execute();
        $dadosCards['meus_leads'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
    
    // Meus Clientes (Corretor)
    if ($permissao === 'Corretor') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_usuario = ? AND is_deleted = 0");
        $stmt->bind_param("i", $idUsuarioLogado);
        $stmt->execute();
        $dadosCards['meus_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
}


// --- Lógica para os GRÁFICOS (Corrigida com o esquema SQL) ---

function formatarDadosGrafico($mysqli_result) {
    $labels = [];
    $data = [];
    if ($mysqli_result) {
        while ($row = $mysqli_result->fetch_assoc()) {
            $labels[] = $row['status_label'];
            $data[] = (int)$row['total'];
        }
    }
    return ['labels' => $labels, 'data' => $data];
}

// CORREÇÃO: Usando 'status_pipeline' da tabela 'leads'
$statusFunil = [
    'Novo' => 'Novos Leads',
    'Contato' => 'Em Contato',
    'Negociação' => 'Em Negociação',
    'Fechado' => 'Ganhos',
    'Perdido' => 'Perdidos'
];
$statusFunilSQL = implode("', '", array_keys($statusFunil));

// CORREÇÃO: Usando 'status_pipeline'
$queryFunilBase = "SELECT 
                        CASE status_pipeline ";
foreach ($statusFunil as $statusKey => $statusLabel) {
    $queryFunilBase .= "WHEN '$statusKey' THEN '$statusLabel' ";
}
$queryFunilBase .= "END as status_label, 
                        COUNT(*) as total 
                   FROM leads 
                   WHERE status_pipeline IN ('$statusFunilSQL')";

$dadosGraficoFunil = ['labels' => [], 'data' => []];
$dadosGraficoStatus = ['labels' => [], 'data' => []]; 

if ($permissao === 'Corretor') {
    // CORREÇÃO: Usando 'id_usuario_responsavel'
    $stmt = $mysqli->prepare("$queryFunilBase AND id_usuario_responsavel = ? AND is_deleted = 0 GROUP BY status_label ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
    $stmt->bind_param("i", $idUsuarioLogado);
} elseif ($permissao === 'Admin' || $permissao === 'Coordenador') {
    $stmt = $mysqli->prepare("$queryFunilBase AND id_imobiliaria = ? AND is_deleted = 0 GROUP BY status_label ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
    $stmt->bind_param("i", $idImobiliaria);
} elseif ($permissao === 'SuperAdmin') {
    $stmt = $mysqli->prepare("$queryFunilBase AND is_deleted = 0 GROUP BY status_label ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
}

if (isset($stmt)) {
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dadosFormatados = formatarDadosGrafico($resultado);
    $dadosGraficoFunil = $dadosFormatados;
    $dadosGraficoStatus = $dadosFormatados; // Usando os mesmos dados, você pode mudar a query de status
    $stmt->close();
}

// --- Lógica para TAREFAS (Correta) ---
$stmt = $mysqli->prepare("SELECT id_tarefa, descricao, prazo, status FROM tarefas WHERE id_usuario = ? AND status != 'concluida' ORDER BY prazo ASC");
$stmt->bind_param("i", $idUsuarioLogado);
$stmt->execute();
$tarefasRecentes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$hoje = date('Y-m-d');

// =========================================================================
// ETAPA 2: Resposta da API
// =========================================================================

header('Content-Type: application/json');
echo json_encode([
    'nomeUsuario' => $nomeUsuario,
    'nomeImobiliaria' => $nomeImobiliaria,
    'permissao' => $permissao,
    'dadosCards' => $dadosCards, // Atualizado com mais dados
    'tarefasRecentes' => $tarefasRecentes,
    'dadosGraficoFunil' => $dadosGraficoFunil, // Atualizado com dados reais
    'dadosGraficoStatus' => $dadosGraficoStatus, // Atualizado com dados reais
    'hoje' => $hoje
]);

exit;
?>