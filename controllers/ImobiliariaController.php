<?php
// Inicia a sessão para usar mensagens de feedback (ex: sucesso, erro)
session_start();

// Inclui o arquivo de configuração da conexão com o banco de dados
require_once '../config/config.php';

// Inclui o arquivo com a classe Imobiliaria (modelo)
require_once '../models/Imobiliaria.php';

// Cria uma instância da classe Imobiliaria, passando a conexão com o banco
$imobiliaria = new Imobiliaria($connection);

// --- ALTERADO ---
// Rota para lidar com requisições POST (cadastrar, atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ação de cadastro
    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);
        if ($imobiliaria->cadastrar($nome, $cnpj)) {
            $_SESSION['sucesso'] = "Imobiliária cadastrada com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar imobiliária.";
        }
    }

    // Ação de atualização
    if (isset($_POST['action']) && $_POST['action'] === 'atualizar') {
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);
        if ($imobiliaria->atualizar($id, $nome, $cnpj)) {
            $_SESSION['sucesso'] = "Imobiliária atualizada com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar imobiliária.";
        }
    }
    // Redireciona para a listagem após a ação
    header('Location: ../views/imobiliarias/listar_imobiliaria.php');
    exit;
}

// --- ALTERADO ---
// Rota para lidar com requisições GET (excluir)
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    // VERIFICAÇÃO: Checa se existem usuários vinculados antes de excluir
    if ($imobiliaria->temUsuariosVinculados($id)) {
        // Se houver, define mensagem de erro e não exclui
        $_SESSION['erro'] = "Não é possível excluir esta imobiliária, pois existem usuários vinculados a ela.";
    } else {
        // Se não houver, prossegue com a exclusão
        if ($imobiliaria->excluir($id)) {
            $_SESSION['sucesso'] = "Imobiliária excluída com sucesso!";
        } else {
            $_SESSION['erro'] = "Ocorreu um erro ao tentar excluir a imobiliária.";
        }
    }
    // Redireciona para a listagem após a tentativa de exclusão
    header('Location: ../views/imobiliarias/listar_imobiliaria.php');
    exit;
}
