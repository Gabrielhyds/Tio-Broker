<?php

require_once __DIR__ . '/../models/Tarefa.php';

class TarefaController
{
    private $db;
    private $tarefaModel;
    private $baseUrl;

    public function __construct($db)
    {
        $this->db = $db;
        $this->tarefaModel = new Tarefa($this->db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->baseUrl = 'index.php?controller=tarefa&action=listar';
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function listar()
    {
        $this->verificarLogin();

        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $tarefas = $this->tarefaModel->listarPorUsuario($id_usuario);

        $activeMenu = 'tarefas';
        $conteudo = '../views/tarefas/listar_tarefa_content.php';
        include '../template_base_dashboard.php';
    }

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['usuario']['id_usuario'];
            $id_cliente = $_POST['id_cliente'] ?? null;
            $descricao = $_POST['descricao'] ?? '';
            $status = $_POST['status'] ?? 'pendente';

            $ok = $this->tarefaModel->cadastrar($id_usuario, $id_cliente, $descricao, $status);

            if ($ok) {
                $_SESSION['mensagem_sucesso'] = "Tarefa cadastrada!";
                header("Location: index.php?controller=tarefa&action=listar");
                exit;
            } else {
                echo "Erro ao cadastrar: " . $this->db->error;
                exit;
            }
        }

        $activeMenu = 'tarefas';
        $conteudo = '../views/tarefas/cadastrar_tarefa_content.php';
        include '../template_base_dashboard.php';
    }

    public function editar()
    {
        $this->verificarLogin();

        if (!isset($_GET['id_tarefa']) || !is_numeric($_GET['id_tarefa'])) {
            $_SESSION['mensagem_erro'] = "ID da tarefa inválido.";
            header('Location: ' . $this->baseUrl);
            exit;
        }

        $id_tarefa = (int)$_GET['id_tarefa'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descricao = trim($_POST['descricao'] ?? '');
            $status = $_POST['status'] ?? 'pendente';
            $data_conclusao = ($status === 'concluida') ? date('Y-m-d H:i:s') : null;

            if (empty($descricao)) {
                $_SESSION['mensagem_erro'] = "Descrição obrigatória.";
                $tarefa = ['id_tarefa' => $id_tarefa, 'descricao' => $descricao, 'status' => $status];
                $activeMenu = 'tarefas';
                $conteudo = '../views/tarefas/editar_tarefa_content.php';
                include '../template_base_dashboard.php';
                return;
            }

            if ($this->tarefaModel->atualizar($id_tarefa, $descricao, $status, $data_conclusao)) {
                $_SESSION['mensagem_sucesso'] = "Tarefa atualizada com sucesso!";
                header('Location: ' . $this->baseUrl);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar tarefa.";
                $tarefa = ['id_tarefa' => $id_tarefa, 'descricao' => $descricao, 'status' => $status];
                $activeMenu = 'tarefas';
                $conteudo = '../views/tarefas/editar_tarefa_content.php';
                include '../template_base_dashboard.php';
            }
            return;
        }

        $tarefa = $this->tarefaModel->buscarPorId($id_tarefa);
        if (!$tarefa) {
            $_SESSION['mensagem_erro'] = "Tarefa não encontrada.";
            header('Location: ' . $this->baseUrl);
            exit;
        }

        $activeMenu = 'tarefas';
        $conteudo = '../views/tarefas/editar_tarefa_content.php';
        include '../template_base_dashboard.php';
    }

    public function excluir()
    {
        $this->verificarLogin();

        if (!isset($_GET['id_tarefa']) || !is_numeric($_GET['id_tarefa'])) {
            $_SESSION['mensagem_erro'] = "ID inválido para exclusão.";
            header('Location: ' . $this->baseUrl);
            exit;
        }

        $id_tarefa = (int)$_GET['id_tarefa'];

        if ($this->tarefaModel->excluir($id_tarefa)) {
            $_SESSION['mensagem_sucesso'] = "Tarefa excluída com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir tarefa.";
        }

        header('Location: ' . $this->baseUrl);
        exit;
    }

    // Métodos auxiliares (internos para views)
    public function listarInterno()
    {
        $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
        if (!$id_usuario) return [];
        return $this->tarefaModel->listarPorUsuario($id_usuario);
    }

    public function buscarInterno($id_tarefa)
    {
        return $this->tarefaModel->buscarPorId($id_tarefa);
    }
}
