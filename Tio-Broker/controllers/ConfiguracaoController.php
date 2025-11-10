<?php


/*
|--------------------------------------------------------------------------
| ARQUIVO: controllers/ConfiguracaoController.php (Versão Final Corrigida)
|--------------------------------------------------------------------------
| Este controller agora usa o seu ConfiguracaoDAO para salvar os dados
| e verifica a chave de sessão correta ('id_usuario').
*/

@session_start();
header('Content-Type: application/json');

// Inclui os arquivos necessários
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/ConfiguracaoDAO.php'; // Usa o seu DAO

class ConfiguracaoController
{
    public function salvar()
    {
        // ATENÇÃO: Alterado para 'connection' para corresponder ao seu config.php
        global $connection;

        // **CORREÇÃO**: Verifica a chave 'id_usuario', conforme seu arquivo de teste e banco de dados.
        if (!isset($_SESSION['usuario']['id_usuario'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($dados)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos recebidos.']);
            exit;
        }

        try {
            $id_usuario = $_SESSION['usuario']['id_usuario'];

            // Instancia e usa o SEU DAO para salvar as configurações
            $configuracaoDAO = new ConfiguracaoDAO($connection);
            $sucesso = $configuracaoDAO->salvarConfiguracoes($id_usuario, $dados);

            if ($sucesso) {
                // Se salvou no banco, atualiza também a sessão PHP
                $_SESSION['usuario']['configuracoes'] = $dados;

                echo json_encode(['success' => true, 'message' => 'Configurações salvas com sucesso!']);
            } else {
                throw new Exception('Falha ao salvar as configurações no banco de dados.');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

// Roteamento simples para a ação 'salvar'
if (isset($_GET['action']) && $_GET['action'] == 'salvar') {
    $controller = new ConfiguracaoController();
    $controller->salvar();
}
