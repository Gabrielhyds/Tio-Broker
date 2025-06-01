<?php

require_once __DIR__ . '/../models/Interacao.php';
// Se precisar de outros models, como Cliente para buscar o cliente após adicionar interação, inclua aqui.

class InteracaoController
{
    private $interacaoModel;
    // private $clienteModel; // Descomente se precisar do ClienteModel

    public function __construct($db)
    {
        $this->interacaoModel = new Interacao($db);
        // $this->clienteModel = new Cliente($db); // Descomente se precisar
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarLogin() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para realizar esta ação.";
            // Tenta redirecionar para a página de login do contexto atual
            // Se o InteracaoController for chamado pelo index.php de views/contatos/
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Adiciona uma nova interação para um cliente.
     */
    public function adicionar()
    {
        $this->verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validação básica dos dados recebidos
            if (empty($_POST['id_cliente']) || !is_numeric($_POST['id_cliente']) || 
                empty($_POST['descricao']) || empty($_POST['tipo_interacao'])) {
                
                $_SESSION['mensagem_erro'] = "Dados inválidos para adicionar interação. Descrição e tipo são obrigatórios.";
                // Redireciona de volta para a página do cliente, se o ID do cliente estiver disponível
                $idClienteFallback = $_POST['id_cliente'] ?? null;
                if ($idClienteFallback) {
                    header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idClienteFallback);
                } else {
                    header('Location: index.php?controller=cliente&action=listar'); // Fallback para a lista
                }
                exit;
            }

            $idCliente = (int)$_POST['id_cliente'];
            $idUsuarioLogado = $_SESSION['usuario']['id_usuario']; // Usuário que está registrando a interação

            $dadosInteracao = [
                'id_cliente' => $idCliente,
                'id_usuario' => $idUsuarioLogado,
                'tipo_interacao' => $_POST['tipo_interacao'],
                'descricao' => trim($_POST['descricao'])
                // 'anexo_caminho' => $caminhoDoAnexoSalvo, // Para quando implementar anexos
            ];

            if ($this->interacaoModel->cadastrar($dadosInteracao)) {
                $_SESSION['mensagem_sucesso'] = "Interação registrada com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao registrar interação. Tente novamente.";
            }

            // Redireciona de volta para a página de detalhes do cliente
            header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            exit;
        } else {
            // Se alguém tentar acessar a ação adicionar via GET, redireciona para a lista de clientes
            $_SESSION['mensagem_aviso'] = "Ação não permitida diretamente.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
    }
}
