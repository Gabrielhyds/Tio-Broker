<?php
// =========================================================================
// ETAPA 1: API (BACKEND)
// Versão atualizada com melhorias de depuração e correções
// =========================================================================

// --- Configuração de Erros ---
// Adicionado para depuração. Comente ou remova em produção.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Resposta de erro padronizada
function enviarErro($mensagem, $codigo = 500) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo json_encode(['erro' => $mensagem]);
    exit;
}

// Proteção da API
if (!isset($_SESSION['usuario'])) {
    enviarErro('Usuario nao autenticado', 401);
}

// --- Conexão com o Banco ---
$configFile = __DIR__ . '/../config/config.php';
if (!file_exists($configFile)) {
    enviarErro('Arquivo de configuracao nao encontrado em: ' . $configFile);
}

require_once $configFile;

// Verificação de Conexão
if (!isset($connection) || $connection->connect_error) {
    enviarErro('Falha na conexao com o banco de dados: ' . ($connection->connect_error ?? 'Variavel de conexao nao definida.'));
}
$mysqli = $connection;


// --- Início da Lógica de Negócios ---
$nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';
$idUsuarioLogado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$idImobiliaria = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);
$permissao = $_SESSION['usuario']['permissao'] ?? null;

// Se a permissão for nula, é um problema.
if ($permissao === null) {
    enviarErro('Permissao do usuario nao definida na sessao.');
}

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
$queryTotalClientes = "SELECT COUNT(*) AS total FROM cliente WHERE id_imobiliaria = ? AND is_deleted = 0";

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

// =========================================================================
// CORREÇÃO SQL_MODE
// Adicionamos 'status_pipeline' ao SELECT para que possa ser usado no GROUP BY
// =========================================================================
$queryFunilBase = "SELECT 
                        CASE status_pipeline ";
foreach ($statusFunil as $statusKey => $statusLabel) {
    $queryFunilBase .= "WHEN '$statusKey' THEN '$statusLabel' ";
}
$queryFunilBase .= "END as status_label, 
                        status_pipeline, -- <--- ADICIONADO AQUI
                        COUNT(*) as total 
                   FROM leads 
                   WHERE status_pipeline IN ('$statusFunilSQL')";

$dadosGraficoFunil = ['labels' => [], 'data' => []];
$dadosGraficoStatus = ['labels' => [], 'data' => []]; 

// =========================================================================
// INÍCIO DA CORREÇÃO DO BUG
// A lógica de execute() e close() foi movida para DENTRO de cada bloco.
// =========================================================================

if ($permissao === 'Corretor') {
    // CORREÇÃO: Usando 'id_usuario_responsavel'
    // CORREÇÃO SQL_MODE: Adicionado 'status_pipeline' ao GROUP BY
    $stmt = $mysqli->prepare("$queryFunilBase AND id_usuario_responsavel = ? AND is_deleted = 0 GROUP BY status_label, status_pipeline ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
    $stmt->bind_param("i", $idUsuarioLogado);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dadosFormatados = formatarDadosGrafico($resultado);
    $stmt->close();
} elseif ($permissao === 'Admin' || $permissao === 'Coordenador') {
    // CORREÇÃO SQL_MODE: Adicionado 'status_pipeline' ao GROUP BY
    $stmt = $mysqli->prepare("$queryFunilBase AND id_imobiliaria = ? AND is_deleted = 0 GROUP BY status_label, status_pipeline ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dadosFormatados = formatarDadosGrafico($resultado);
    $stmt->close();
} elseif ($permissao === 'SuperAdmin') {
    // CORREÇÃO SQL_MODE: Adicionado 'status_pipeline' ao GROUP BY
    $stmt = $mysqli->prepare("$queryFunilBase AND is_deleted = 0 GROUP BY status_label, status_pipeline ORDER BY FIELD(status_pipeline, '$statusFunilSQL')");
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dadosFormatados = formatarDadosGrafico($resultado);
    $stmt->close();
}

// Agora, só preenchemos os dados se $dadosFormatados foi definido
if (isset($dadosFormatados)) {
    $dadosGraficoFunil = $dadosFormatados;
    // Você pode ter uma query diferente para o gráfico de status, se necessário.
    // Por enquanto, estou usando os mesmos dados do funil.
    $dadosGraficoStatus = $dadosFormatados; 
}
// =========================================================================
// FIM DA CORREÇÃO
// =========================================================================


// --- Lógica para TAREFAS (Correta) ---
// Esta consulta agora é segura, pois $stmt não está mais "contaminado"
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