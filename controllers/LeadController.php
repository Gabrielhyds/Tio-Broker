<?php
// Inicia a sessão, caso ainda não tenha começado
if (session_status() === PHP_SESSION_NONE) session_start();

// Define o caminho base do projeto (sobe um nível na pasta)
// Exemplo: se estiver em /controllers, ele volta pra raiz do projeto
$basePath = dirname(__DIR__);

// Importa os arquivos necessários do projeto
require_once $basePath . '/config/config.php';     // Configurações do banco de dados
require_once $basePath . '/models/Lead.php';       // Model que cuida dos leads
require_once $basePath . '/models/Usuario.php';    // Model dos usuários (usado para buscar corretores, etc)
require_once $basePath . '/models/Notificacao.php';// Model que envia notificações

// Cria os objetos principais (cada um com acesso ao banco)
$leadModel = new Lead($connection);
$usuarioModel = new Usuario($connection);
$notificacaoModel = new Notificacao($connection);

// Pega informações do usuário logado
$id_imobiliaria_logada = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0); // ID da imobiliária do usuário
$id_usuario_logado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);         // ID do usuário logado
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Sistema';            // Nome do usuário ou "Sistema"

// Verifica qual ação o usuário está tentando fazer (pode vir via POST ou GET)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ---------- ROTEAMENTO DE AÇÕES ----------
// Aqui o sistema decide o que fazer de acordo com o valor de $action
switch ($action) {

    // ====================================
    // 1️⃣ Cadastrar novo Lead
    // ====================================
    case 'cadastrar':
        try {
            // Verifica se os campos obrigatórios foram preenchidos
            if (empty($_POST['nome']) || empty($_POST['telefone']) || empty($_POST['origem'])) {
                throw new Exception("Campos obrigatórios (Nome, Contato, Origem) devem ser preenchidos.");
            }

            // Pega o ID do corretor responsável (ou usa o próprio usuário logado)
            $id_usuario_responsavel = (int)($_POST['id_usuario_responsavel'] ?? $id_usuario_logado);

            // Monta o array de dados do novo lead
            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => $id_usuario_responsavel,
                'id_imobiliaria' => $id_imobiliaria_logada
            ];

            // Salva no banco
            $novoId = $leadModel->cadastrar($dados);

            if ($novoId) {
                // Mensagem de sucesso
                $_SESSION['sucesso'] = "Lead cadastrado com sucesso! (ID: $novoId)";

                // Envia notificação se o lead foi atribuído a outro corretor
                try {
                    if ($id_usuario_responsavel > 0 && $id_usuario_responsavel != $id_usuario_logado) {
                        $mensagem = "$nome_usuario_logado atribuiu um novo lead a você: " . $dados['nome'];
                        $notificacaoModel->criarNotificacao($id_usuario_responsavel, $mensagem);
                    }
                } catch (Exception $e) {
                    // Caso dê erro ao enviar notificação, apenas registra no log
                    error_log("Falha ao criar notificação de cadastro: " . $e->getMessage());
                }

            } else {
                throw new Exception("Falha ao salvar o lead no banco de dados.");
            }

        } catch (Exception $e) {
            // Guarda a mensagem de erro na sessão pra mostrar depois
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }

        // Redireciona de volta para a tela principal (pipeline)
        header("Location: ../views/leads/pipeline.php");
        exit;

    // ====================================
    // 2️⃣ Editar um Lead existente
    // ====================================
    case 'editar':
        try {
            // Pega o ID do lead enviado no formulário
            $id_lead = (int)($_POST['id_lead'] ?? 0);

            if ($id_lead === 0) {
                throw new Exception("ID do lead inválido.");
            }

            // Busca os dados antigos para comparar quem era o responsável antes
            $lead_antigo = $leadModel->buscarPorId($id_lead);
            $id_usuario_antigo = (int)($lead_antigo['id_usuario_responsavel'] ?? 0);
            $id_usuario_novo = (int)($_POST['id_usuario_responsavel'] ?? 0);

            // Monta o array com os novos dados
            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'] ?? '',
                'telefone' => $_POST['telefone'],
                'origem' => $_POST['origem'],
                'interesse' => $_POST['interesse'] ?? '',
                'id_usuario_responsavel' => $id_usuario_novo
            ];

            // Atualiza no banco
            if ($leadModel->editar($id_lead, $dados)) {
                $_SESSION['sucesso'] = "Lead atualizado com sucesso!";

                // Se o lead foi reatribuído a outro corretor, cria notificação
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

    // ====================================
    // 3️⃣ Excluir (inativar) um Lead
    // ====================================
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

    // ====================================
    // 4️⃣ Mover o Lead entre colunas do pipeline
    // ====================================
    case 'mover_pipeline':
        header('Content-Type: application/json');

        $id_lead = (int)($_POST['id_lead'] ?? 0);
        $novo_status = $_POST['novo_status'] ?? '';

        // Status que o sistema permite
        $status_permitidos = ['Novo', 'Contato', 'Negociação', 'Fechado', 'Perdido'];

        if ($id_lead > 0 && in_array($novo_status, $status_permitidos)) {
            if ($leadModel->moverPipeline($id_lead, $novo_status)) {
                try {
                    // Busca o lead para enviar notificação ao responsável
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

    // ====================================
    // 5️⃣ Registrar uma interação com o Lead (anotação, ligação, etc)
    // ====================================
    case 'registrar_interacao':
        header('Content-Type: application/json');

        // Campos obrigatórios
        if (empty($_POST['id_lead']) || empty($_POST['descricao']) || empty($_POST['tipo_interacao'])) {
            echo json_encode(['success' => false, 'message' => 'Descrição e tipo são obrigatórios.']);
            exit;
        }

        // Monta os dados e salva
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

    // ====================================
    // 6️⃣ Buscar informações de um Lead específico
    // ====================================
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

    // ====================================
    // 🚫 Caso a ação não exista
    // ====================================
    default:
        // Se a requisição for via AJAX, retorna JSON de erro
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ação desconhecida ou inválida.', 'action_received' => $action]);
            exit;
        }

        // Caso contrário, redireciona pro pipeline
        header("Location: ../views/leads/pipeline.php");
        exit;
}
?>