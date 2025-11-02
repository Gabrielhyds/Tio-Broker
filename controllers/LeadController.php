<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Define o caminho base do projeto
$basePath = dirname(__DIR__); // Sai de /controllers para a raiz

require_once $basePath . '/config/config.php';
require_once $basePath . '/models/Lead.php';
require_once $basePath . '/models/Usuario.php'; // Para RF06
require_once $basePath . '/models/Notificacao.php'; // Model de notificação

// Instancia a conexão e os models
$leadModel = new Lead($connection);
$usuarioModel = new Usuario($connection);
$notificacaoModel = new Notificacao($connection);

// Pega dados da sessão
$id_imobiliaria_logada = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);
$id_usuario_logado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Sistema';

// Determina a ação
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Roteamento de Ações
switch ($action) {

    case 'cadastrar':
        try {
            if (empty($_POST['nome']) || empty($_POST['telefone']) || empty($_POST['origem'])) {
                throw new Exception("Campos obrigatórios (Nome, Contato, Origem) devem ser preenchidos.");
            }

            $id_usuario_responsavel = (int)($_POST['id_usuario_responsavel'] ?? $id_usuario_logado);

            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => $id_usuario_responsavel,
                'id_imobiliaria' => $id_imobiliaria_logada
            ];

            $novoId = $leadModel->cadastrar($dados);
            if ($novoId) {
                $_SESSION['sucesso'] = "Lead cadastrado com sucesso! (ID: $novoId)";
                try {
                    if ($id_usuario_responsavel > 0 && $id_usuario_responsavel != $id_usuario_logado) {
                        $mensagem = "$nome_usuario_logado atribuiu um novo lead a você: " . $dados['nome'];
                        $notificacaoModel->criarNotificacao($id_usuario_responsavel, $mensagem);
                    }
                } catch (Exception $e) {
                    error_log("Falha ao criar notificação de cadastro: " . $e->getMessage());
                }
            } else {
                throw new Exception("Falha ao salvar o lead no banco de dados.");
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        header("Location: ../views/leads/pipeline.php");
        exit;

    case 'editar':
        try {
            $id_lead = (int)($_POST['id_lead'] ?? 0);
            if ($id_lead === 0) {
                throw new Exception("ID do lead inválido.");
            }

            $lead_antigo = $leadModel->buscarPorId($id_lead);
            $id_usuario_antigo = (int)($lead_antigo['id_usuario_responsavel'] ?? 0);
            $id_usuario_novo = (int)($_POST['id_usuario_responsavel'] ?? 0);

            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => $id_usuario_novo
            ];

            if ($leadModel->editar($id_lead, $dados)) {
                $_SESSION['sucesso'] = "Lead atualizado com sucesso!";
                if ($id_usuario_novo > 0 && $id_usuario_novo != $id_usuario_antigo) {
                    try {
                        $mensagem = "$nome_usuario_logado reatribuiu o lead " . $dados['nome'] . " para você.";
                        $notificacaoModel->criarNotificacao($id_usuario_novo, $mensagem);
                    } catch (Exception $e) {
                        error_log("Falha ao criar notificação de reatribuição: " . $e->getMessage());
                    }
                }
            } else {
                throw new Exception("Falha ao atualizar o lead.");
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        header("Location: ../views/leads/pipeline.php");
        exit;

    case 'excluir':
        header('Content-Type: application/json');
        $id_lead = (int)($_POST['id_lead'] ?? 0); 
        if ($id_lead > 0) {
            if ($leadModel->excluir($id_lead)) {
                echo json_encode(['success' => true, 'message' => 'Lead marcado como inativo.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha ao excluir o lead. Pode estar vinculado a negociações.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        }
        exit;

    case 'mover_pipeline':
        header('Content-Type: application/json');
        $id_lead = (int)($_POST['id_lead'] ?? 0);
        $novo_status = $_POST['novo_status'] ?? '';
        $status_permitidos = ['Novo', 'Contato', 'Negociação', 'Fechado', 'Perdido'];

        if ($id_lead > 0 && in_array($novo_status, $status_permitidos)) {
            if ($leadModel->moverPipeline($id_lead, $novo_status)) {
                try {
                    $lead = $leadModel->buscarPorId($id_lead);
                    $id_responsavel = (int)($lead['id_usuario_responsavel'] ?? 0);
                    if ($id_responsavel > 0 && $id_responsavel != $id_usuario_logado) {
                        $mensagem = "$nome_usuario_logado moveu o lead " . $lead['nome'] . " para '$novo_status'.";
                        $notificacaoModel->criarNotificacao($id_responsavel, $mensagem);
                    }
                } catch (Exception $e) {
                    error_log("Falha ao criar notificação de pipeline: " . $e->getMessage());
                }
                echo json_encode(['success' => true, 'message' => 'Status atualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha de conexão ao atualizar status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos para movimentação.']);
        }
        exit;

    case 'registrar_interacao':
        header('Content-Type: application/json');
        if (empty($_POST['id_lead']) || empty($_POST['descricao']) || empty($_POST['tipo_interacao'])) {
            echo json_encode(['success' => false, 'message' => 'Descrição e tipo são obrigatórios.']);
            exit;
        }

        $dados = [
            'id_lead' => (int)$_POST['id_lead'],
            'id_usuario' => $id_usuario_logado,
            'tipo_interacao' => $_POST['tipo_interacao'],
            'descricao' => $_POST['descricao']
        ];

        if ($leadModel->registrarInteracao($dados)) {
            echo json_encode(['success' => true, 'message' => 'Interação registrada.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao salvar interação.']);
        }
        exit;

    case 'buscar_lead':
        header('Content-Type: application/json');
        $id_lead = (int)($_GET['id_lead'] ?? 0);
        if ($id_lead > 0) {
            $leadData = $leadModel->buscarPorId($id_lead);
            if ($leadData) {
                echo json_encode(['success' => true, 'data' => $leadData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lead não encontrado ou inativo.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        }
        exit;

    default:
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida ou inválida.', 'action_received' => $action]);
            exit;
        }
        header("Location: ../views/leads/pipeline.php");
        exit;
}
?>