<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: salvar_tema.php (NOVO ARQUIVO)
|--------------------------------------------------------------------------
| API para salvar a preferência de tema (dark/light) do usuário
| no banco de dados.
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 1. Proteção da API
if (!isset($_SESSION['usuario']['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado']);
    exit;
}

// 2. Incluir configuração do banco
require_once __DIR__ . '/../config/config.php';
$mysqli = $connection;

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha na conexão com o banco']);
    exit;
}

// 3. Obter dados do POST (JSON)
$dados = json_decode(file_get_contents('php://input'), true);
$novoTema = $dados['tema'] ?? null;
$idUsuario = (int)$_SESSION['usuario']['id_usuario'];

// 4. Validar o tema
if ($novoTema !== 'light' && $novoTema !== 'dark') {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'Tema inválido']);
    exit;
}

// 5. Atualizar o banco de dados
$stmt = $mysqli->prepare("UPDATE usuario SET tema = ? WHERE id_usuario = ?");
if ($stmt) {
    $stmt->bind_param("si", $novoTema, $idUsuario);
    
    if ($stmt->execute()) {
        
        // =================================================================
        // CORREÇÃO DO BUG DE SESSÃO (AQUI!)
        // =================================================================
        // Atualiza a sessão no caminho correto que o AuthController usa
        $_SESSION['usuario']['configuracoes']['appearance']['theme'] = $novoTema;

        // (OPCIONAL, mas bom para consistência) Atualiza a coluna 'tema' que também existe
        $_SESSION['usuario']['tema'] = $novoTema; 

        echo json_encode(['sucesso' => true, 'tema' => $novoTema]);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'erro' => 'Falha ao atualizar o tema no banco']);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha ao preparar a consulta']);
}

$mysqli->close();
?>