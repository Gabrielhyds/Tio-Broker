<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Tarefa.php';

session_start();

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/auth/login.php');
    exit;
}

$tarefaModel = new Tarefa($connection);

// Pega os dados do usuário logado a partir da sessão para segurança e filtros.
$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? 0;
$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 0;
$permissao_usuario = $_SESSION['usuario']['permissao'] ?? '';

// Obtem a ação a ser executada.
$action = $_POST['action'] ?? $_GET['action'] ?? null;

switch ($action) {
    case 'cadastrar':
        cadastrarTarefa($tarefaModel, $id_usuario_logado, $id_imobiliaria_logada);
        break;

    case 'editar':
        editarTarefa($tarefaModel, $id_usuario_logado, $id_imobiliaria_logada, $permissao_usuario);
        break;

    case 'excluir':
        excluirTarefa($tarefaModel, $id_usuario_logado, $id_imobiliaria_logada, $permissao_usuario);
        break;

    default:
        // Se nenhuma ação for especificada, redireciona para a listagem.
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
}

// --- Funções do Controller ---

function cadastrarTarefa($model, $id_usuario, $id_imobiliaria)
{
    $dados = coletarDadosDaTarefa();
    $dados['id_usuario'] = $id_usuario;
    $dados['id_imobiliaria'] = $id_imobiliaria; // Associa a tarefa à imobiliária do usuário

    // Validação dos dados (exemplo)
    if (empty($dados['descricao'])) {
        $_SESSION['erro'] = 'A descrição da tarefa não pode estar vazia.';
        header('Location: ../views/tarefas/cadastrar_tarefa.php');
        exit;
    }

    // NOTA: O método criar() no seu Model/Tarefa.php precisará ser ajustado para aceitar e salvar o id_imobiliaria.
    if ($model->criar($dados)) {
        $_SESSION['sucesso'] = 'Tarefa cadastrada com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao cadastrar tarefa.';
    }

    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}

function editarTarefa($model, $id_usuario, $id_imobiliaria, $permissao)
{
    $id_tarefa = $_POST['id_tarefa'] ?? null;
    if (!$id_tarefa) {
        $_SESSION['erro'] = 'ID da tarefa não informado.';
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
    }

    // Antes de editar, verifica se o usuário tem permissão.
    $tarefaExistente = $model->buscarPorId($id_tarefa);
    if (!$tarefaExistente || ($permissao !== 'SuperAdmin' && $tarefaExistente['id_imobiliaria'] != $id_imobiliaria)) {
        $_SESSION['erro'] = 'Você não tem permissão para editar esta tarefa.';
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
    }

    $dados = coletarDadosDaTarefa();
    $dados['id_tarefa'] = $id_tarefa;
    $dados['id_usuario'] = $id_usuario; // Opcional: reatribuir a tarefa
    $dados['id_imobiliaria'] = $id_imobiliaria;

    // NOTA: O método atualizar() no seu Model/Tarefa.php precisará ser ajustado para usar os novos dados.
    if ($model->atualizar($dados)) {
        $_SESSION['sucesso'] = 'Tarefa atualizada com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao atualizar tarefa.';
    }

    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}

function excluirTarefa($model, $id_usuario, $id_imobiliaria, $permissao)
{
    $id_tarefa = $_GET['id'] ?? null;
    if (!$id_tarefa) {
        $_SESSION['erro'] = 'ID da tarefa inválido.';
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
    }

    // Antes de excluir, verifica se o usuário tem permissão.
    $tarefaExistente = $model->buscarPorId($id_tarefa);
    if (!$tarefaExistente || ($permissao !== 'SuperAdmin' && $tarefaExistente['id_imobiliaria'] != $id_imobiliaria)) {
        $_SESSION['erro'] = 'Você não tem permissão para excluir esta tarefa.';
        header('Location: ../views/tarefas/listar_tarefa.php');
        exit;
    }

    if ($model->excluir($id_tarefa)) {
        $_SESSION['sucesso'] = 'Tarefa excluída com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao excluir tarefa.';
    }

    header('Location: ../views/tarefas/listar_tarefa.php');
    exit;
}

/**
 * Coleta e valida os dados do formulário de tarefas.
 */
function coletarDadosDaTarefa()
{
    $dados = [];
    $dados['tipo_tarefa'] = $_POST['tipo_tarefa'] ?? 'outro';
    $dados['descricao'] = trim($_POST['descricao'] ?? '');
    $dados['status'] = $_POST['status'] ?? 'pendente';
    $dados['prioridade'] = $_POST['prioridade'] ?? 'media';
    $dados['prazo'] = !empty($_POST['prazo']) ? $_POST['prazo'] : null;
    $dados['id_cliente'] = null;

    if ($dados['tipo_tarefa'] === 'cliente') {
        $dados['id_cliente'] = $_POST['id_cliente'] ?? null;
        if (!$dados['id_cliente']) {
            $_SESSION['erro'] = 'Selecione um cliente para a tarefa.';
            header('Location: ' . $_SERVER['HTTP_REFERER']); // Volta para a página anterior
            exit;
        }
    } elseif ($dados['tipo_tarefa'] === 'outro') {
        $outro_tipo = trim($_POST['outro_tipo'] ?? '');
        if (empty($outro_tipo)) {
            $_SESSION['erro'] = 'O campo "Outro Tipo" é obrigatório para este tipo de tarefa.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        // Usa o campo "outro" como a descrição principal.
        $dados['descricao'] = $outro_tipo;
    }

    return $dados;
}
