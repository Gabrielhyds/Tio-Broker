<?php
// Inicia a sessão para manipular dados do usuário logado
session_start();

// Inclui os arquivos de configuração e do modelo Usuario
require_once '../config/config.php';
require_once '../models/Usuario.php';

// Instancia o modelo Usuario com a conexão com o banco
$usuario = new Usuario($connection);

/**
 * Função auxiliar para salvar uma foto de perfil enviada via formulário
 * Retorna o caminho do arquivo salvo ou null se nenhuma imagem for enviada
 */
function salvarFoto($inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION); // Obtém a extensão da imagem
        $novoNome = uniqid('foto_', true) . '.' . $ext; // Cria um nome único
        $caminho = '../uploads/' . $novoNome; // Caminho onde será salva

        // Cria o diretório uploads se não existir
        if (!is_dir('../uploads')) mkdir('../uploads');

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
        $fotoPath = salvarFoto('foto'); // Salva a foto se houver

        // Chama o método de cadastro com os dados do formulário
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

        // Redireciona após o cadastro com flag de sucesso
        header('Location: ../views/usuarios/listar.php?sucesso=1');
    }

    // Atualização de usuário existente
    if ($_POST['action'] === 'atualizar') {
        $fotoPath = salvarFoto('foto'); // Tenta salvar nova foto

        // Se não houver nova foto, mantém a antiga
        if (!$fotoPath) {
            $dadosAntigos = $usuario->buscarPorId($_POST['id_usuario']);
            $fotoPath = $dadosAntigos['foto'] ?? null;
        }

        // Chama o método de atualização com os dados atualizados
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

        // Redireciona com flag de sucesso na edição
        header('Location: ../views/usuarios/listar.php?atualizado=1');
    }
}

// Exclusão de usuário via GET
if (isset($_GET['excluir'])) {
    $usuario->excluir($_GET['excluir']);
    header('Location: ../views/usuarios/listar.php?excluido=1');
}
