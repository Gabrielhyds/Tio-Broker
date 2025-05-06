<?php
// Inicia a sessão para permitir o uso de variáveis de sessão
session_start();

// Inclui o arquivo de configuração com os dados de conexão ao banco de dados
require_once '../config/config.php';

// Inclui o modelo de usuário que contém os métodos de login e manipulação de dados do usuário
require_once '../models/Usuario.php'; 

// Verifica se a requisição foi feita via método POST e se a ação é de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    
    // Recupera os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Instancia o objeto Usuario passando a conexão com o banco
    $usuario = new Usuario($connection);

    // Chama o método de login para verificar as credenciais
    $dados = $usuario->login($email, $senha);

    // Se o login for bem-sucedido, os dados do usuário são armazenados na sessão
    if ($dados) {
        $_SESSION['usuario'] = $dados;
    
        // Redireciona o usuário para o painel adequado, conforme o nível de permissão
        switch ($dados['permissao']) {
            case 'SuperAdmin':
                header('Location: ../views/dashboard_superadmin.php');
                break;
            case 'Admin':
                header('Location: ../views/dashboard_admin.php');
                break;
            case 'Coordenador':
                header('Location: ../views/dashboard_coordenador.php');
                break;
            case 'Corretor':
                header('Location: ../views/dashboard_corretor.php');
                break;
            default:
                // Caso a permissão não seja reconhecida, exibe erro
                echo "Permissão inválida. Contate o administrador.";
                exit;
        }
        // Encerra o script após o redirecionamento
        exit;
    } else {
        // Se o login falhar, exibe mensagem de erro
        echo "Login inválido.";
    }
}
?>
