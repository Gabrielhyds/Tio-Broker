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
        // ✅ Protege o cadastro de SuperAdmin
        $permissaoRecebida = $_POST['permissao'];

        if ($permissaoRecebida === 'SuperAdmin' && $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
            $_SESSION['mensagem_erro'] = "Apenas SuperAdmins podem cadastrar outros SuperAdmins.";
            header('Location: ../views/usuarios/cadastrar.php');
            exit;
        }

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

    // Bloco para lidar com a atualização de um usuário existente.
    if ($_POST['action'] === 'atualizar') {
        // (código de atualização existente - sem alterações)
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
        // Protege a permissão do SuperAdmin
        $usuarioExistente = $usuario->buscarPorId($_POST['id_usuario']);
        $permissaoFinal = $_POST['permissao'];

        // Se o usuário já for SuperAdmin, mantém essa permissão fixamente
        if ($usuarioExistente && $usuarioExistente['permissao'] === 'SuperAdmin') {
            $permissaoFinal = 'SuperAdmin'; // Força a manter a permissão
        }

        $usuario->atualizar(
            $_POST['id_usuario'],
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $permissaoFinal,
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null,
            $fotoPath
        );
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
        // ✅✅✅ CORREÇÃO: Captura o e-mail do formulário.
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone']);
        $senha = $_POST['senha'] ?? null;
        $confirmar_senha = $_POST['confirmar_senha'] ?? null;
        $remover_foto = $_POST['remover_foto'] ?? '0';
        $novaSenhaHash = null;

        // Validação de senha (se for fornecida)
        if (!empty($senha)) {
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
            $novaSenhaHash = md5($senha);
        }

        // Lógica para foto
        $dadosAntigos = $usuario->buscarPorId($id);
        $fotoPath = $dadosAntigos['foto'] ?? null;

        if ($remover_foto === '1') {
            if ($fotoPath && file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $fotoPath = null;
        }

        $novaFotoPath = salvarFoto('foto');
        if ($novaFotoPath) {
            if ($fotoPath && file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $fotoPath = $novaFotoPath;
        }

        // ✅✅✅ CORREÇÃO: Passa os parâmetros na ordem correta para o Model.
        $resultado = $usuario->atualizarPerfil($id, $nome, $email, $telefone, $novaSenhaHash, $fotoPath);

        if ($resultado) {
            $_SESSION['sucesso'] = "Perfil atualizado com sucesso.";
            // Atualiza os dados da sessão para refletir as mudanças imediatamente.
            $_SESSION['usuario']['nome'] = $nome;
            $_SESSION['usuario']['email'] = $email; // Atualiza o e-mail na sessão
            $_SESSION['usuario']['telefone'] = $telefone;
            $_SESSION['usuario']['foto'] = $fotoPath ? str_replace('../', '', $fotoPath) : null;
        } else {
            // A mensagem de erro específica já é definida no Model
            if (!isset($_SESSION['erro'])) {
                $_SESSION['erro'] = "Erro ao atualizar perfil. O e-mail pode já estar em uso.";
            }
        }

        header('Location: ../views/usuarios/perfil.php');
        exit;
    }

    // ✅✅✅ INÍCIO DA CORREÇÃO ✅✅✅
    // Bloco para vincular um usuário a uma imobiliária (vindo de um formulário)
    if ($_POST['action'] === 'vincularUsuario') {
        $idUsuarioParaVincular = (int)($_POST['id_usuario'] ?? 0);
        $idImobiliariaParaRedirecionar = (int)($_POST['id_imobiliaria'] ?? 0);

        if ($idUsuarioParaVincular > 0 && $idImobiliariaParaRedirecionar > 0) {
            if ($usuario->vincularImobiliaria($idUsuarioParaVincular, $idImobiliariaParaRedirecionar)) {
                $_SESSION['sucesso'] = "Usuário vinculado com sucesso!";
            } else {
                $_SESSION['erro'] = "Ocorreu um erro ao vincular o usuário.";
            }
        } else {
            $_SESSION['erro'] = "Dados inválidos para vincular usuário.";
        }
        header("Location: ../views/imobiliarias/editar_imobiliaria.php?id=" . $idImobiliariaParaRedirecionar);
        exit;
    }
}

// Bloco para lidar com a exclusão de um usuário via requisição GET.
if (isset($_GET['excluir'])) {
    $usuario->excluir((int)$_GET['excluir']);
    $_SESSION['sucesso'] = "Usuário excluído com sucesso."; // Feedback de exclusão
    header('Location: ../views/usuarios/listar.php');
    exit;
}

// Bloco para remover o vínculo de um usuário com uma imobiliária
if (isset($_GET['removerImobiliaria']) && isset($_GET['idImobiliaria'])) {
    // NOTA: Usar GET para ações que modificam dados não é a melhor prática.
    // O ideal seria usar um formulário com método POST.
    $idUsuarioParaRemover = (int)$_GET['removerImobiliaria'];
    $idImobiliariaParaRedirecionar = (int)$_GET['idImobiliaria'];

    if ($usuario->removerImobiliaria($idUsuarioParaRemover)) {
        $_SESSION['sucesso'] = "Usuário desvinculado com sucesso!";
    } else {
        $_SESSION['erro'] = "Ocorreu um erro ao desvincular o usuário.";
    }
    header("Location: ../views/imobiliarias/editar_imobiliaria.php?id=" . $idImobiliariaParaRedirecionar);
    exit;
}

// ✅✅✅ INÍCIO DA CORREÇÃO ✅✅✅
// Bloco para VINCULAR um usuário a uma imobiliária via GET
// NOTA: O ideal é usar o método POST para ações que modificam dados.
// Este bloco foi ajustado para funcionar com o formulário existente que usa GET.
if (isset($_GET['incluirUsuario']) && isset($_GET['idImobiliaria'])) {
    $idUsuarioParaVincular = (int)$_GET['incluirUsuario'];
    $idImobiliariaParaRedirecionar = (int)$_GET['idImobiliaria'];

    if ($idUsuarioParaVincular > 0 && $idImobiliariaParaRedirecionar > 0) {
        if ($usuario->vincularImobiliaria($idUsuarioParaVincular, $idImobiliariaParaRedirecionar)) {
            $_SESSION['sucesso'] = "Usuário vinculado com sucesso!";
        } else {
            $_SESSION['erro'] = "Ocorreu um erro ao vincular o usuário.";
        }
    } else {
        $_SESSION['erro'] = "Dados inválidos para vincular usuário.";
    }
    header("Location: ../views/imobiliarias/editar_imobiliaria.php?id=" . $idImobiliariaParaRedirecionar);
    exit;
}
// ✅✅✅ FIM DA CORREÇÃO ✅✅✅



// (Restante do seu código... sem alterações)

// Se nenhuma das condições anteriores for atendida, a requisição é considerada inválida.
header('HTTP/1.1 400 Bad Request');
echo "Requisição inválida.";
exit;
