<?php
// Inicia ou resume a sessão para acessar os dados do usuário logado.
session_start();

// Verifica se há um usuário na sessão. Se não houver, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Inclui os arquivos de configuração e o modelo de dados da Imobiliária.
require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

// Cria uma nova instância do modelo Imobiliaria, passando a conexão com o banco de dados.
$imobiliaria = new Imobiliaria($connection);

// --- CONTROLE DE ACESSO ---
// Obtém os dados do usuário que está logado a partir da sessão.
$usuarioLogado = $_SESSION['usuario'];
// Obtém o nível de permissão do usuário logado.
$permissao = $usuarioLogado['permissao'] ?? '';
// Obtém o ID da imobiliária associada ao usuário logado.
$id_imobiliaria = $usuarioLogado['id_imobiliaria'] ?? null;

// Lógica para determinar quais imobiliárias o usuário pode ver.
if ($permissao === 'SuperAdmin') {
    // Se o usuário for SuperAdmin, busca a lista de todas as imobiliárias ativas.
    $listaImobiliarias = $imobiliaria->listarTodas();
} else {
    // Se não for SuperAdmin, busca apenas a imobiliária do próprio usuário.
    $imob = $imobiliaria->buscarPorId($id_imobiliaria);
    // Cria uma lista contendo apenas a imobiliária do usuário (se ela existir).
    $listaImobiliarias = $imob ? [$imob] : [];
}

// Define qual item do menu de navegação deve ser marcado como "ativo".
$activeMenu = 'usuario_listar';
// Define o nome do arquivo que contém o formulário de cadastro.
$conteudo = 'cadastrar_conteudo.php';
// Inclui o template base da página, que usará a variável $conteudo para carregar o HTML correto.
include '../layout/template_base.php';
