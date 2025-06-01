<?php
// Inicia a sessão para garantir que o usuário está autenticado (se necessário futuramente)
session_start();

// Inclui o arquivo de configuração da conexão com o banco de dados
require_once '../config/config.php';

// Inclui o arquivo com a classe Imobiliaria (modelo)
require_once '../models/Imobiliaria.php';

// Cria uma instância da classe Imobiliaria, passando a conexão com o banco
$imobiliaria = new Imobiliaria($connection);

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ação de cadastro de nova imobiliária
    if ($_POST['action'] === 'cadastrar') {
        // Obtém e limpa os dados do formulário
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);

        // Tenta cadastrar no banco de dados
        if ($imobiliaria->cadastrar($nome, $cnpj)) {
            // Redireciona para a listagem com mensagem de sucesso
            header('Location: ../views/imobiliarias/listar_imobiliaria.php?sucesso=1');
        } else {
            // Exibe erro se falhar
            echo "Erro ao cadastrar imobiliária.";
        }
    }

    // Ação de atualização de imobiliária existente
    if ($_POST['action'] === 'atualizar') {
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);

        // Tenta atualizar no banco
        if ($imobiliaria->atualizar($id, $nome, $cnpj)) {
            header('Location: ../views/imobiliarias/listar_imobiliaria.php?atualizado=1');
        } else {
            echo "Erro ao atualizar imobiliária.";
        }
    }
}

// Ação para excluir uma imobiliária (passado por GET)
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    
    if ($imobiliaria->excluir($id)) {
        // Redireciona após exclusão
        header('Location: ../views/imobiliarias/listar_imobiliaria.php?excluido=1');
    } else {
        echo "Erro ao excluir imobiliária.";
    }
}
?>
