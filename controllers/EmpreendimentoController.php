<?php
// controllers/EmpreendimentoController.php

// Inclui o arquivo de configuração apenas para a conexão e constantes de caminho
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Empreendimento.php';
require_once __DIR__ . '/../config/rotas.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mysqli = $connection; // Pega a conexão do config.php

class EmpreendimentoController {
    private $model;

    public function __construct($mysqli) {
        $this->model = new Empreendimento($mysqli);
    }

    /**
     * Orquestra o cadastro de um novo empreendimento e seus arquivos.
     */
    public function criar($dados, $arquivos) {
        $this->model->beginTransaction();

        try {
            // 1. Salva os arquivos usando o método privado da própria classe
            $imagens = $this->salvarUploads('imagens', 'empreendimentos');
            $videos = $this->salvarUploads('videos', 'empreendimentos');
            $documentos = $this->salvarUploads('documentos', 'empreendimentos');

            // 2. Chama o model para salvar os dados do empreendimento e os caminhos dos arquivos no banco
            $this->model->criar($dados, $imagens, $videos, $documentos);

            // 3. Se tudo deu certo, confirma a transação
            $this->model->commit();
            $_SESSION['sucesso'] = "Empreendimento cadastrado com sucesso.";

        } catch (Exception $e) {
            // 4. Se qualquer passo falhou, desfaz todas as operações no banco
            $this->model->rollback();
            $_SESSION['erro'] = "Erro ao cadastrar empreendimento: " . $e->getMessage();
        }

        // Redireciona de volta para a página de listagem
        header('Location: ../views/empreendimento/listar_empreendimento.php');
        exit;
    }

    /**
     * Processa o upload de múltiplos arquivos, cria a pasta de destino se necessário
     * e retorna um array com os caminhos relativos dos arquivos salvos. (Movido para cá)
     */
    private function salvarUploads($campo, $subpasta)
    {
        $arquivosSalvos = [];
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'doc', 'docx', 'xls', 'xlsx', 'webp'];

        if (!defined('UPLOADS_DIR')) {
            throw new Exception("UPLOADS_DIR não está definido. Verifique o arquivo config.php.");
        }
        $destinoRelativo = 'uploads/' . trim($subpasta, '/') . '/';
        $destinoAbsoluto = UPLOADS_DIR . trim($subpasta, '/') . '/';

        if (!empty($_FILES[$campo]['name'][0])) {
            
            if (!is_dir($destinoAbsoluto)) {
                if (!is_writable(UPLOADS_DIR)) {
                       throw new Exception("Erro de permissão: O diretório de uploads principal ('" . UPLOADS_DIR . "') não tem permissão de escrita pelo servidor.");
                }
                if (!mkdir($destinoAbsoluto, 0755, true)) {
                    throw new Exception("Falha ao criar a pasta de uploads ('" . $destinoAbsoluto . "'). Verifique as permissões do servidor.");
                }
            }

            foreach ($_FILES[$campo]['tmp_name'] as $index => $tmp) {
                if (empty($tmp) || $_FILES[$campo]['error'][$index] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $extensao = strtolower(pathinfo($_FILES[$campo]['name'][$index], PATHINFO_EXTENSION));
                if (!in_array($extensao, $permitidas)) continue;

                $nomeArquivoOriginal = basename($_FILES[$campo]['name'][$index]);
                $nomeSeguro = preg_replace("/[^a-zA-Z0-9-_\.]/", "", pathinfo($nomeArquivoOriginal, PATHINFO_FILENAME));
                $nomeFinal = $nomeSeguro . '_' . uniqid() . '.' . $extensao;
                $caminhoCompleto = $destinoAbsoluto . $nomeFinal;
                $caminhoRelativo = $destinoRelativo . $nomeFinal;

                if (move_uploaded_file($tmp, $caminhoCompleto)) {
                    $arquivosSalvos[] = $caminhoRelativo;
                } else {
                    throw new Exception("Falha ao mover o arquivo enviado: " . $nomeArquivoOriginal);
                }
            }
        }

        return $arquivosSalvos;
    }
}


// --- LÓGICA DE EXECUÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $controller = new EmpreendimentoController($mysqli);
    $controller->criar($_POST, $_FILES);
}

