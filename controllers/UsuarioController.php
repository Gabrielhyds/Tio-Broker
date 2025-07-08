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
        // (código de cadastro existente - sem alterações)
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
            // ✅ CORREÇÃO: Usando md5() para compatibilidade com o sistema legado.
            // ATENÇÃO: MD5 não é seguro para senhas. O ideal é migrar seu sistema
            // para usar password_hash() e password_verify().
            $novaSenhaHash = md5($senha);
        }

        // Busca os dados atuais do usuário para obter o caminho da foto antiga.
        $dadosAntigos = $usuario->buscarPorId($id);
        $fotoPath = $dadosAntigos['foto'] ?? null;

        // Lógica para remover a foto.
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
    // (código de exclusão existente - sem alterações)
    $usuario->excluir($_GET['excluir']);
    header('Location: ../views/usuarios/listar.php?excluido=1');
    exit;
}

// (Restante do seu código... sem alterações)

// Se nenhuma das condições anteriores for atendida, a requisição é considerada inválida.
header('HTTP/1.1 400 Bad Request');
echo "Requisição inválida.";
exit;
