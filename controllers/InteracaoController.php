<?php

// Inclui o arquivo do Model 'Interacao', que contém a lógica para interagir com a tabela de interações no banco de dados.
require_once __DIR__ . '/../models/Interacao.php';
// Se precisar de outros models, como Cliente para buscar o cliente após adicionar interação, inclua aqui.

class InteracaoController
{
    // Propriedade para armazenar a instância do Model de Interação.
    private $interacaoModel;
    // private $clienteModel; // Uma propriedade comentada, pronta para ser usada se o ClienteModel for necessário.

    // Construtor da classe, executado quando um objeto InteracaoController é criado.
    public function __construct($db)
    {
        // Cria uma nova instância da classe Interacao, passando a conexão com o banco de dados ($db).
        $this->interacaoModel = new Interacao($db);
        // $this->clienteModel = new Cliente($db); // Linha comentada para inicializar o ClienteModel se necessário.

        // Verifica se uma sessão PHP já foi iniciada.
        if (session_status() == PHP_SESSION_NONE) {
            // Se não houver uma sessão ativa, inicia uma nova.
            session_start();
        }
    }

    // Método privado para verificar se o usuário está logado.
    private function verificarLogin()
    {
        // Verifica se a variável de sessão 'usuario' não está definida.
        if (!isset($_SESSION['usuario'])) {
            // Se não estiver logado, define uma mensagem de erro na sessão.
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para realizar esta ação.";
            // Redireciona o usuário para a página de login.
            header('Location: index.php?controller=auth&action=login');
            // Encerra a execução do script para garantir o redirecionamento.
            exit;
        }
    }

    /**
     * Adiciona uma nova interação para um cliente.
     */
    public function adicionar()
    {
        // Chama o método interno para garantir que o usuário está logado antes de prosseguir.
        $this->verificarLogin();

        // Verifica se o método da requisição HTTP é POST. A adição de dados deve ser feita via POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida se os campos essenciais foram enviados e se o ID do cliente é numérico.
            if (
                empty($_POST['id_cliente']) || !is_numeric($_POST['id_cliente']) ||
                empty($_POST['descricao']) || empty($_POST['tipo_interacao'])
            ) {

                // Se a validação falhar, define uma mensagem de erro na sessão.
                $_SESSION['mensagem_erro'] = "Dados inválidos para adicionar interação. Descrição e tipo são obrigatórios.";

                // Tenta obter o ID do cliente para redirecionar de volta à página correta.
                $idClienteFallback = $_POST['id_cliente'] ?? null;
                if ($idClienteFallback) {
                    // Se o ID do cliente existir, redireciona para a página de detalhes desse cliente.
                    header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idClienteFallback);
                } else {
                    // Se não for possível obter o ID, redireciona para a lista geral de clientes.
                    header('Location: index.php?controller=cliente&action=listar');
                }
                // Encerra a execução do script.
                exit;
            }

            // Converte o ID do cliente para um número inteiro.
            $idCliente = (int)$_POST['id_cliente'];
            // Obtém o ID do usuário logado a partir da sessão.
            $idUsuarioLogado = $_SESSION['usuario']['id_usuario'];

            // Cria um array com os dados da interação para serem passados ao Model.
            $dadosInteracao = [
                'id_cliente' => $idCliente,
                'id_usuario' => $idUsuarioLogado,
                'tipo_interacao' => $_POST['tipo_interacao'],
                'descricao' => trim($_POST['descricao']) // Remove espaços extras da descrição.
                // 'anexo_caminho' => $caminhoDoAnexoSalvo, // Linha comentada, preparada para futura implementação de anexos.
            ];

            // Chama o método 'cadastrar' do Model, passando os dados da interação.
            if ($this->interacaoModel->cadastrar($dadosInteracao)) {
                // Se o cadastro for bem-sucedido, define uma mensagem de sucesso.
                $_SESSION['mensagem_sucesso'] = "Interação registrada com sucesso!";
            } else {
                // Se ocorrer um erro no cadastro, define uma mensagem de erro.
                $_SESSION['mensagem_erro'] = "Erro ao registrar interação. Tente novamente.";
            }

            // Após a tentativa de cadastro, redireciona o usuário de volta para a página de detalhes do cliente.
            header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            // Encerra a execução do script.
            exit;
        } else {
            // Se a requisição não for POST (ex: acesso direto pela URL), a ação é bloqueada.
            $_SESSION['mensagem_aviso'] = "Ação não permitida diretamente.";
            // Redireciona para a lista de clientes.
            header('Location: index.php?controller=cliente&action=listar');
            // Encerra a execução do script.
            exit;
        }
    }
}
