<?php
// Define o caminho base do projeto
$basePath = dirname(__DIR__); // Sai de /controllers para a raiz

require_once $basePath . '/config/config.php';
require_once $basePath . '/models/Lead.php';
require_once $basePath . '/models/Usuario.php'; // Para RF06

// Instancia a conexão e os models
$leadModel = new Lead($connection);
$usuarioModel = new Usuario($connection); // Necessário para listar usuários (RF06)

// Define o ID da imobiliária (logada) - Simplificado
// Em um app real, isso viria da sessão do usuário logado
$id_imobiliaria_logada = 1; 
$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? 1; // Pega da sessão

// Define o tipo de resposta padrão (para requisições AJAX)
// A *resposta* ainda será JSON, mesmo que a *requisição* seja form-urlencoded
header('Content-Type: application/json');


// Determina a ação (AJAX ou formulário)
// Agora lemos de $_POST ou $_GET, como no seu exemplo
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Roteamento de Ações
switch ($action) {

    /**
     * RF01: Cadastrar Lead (Já usa $_POST, está correto)
     */
    case 'cadastrar':
        try {
            // Validação (RF01 Erro)
            if (empty($_POST['nome']) || empty($_POST['telefone']) || empty($_POST['origem'])) {
                throw new Exception("Campos obrigatórios (Nome, Contato, Origem) devem ser preenchidos.");
            }

            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => (int)($_POST['id_usuario_responsavel'] ?? $id_usuario_logado),
                'id_imobiliaria' => $id_imobiliaria_logada
            ];

            $novoId = $leadModel->cadastrar($dados);
            if ($novoId) {
                // (RF10) - Aqui você dispararia uma notificação
                $_SESSION['sucesso'] = "Lead cadastrado com sucesso! (ID: $novoId)";
            } else {
                throw new Exception("Falha ao salvar o lead no banco de dados.");
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        // Redireciona de volta para o pipeline
        header("Location: ../views/leads/pipeline.php");
        exit;

    /**
     * RF02: Editar Lead (Já usa $_POST, está correto)
     */
    case 'editar':
        try {
            $id_lead = (int)($_POST['id_lead'] ?? 0);
            if ($id_lead === 0) {
                 throw new Exception("ID do lead inválido.");
            }

            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => (int)($_POST['id_usuario_responsavel'])
            ];
            
            if ($leadModel->editar($id_lead, $dados)) {
                // (RF10) - Disparar notificação de atualização
                $_SESSION['sucesso'] = "Lead atualizado com sucesso!";
            } else {
                 throw new Exception("Falha ao atualizar o lead.");
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        header("Location: ../views/leads/pipeline.php");
        exit;

    /**
     * RF03: Excluir Lead (via AJAX/Fetch)
     */
    case 'excluir':
        // Alterado de $input para $_POST
        $id_lead = (int)($_POST['id_lead'] ?? 0); 
        
        if ($id_lead > 0) {
            // (RF03 Erro) - O Model deve conter a lógica para bloquear se houver negociações
            if ($leadModel->excluir($id_lead)) {
                echo json_encode(['success' => true, 'message' => 'Lead marcado como inativo.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha ao excluir o lead. Pode estar vinculado a negociações.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        }
        exit;

    /**
     * RF05: Movimentar Lead no Pipeline (via AJAX/Fetch)
     */
    case 'mover_pipeline':
        // Alterado de $input para $_POST
        $id_lead = (int)($_POST['id_lead'] ?? 0);
        $novo_status = $_POST['novo_status'] ?? '';

        $status_permitidos = ['Novo', 'Contato', 'Negociação', 'Fechado', 'Perdido'];

        if ($id_lead > 0 && in_array($novo_status, $status_permitidos)) {
            if ($leadModel->moverPipeline($id_lead, $novo_status)) {
                 // (RF10) - Disparar notificação
                echo json_encode(['success' => true, 'message' => 'Status atualizado.']);
            } else {
                // (RF05 Erro)
                echo json_encode(['success' => false, 'message' => 'Falha de conexão ao atualizar status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos para movimentação.']);
        }
        exit;

    /**
     * RF08: Registrar Interação (via AJAX/Fetch)
     */
    case 'registrar_interacao':
        // Alterado de $input para $_POST
        // (RF08 Erro)
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

    /**
     * RF02/RF08: Buscar Dados de um Lead (via AJAX/Fetch)
     */
    case 'buscar_lead':
        // Este já usava $_GET, está correto
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

    /**
     * Ação padrão: redirecionar para o pipeline
     */
    default:
        // Se a ação for desconhecida e for um AJAX, retorna erro JSON
        // Verificação padrão para AJAX (sem $input)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida ou inválida.', 'action_received' => $action]);
            exit;
        }
        
        // Se for requisição de formulário normal, redireciona
        header("Location: ../views/leads/pipeline.php");
        exit;
}
?>

