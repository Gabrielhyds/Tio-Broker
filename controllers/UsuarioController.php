<?php
// Inicia a sessão para manipular dados do usuário logado
session_start();

// Inclui os arquivos de configuração e do modelo Usuario
require_once '../config/config.php';
require_once '../models/Usuario.php';
require_once '../config/validadores.php';

// Instancia o modelo Usuario com a conexão com o banco
$usuario = new Usuario($connection);

/**
 * Função auxiliar para salvar uma foto de perfil enviada via formulário
 * Retorna o caminho do arquivo salvo ou null se nenhuma imagem for enviada
 */
function salvarFoto($inputName)
{
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION); // Obtém a extensão da imagem
        $novoNome = uniqid('foto_', true) . '.' . $ext; // Cria um nome único
        $caminho = '../uploads/' . $novoNome; // Caminho onde será salva

        // Cria o diretório uploads se não existir
        if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);

        // Move a imagem temporária para o local definitivo
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $caminho);

        return $caminho; // Retorna o caminho da imagem salva
    }
    return null; // Nenhuma imagem válida enviada
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cadastro de novo usuário
    if ($_POST['action'] === 'cadastrar') {
        if (!validarCpf($_POST['cpf'])) {
            $_SESSION['mensagem_erro'] = "CPF inválido. Verifique e tente novamente.";
            header('Location: ../views/usuarios/cadastrar.php');
            exit;
        }

        $fotoPath = salvarFoto('foto');
        $usuario->cadastrar(
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['permissao'],
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null,
            $fotoPath
        );

        header('Location: ../views/usuarios/listar.php?sucesso=1');
        exit;
    }


    // Atualização de usuário existente
    if ($_POST['action'] === 'atualizar') {
        require_once '../config/validadores.php';

        if (!validarCpf($_POST['cpf'])) {
            $_SESSION['mensagem_erro'] = "CPF inválido.";
            header('Location: ../views/usuarios/editar.php?id=' . $_POST['id_usuario']);
            exit;
        }

        $fotoPath = salvarFoto('foto');
        if (!$fotoPath) {
            $dadosAntigos = $usuario->buscarPorId($_POST['id_usuario']);
            $fotoPath = $dadosAntigos['foto'] ?? null;
        }

        $usuario->atualizar(
            $_POST['id_usuario'],
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['permissao'],
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null,
            $fotoPath
        );

        header('Location: ../views/usuarios/listar.php?atualizado=1');
        exit;
    }
}

// Exclusão de usuário via GET
if (isset($_GET['excluir'])) {
    $usuario->excluir($_GET['excluir']);
    header('Location: ../views/usuarios/listar.php?excluido=1');
    exit;
}

// Remover vínculo de usuário com imobiliária
// Espera receber ?removerImobiliaria={id_usuario}&idImobiliaria={id_imobiliaria}
if (isset($_GET['removerImobiliaria']) && isset($_GET['idImobiliaria'])) {
    $idUsuario     = intval($_GET['removerImobiliaria']);
    $idImobiliaria = intval($_GET['idImobiliaria']);

    // Chama método no modelo para desvincular
    if ($usuario->removerImobiliaria($idUsuario)) {
        // Redireciona de volta para a edição da imobiliária com flag de remoção
        header("Location: ../views/imobiliarias/editar_imobiliaria.php?id={$idImobiliaria}&removido=1");
        exit;
    } else {
        echo "Erro ao remover vínculo do usuário.";
        exit;
    }
}

// 2) NOVO: Vincular o usuário selecionado à imobiliária
// Vem do formulário <select name="incluirUsuario"> via GET
if (isset($_GET['incluirUsuario']) && isset($_GET['idImobiliaria'])) {
    $idUsuario     = intval($_GET['incluirUsuario']);
    $idImobiliaria = intval($_GET['idImobiliaria']);

    if ($usuario->vincularImobiliaria($idUsuario, $idImobiliaria)) {
        header("Location: ../views/imobiliarias/editar_imobiliaria.php?id={$idImobiliaria}&incluidoUsuario=1");
        exit;
    } else {
        echo "Erro ao vincular usuário à imobiliária.";
        exit;
    }
}

// Se nada for acionado, retorna 400
header('HTTP/1.1 400 Bad Request');
echo "Requisição inválida.";
exit;
