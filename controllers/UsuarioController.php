<?php
// Inicia a sessão para poder manipular dados do usuário logado e mensagens de feedback.
session_start();

// Inclui os arquivos de configuração, o modelo de dados 'Usuario' e funções de validação.
require_once '../config/config.php';
require_once '../models/Usuario.php';
require_once '../config/validadores.php';

// Cria uma nova instância da classe 'Usuario', passando a conexão com o banco de dados.
$usuario = new Usuario($connection);

/**
 * Função auxiliar para salvar uma foto de perfil enviada via formulário.
 * @param string $inputName O nome do campo do tipo 'file' no formulário.
 * @return string|null O caminho do arquivo salvo ou null se nenhuma imagem for enviada.
 */
function salvarFoto($inputName)
{
    // Verifica se um arquivo foi enviado e se o upload ocorreu sem erros.
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        // Obtém a extensão original do arquivo.
        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        // Cria um nome de arquivo único para evitar conflitos, prefixado com 'foto_'.
        $novoNome = uniqid('foto_', true) . '.' . $ext;
        // Define o caminho completo de destino para salvar o arquivo.
        $caminho = '../uploads/' . $novoNome;

        // Verifica se o diretório 'uploads' não existe.
        if (!is_dir('../uploads')) {
            // Se não existir, cria o diretório com permissões de leitura/escrita.
            mkdir('../uploads', 0755, true);
        }

        // Move o arquivo temporário do upload para o diretório de destino permanente.
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $caminho);

        // Retorna o caminho onde o arquivo foi salvo para ser armazenado no banco de dados.
        return $caminho;
    }
    // Retorna null se nenhum arquivo válido foi enviado.
    return null;
}

// Verifica se a requisição HTTP foi feita usando o método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Bloco para lidar com o cadastro de um novo usuário.
    if ($_POST['action'] === 'cadastrar') {
        // Valida o CPF fornecido usando uma função externa.
        if (!validarCpf($_POST['cpf'])) {
            // Se o CPF for inválido, define uma mensagem de erro na sessão.
            $_SESSION['mensagem_erro'] = "CPF inválido. Verifique e tente novamente.";
            // Redireciona de volta para a página de cadastro.
            header('Location: ../views/usuarios/cadastrar.php');
            // Encerra o script.
            exit;
        }

        // Tenta salvar a foto enviada e obtém o caminho dela.
        $fotoPath = salvarFoto('foto');
        // Chama o método 'cadastrar' do modelo, passando todos os dados do formulário.
        $usuario->cadastrar(
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['permissao'],
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null, // Usa o operador de coalescência nula para o CRECI (opcional).
            $fotoPath
        );

        // Redireciona para a página de listagem com uma mensagem de sucesso.
        header('Location: ../views/usuarios/listar.php?sucesso=1');
        exit;
    }

    // Bloco para lidar com a atualização de um usuário existente.
    if ($_POST['action'] === 'atualizar') {
        // Valida o CPF fornecido.
        if (!validarCpf($_POST['cpf'])) {
            // Se for inválido, define uma mensagem de erro.
            $_SESSION['mensagem_erro'] = "CPF inválido.";
            // Redireciona de volta para a página de edição do usuário específico.
            header('Location: ../views/usuarios/editar.php?id=' . $_POST['id_usuario']);
            exit;
        }

        // Tenta salvar uma nova foto.
        $fotoPath = salvarFoto('foto');
        // Se nenhuma foto nova foi enviada.
        if (!$fotoPath) {
            // Busca os dados antigos do usuário para manter a foto existente.
            $dadosAntigos = $usuario->buscarPorId($_POST['id_usuario']);
            // Mantém o caminho da foto antiga.
            $fotoPath = $dadosAntigos['foto'] ?? null;
        }

        // Chama o método 'atualizar' do modelo com os novos dados.
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

        // Redireciona para a lista de usuários com uma mensagem de sucesso na atualização.
        header('Location: ../views/usuarios/listar.php?atualizado=1');
        exit;
    }

    // Bloco para lidar com atualização do próprio perfil
    if ($_POST['action'] === 'atualizarPerfil') {
        // Validação de sessão e segurança
        if (!isset($_SESSION['usuario']['id_usuario'])) {
            $_SESSION['erro'] = "Sessão expirada. Por favor, faça login novamente.";
            header('Location: ../auth/login.php');
            exit;
        }

        $id = $_SESSION['usuario']['id_usuario'];
        $nome = trim($_POST['nome']);
        $telefone = trim($_POST['telefone']);
        $senha = $_POST['senha'] ?? null;
        $confirmar_senha = $_POST['confirmar_senha'] ?? null;
        $remover_foto = $_POST['remover_foto'] ?? '0';
        $novaSenhaHash = null; // Usaremos esta variável para a senha criptografada

        // Validação de senha (se for fornecida)
        if (!empty($senha)) {
            // Validação de segurança no servidor
            if (strlen($senha) < 8) {
                $_SESSION['erro'] = "A nova senha deve ter pelo menos 8 caracteres.";
                header('Location: ../views/usuarios/perfil.php');
                exit;
            }
            if ($senha !== $confirmar_senha) {
                $_SESSION['erro'] = "As senhas não coincidem.";
                header('Location: ../views/usuarios/perfil.php');
                exit;
            }
            // ✅ CORREÇÃO 1: Criptografar a nova senha antes de salvar.
            $novaSenhaHash = password_hash($senha, PASSWORD_DEFAULT);
        }

        // Busca os dados atuais do usuário para obter o caminho da foto antiga.
        $dadosAntigos = $usuario->buscarPorId($id);
        $fotoPath = $dadosAntigos['foto'] ?? null;

        // ✅ CORREÇÃO 2: Lógica para remover a foto.
        if ($remover_foto === '1') {
            // Se o arquivo antigo existir, apaga do servidor.
            if ($fotoPath && file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $fotoPath = null; // Define o caminho como nulo para limpar no banco.
        }

        // Upload de uma nova imagem (sobrescreve a remoção se uma nova for enviada)
        $novaFotoPath = salvarFoto('foto');
        if ($novaFotoPath) {
            // Se uma nova foto foi enviada, apaga a antiga se existir.
            if ($fotoPath && file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $fotoPath = $novaFotoPath;
        }

        // Atualiza no banco, passando a senha já criptografada (ou null se não for alterada).
        $resultado = $usuario->atualizarPerfil($id, $nome, $telefone, $novaSenhaHash, $fotoPath);

        if ($resultado) {
            $_SESSION['sucesso'] = "Perfil atualizado com sucesso.";
            // Atualiza os dados da sessão para refletir as mudanças imediatamente.
            $_SESSION['usuario']['nome'] = $nome;
            $_SESSION['usuario']['telefone'] = $telefone;
            // Atualiza a foto na sessão, garantindo que o caminho esteja correto.
            $_SESSION['usuario']['foto'] = $fotoPath ? str_replace('../', '', $fotoPath) : null;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar perfil.";
        }

        header('Location: ../views/usuarios/perfil.php');
        exit;
    }
}

// Bloco para lidar com a exclusão de um usuário via requisição GET.
if (isset($_GET['excluir'])) {
    // Chama o método 'excluir' do modelo, passando o ID do usuário.
    $usuario->excluir($_GET['excluir']);
    // Redireciona para a lista de usuários com uma mensagem de sucesso na exclusão.
    header('Location: ../views/usuarios/listar.php?excluido=1');
    exit;
}

// Bloco para remover o vínculo de um usuário com uma imobiliária.
// Espera receber os parâmetros 'removerImobiliaria' (com o ID do usuário) e 'idImobiliaria'.
if (isset($_GET['removerImobiliaria']) && isset($_GET['idImobiliaria'])) {
    // Converte os IDs para inteiros para segurança.
    $idUsuario     = intval($_GET['removerImobiliaria']);
    $idImobiliaria = intval($_GET['idImobiliaria']);

    // Chama o método no modelo para desvincular o usuário da imobiliária.
    if ($usuario->removerImobiliaria($idUsuario)) {
        // Redireciona de volta para a página de edição da imobiliária com um status de sucesso.
        header("Location: ../views/imobiliarias/editar_imobiliaria.php?id={$idImobiliaria}&removido=1");
        exit;
    } else {
        // Se houver um erro, exibe uma mensagem e encerra.
        echo "Erro ao remover vínculo do usuário.";
        exit;
    }
}

// Bloco para vincular um usuário selecionado a uma imobiliária.
// Vem de um formulário que envia 'incluirUsuario' (ID do usuário) e 'idImobiliaria' via GET.
if (isset($_GET['incluirUsuario']) && isset($_GET['idImobiliaria'])) {
    // Converte os IDs para inteiros.
    $idUsuario     = intval($_GET['incluirUsuario']);
    $idImobiliaria = intval($_GET['idImobiliaria']);

    // Chama o método no modelo para criar o vínculo.
    if ($usuario->vincularImobiliaria($idUsuario, $idImobiliaria)) {
        // Redireciona de volta para a página de edição da imobiliária com um status de sucesso.
        header("Location: ../views/imobiliarias/editar_imobiliaria.php?id={$idImobiliaria}&incluidoUsuario=1");
        exit;
    } else {
        // Se houver um erro, exibe uma mensagem e encerra.
        echo "Erro ao vincular usuário à imobiliária.";
        exit;
    }
}

// Se nenhuma das condições anteriores for atendida, a requisição é considerada inválida.
// Define o código de status HTTP para 400 (Bad Request).
header('HTTP/1.1 400 Bad Request');
// Exibe uma mensagem de erro genérica.
echo "Requisição inválida.";
// Encerra o script.
exit;
