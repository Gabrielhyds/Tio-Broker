<?php
// Inicia ou resume uma sessão PHP existente. Essencial para usar a superglobal $_SESSION.
session_start();

// Inclui o arquivo de configuração, que geralmente contém as credenciais e a criação da variável de conexão com o banco de dados.
require_once '../config/config.php';

// Inclui o arquivo do Model 'Imobiliaria'. O Model contém a lógica de negócio e as interações com o banco de dados para a entidade imobiliária.
require_once '../models/Imobiliaria.php';

// Cria uma nova instância da classe 'Imobiliaria', passando a variável de conexão ($connection) que veio do arquivo config.php.
$imobiliaria = new Imobiliaria($connection);

// --- Bloco para Requisições POST ---
// Verifica se o método da requisição HTTP é POST, usado para enviar dados de formulários (neste caso, para cadastrar ou atualizar).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pessoa = $_POST['tipo_pessoa']; // 'F' ou 'J'
    $nome = trim($_POST['nome']);
    $documento = trim($_POST['documento']); // CPF ou CNPJ

    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
        try {
            if ($imobiliaria->cadastrar($nome, $documento, $tipo_pessoa)) {
                $_SESSION['sucesso'] = "Imobiliária cadastrada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao cadastrar imobiliária.";
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'atualizar') {
        $id = $_POST['id'];
        try {
            if ($imobiliaria->atualizar($id, $nome, $documento, $tipo_pessoa)) {
                $_SESSION['sucesso'] = "Imobiliária atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar imobiliária.";
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }
    }

    header('Location: ../views/imobiliarias/listar_imobiliaria.php');
    exit;
}

// --- Bloco para Requisições GET ---
// Verifica se a URL contém o parâmetro 'excluir' (ex: .../controller.php?excluir=123).
if (isset($_GET['excluir'])) {
    // Pega o valor do parâmetro 'excluir', que é o ID da imobiliária a ser removida.
    $id = $_GET['excluir'];

    // Antes de excluir, chama um método para verificar se existem usuários associados a esta imobiliária.
    if ($imobiliaria->temUsuariosVinculados($id)) {
        // Se houver usuários vinculados, a exclusão é bloqueada e uma mensagem de erro é definida na sessão.
        $_SESSION['erro'] = "Não é possível excluir esta imobiliária, pois existem usuários vinculados a ela.";
    } else {
        // Se não houver usuários vinculados, a exclusão pode prosseguir.
        // Chama o método 'excluir' do objeto $imobiliaria.
        if ($imobiliaria->excluir($id)) {
            // Se a exclusão for bem-sucedida, define uma mensagem de sucesso.
            $_SESSION['sucesso'] = "Imobiliária excluída com sucesso!";
        } else {
            // Se a exclusão falhar por algum motivo, define uma mensagem de erro.
            $_SESSION['erro'] = "Ocorreu um erro ao tentar excluir a imobiliária.";
        }
    }
    // Após a tentativa de exclusão (bem-sucedida ou não), redireciona o usuário para a página de listagem.
    header('Location: ../views/imobiliarias/listar_imobiliaria.php');
    // Encerra a execução do script.
    exit;
}
