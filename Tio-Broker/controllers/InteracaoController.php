<?php

// Inclui os arquivos necessários
require_once __DIR__ . '/../models/Interacao.php';
require_once __DIR__ . '/../config/rotas.php'; // Essencial para usar a BASE_URL

class InteracaoController
{
    private $interacaoModel;

    public function __construct($db)
    {
        $this->interacaoModel = new Interacao($db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para realizar esta ação.";
            header('Location: ' . BASE_URL . 'views/auth/login.php');
            exit;
        }
    }

    /**
     * Adiciona uma nova interação e redireciona de volta para a página de origem.
     */
    public function adicionar()
    {
        $this->verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Define uma URL de fallback caso 'redirect_url' não seja enviado
            $redirectFallback = BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar';
            if (!empty($_POST['id_cliente'])) {
                 $redirectFallback = BASE_URL . 'views/contatos/index.php?controller=cliente&action=mostrar&id_cliente=' . $_POST['id_cliente'];
            }
            
            // Pega a URL de redirecionamento do formulário. Se não existir, usa o fallback.
            $redirectUrl = $_POST['redirect_url'] ?? $redirectFallback;


            if (
                empty($_POST['id_cliente']) || !is_numeric($_POST['id_cliente']) ||
                empty($_POST['descricao']) || empty($_POST['tipo_interacao'])
            ) {
                $_SESSION['mensagem_erro'] = "Dados inválidos para adicionar interação. Descrição e tipo são obrigatórios.";
                // Redireciona para a URL de onde o usuário veio
                header('Location: ' . $redirectUrl);
                exit;
            }

            $idCliente = (int)$_POST['id_cliente'];
            $idUsuarioLogado = $_SESSION['usuario']['id_usuario'];

            $dadosInteracao = [
                'id_cliente' => $idCliente,
                'id_usuario' => $idUsuarioLogado,
                'tipo_interacao' => $_POST['tipo_interacao'],
                'descricao' => trim($_POST['descricao'])
            ];

            if ($this->interacaoModel->cadastrar($dadosInteracao)) {
                $_SESSION['mensagem_sucesso'] = "Interação registrada com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao registrar interação. Tente novamente.";
            }

            // CORREÇÃO: Redireciona para a URL de origem usando o campo 'redirect_url'
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $_SESSION['mensagem_aviso'] = "Ação não permitida diretamente.";
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
    }
}
