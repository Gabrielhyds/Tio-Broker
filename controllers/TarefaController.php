<?php
require_once '../config/config.php';
require_once '../models/Tarefa.php';

session_start();

$tarefaModel = new Tarefa($connection);

// Obtem a ação
$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'cadastrar') {
    $id_usuario = $_POST['id_usuario'];
    $tipo_tarefa = $_POST['tipo_tarefa'];
    $descricao = trim($_POST['descricao']);
    $status = $_POST['status'];
    $prioridade = $_POST['prioridade'] ?? 'media';
    $prazo = $_POST['prazo'] ?? null;

    if ($tipo_tarefa === 'cliente') {
        $id_cliente = $_POST['id_cliente'] ?? null;
        if (!$id_cliente) {
            $_SESSION['erro'] = 'Selecione um cliente.';
            header('Location: ../views/tarefas/cadastrar_tarefa.php');
            exit;
        }
        $outro_tipo = null;
    } elseif ($tipo_tarefa === 'outro') {
        $id_cliente = null;
        $outro_tipo = trim($_POST['outro_tipo']);
        if (empty($outro_tipo)) {
            $_SESSION['erro'] = 'Preencha o campo "Outro Tipo".';
            header('Location: ../views/tarefas/cadastrar_tarefa.php');
            exit;
        }
    } else {
        $_SESSION['erro'] = 'Tipo de tarefa inválido.';
        header('Location: ../views/tarefas/cadastrar_tarefa.php');
        exit;
    }

    // Substitui descrição se for tipo "outro"
    $descricaoFinal = ($tipo_tarefa === 'outro') ? $outro_tipo : $descricao;

    $ok = $tarefaModel->criar($id_usuario, $id_cliente, $descricaoFinal, $status, $prioridade, $prazo);

    $_SESSION[$ok ? 'sucesso' : 'erro'] = $ok ? 'Tarefa cadastrada com sucesso!' : 'Erro ao cadastrar tarefa.';
    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}

if ($action === 'editar') {
    $id_tarefa = $_POST['id_tarefa'];
    $id_usuario = $_POST['id_usuario'];
    $tipo_tarefa = $_POST['tipo_tarefa'];
    $descricao = trim($_POST['descricao']);
    $status = $_POST['status'];
    $prioridade = $_POST['prioridade'] ?? 'media';
    $prazo = $_POST['prazo'] ?? null;

    if ($tipo_tarefa === 'cliente') {
        $id_cliente = $_POST['id_cliente'] ?? null;
        if (!$id_cliente) {
            $_SESSION['erro'] = 'Selecione um cliente.';
            header("Location: ../views/tarefas/editar_tarefa.php?id=$id_tarefa");
            exit;
        }
        $outro_tipo = null;
    } elseif ($tipo_tarefa === 'outro') {
        $id_cliente = null;
        $outro_tipo = trim($_POST['outro_tipo']);
        if (empty($outro_tipo)) {
            $_SESSION['erro'] = 'Preencha o campo "Outro Tipo".';
            header("Location: ../views/tarefas/editar_tarefa.php?id=$id_tarefa");
            exit;
        }
    } else {
        $_SESSION['erro'] = 'Tipo de tarefa inválido.';
        header("Location: ../views/tarefas/editar_tarefa.php?id=$id_tarefa");
        exit;
    }

    $descricaoFinal = ($tipo_tarefa === 'outro') ? $outro_tipo : $descricao;

    $ok = $tarefaModel->atualizar($id_tarefa, $id_usuario, $id_cliente, $descricaoFinal, $status, $prioridade, $prazo);

    $_SESSION[$ok ? 'sucesso' : 'erro'] = $ok ? 'Tarefa atualizada com sucesso!' : 'Erro ao atualizar tarefa.';
    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}

if ($action === 'excluir') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['erro'] = 'ID da tarefa inválido.';
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
    }

    $ok = $tarefaModel->excluir($id);
    $_SESSION[$ok ? 'sucesso' : 'erro'] = $ok ? 'Tarefa excluída com sucesso!' : 'Erro ao excluir tarefa.';
    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}
