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
    // Verifica se a requisição POST contém o campo 'action' com o valor 'cadastrar'.
    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
        // Pega o valor do campo 'nome' do formulário e remove espaços em branco do início e do fim.
        $nome = trim($_POST['nome']);
        // Pega o valor do campo 'cnpj' do formulário e remove espaços em branco.
        $cnpj = trim($_POST['cnpj']);
        // Chama o método 'cadastrar' do objeto $imobiliaria, passando os dados.
        if ($imobiliaria->cadastrar($nome, $cnpj)) {
            // Se o cadastro for bem-sucedido, armazena uma mensagem de sucesso na sessão.
            $_SESSION['sucesso'] = "Imobiliária cadastrada com sucesso!";
        } else {
            // Se houver uma falha no cadastro, armazena uma mensagem de erro na sessão.
            $_SESSION['erro'] = "Erro ao cadastrar imobiliária.";
        }
    }

    // Verifica se a requisição POST contém o campo 'action' com o valor 'atualizar'.
    if (isset($_POST['action']) && $_POST['action'] === 'atualizar') {
        // Pega o ID da imobiliária que será atualizada.
        $id = $_POST['id'];
        // Pega o novo valor para o campo 'nome'.
        $nome = trim($_POST['nome']);
        // Pega o novo valor para o campo 'cnpj'.
        $cnpj = trim($_POST['cnpj']);
        // Chama o método 'atualizar' do objeto $imobiliaria.
        if ($imobiliaria->atualizar($id, $nome, $cnpj)) {
            // Se a atualização for bem-sucedida, define uma mensagem de sucesso.
            $_SESSION['sucesso'] = "Imobiliária atualizada com sucesso!";
        } else {
            // Se a atualização falhar, define uma mensagem de erro.
            $_SESSION['erro'] = "Erro ao atualizar imobiliária.";
        }
    }
    // Após processar a ação (cadastrar ou atualizar), redireciona o navegador para a página de listagem.
    header('Location: ../views/imobiliarias/listar_imobiliaria.php');
    // Encerra a execução do script para garantir que o redirecionamento ocorra imediatamente.
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
