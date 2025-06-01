<?php
// controllers/DocumentoController.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// O caminho para o model continua o mesmo
require_once __DIR__ . '/../models/DocumentoModel.php';

class DocumentoController {
    private $documentoModel;
    private $db; // Para guardar a conexão MySQLi, se necessário para outras lógicas no controller

    // Defina o caminho base para o diretório de uploads.
    const UPLOAD_DIR = __DIR__ . '/../uploads/documentos_clientes/';


    /**
     * Construtor que recebe a conexão MySQLi.
     * @param mysqli $connection A instância da conexão MySQLi.
     */
    public function __construct($connection) {
        if ($connection instanceof mysqli) {
            $this->db = $connection; // Opcional: guardar a conexão se o controller precisar dela diretamente
            $this->documentoModel = new DocumentoModel($connection); // Passa a conexão para o Model
        } else {
            error_log("DocumentoController: Conexão inválida fornecida.");
            // Em um app real, redirecione para uma página de erro ou lance uma exceção
            throw new Exception("Conexão com banco de dados inválida para DocumentoController.");
        }
        

        // Cria o diretório de upload se não existir
        if (!is_dir(self::UPLOAD_DIR)) {
            if (!mkdir(self::UPLOAD_DIR, 0775, true)) {
                error_log("Falha crítica: Não foi possível criar o diretório de uploads: " . self::UPLOAD_DIR);
                // Considere lançar uma exceção ou tratar de forma mais robusta
            }
        }
    }

    /**
     * Processa o upload de um novo documento.
     */
    public function adicionar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['mensagem_erro'] = "Método de requisição inválido.";
            header('Location: index.php?controller=cliente&action=listar'); // Ou página de erro
            exit;
        }

        if (empty($_POST['id_cliente']) || empty($_POST['nome_documento']) || empty($_POST['tipo_documento']) || empty($_FILES['arquivo_documento']['name'])) {
            $_SESSION['mensagem_erro'] = "Todos os campos marcados com * são obrigatórios, incluindo o arquivo.";
            $idClienteFallback = isset($_POST['id_cliente']) ? '&id_cliente=' . $_POST['id_cliente'] : '';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?controller=cliente&action=mostrar' . $idClienteFallback));
            exit;
        }

        if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['id_usuario'])) {
            $_SESSION['mensagem_erro'] = "Sessão de usuário inválida ou expirada. Faça login novamente.";
            header('Location: index.php?controller=auth&action=login'); // Ajuste para sua rota de login
            exit;
        }

        $idCliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);
        $nomeDocumentoForm = trim(filter_var($_POST['nome_documento'], FILTER_SANITIZE_STRING));
        $tipoDocumentoForm = trim(filter_var($_POST['tipo_documento'], FILTER_SANITIZE_STRING));
        $idUsuario = (int)$_SESSION['usuario']['id_usuario'];

        if (!$idCliente) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        $arquivo = $_FILES['arquivo_documento'];

        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => "O arquivo excede a diretiva upload_max_filesize no php.ini.",
                UPLOAD_ERR_FORM_SIZE  => "O arquivo excede a diretiva MAX_FILE_SIZE especificada no formulário HTML.",
                UPLOAD_ERR_PARTIAL    => "O upload do arquivo foi feito parcialmente.",
                UPLOAD_ERR_NO_FILE    => "Nenhum arquivo foi enviado.",
                UPLOAD_ERR_NO_TMP_DIR => "Faltando uma pasta temporária.",
                UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo no disco.",
                UPLOAD_ERR_EXTENSION  => "Uma extensão do PHP interrompeu o upload do arquivo.",
            ];
            $_SESSION['mensagem_erro'] = "Erro no upload do arquivo: " . ($uploadErrors[$arquivo['error']] ?? "Erro desconhecido.");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $maxFileSize = 10 * 1024 * 1024; // 10 MB
        if ($arquivo['size'] > $maxFileSize) {
            $_SESSION['mensagem_erro'] = "Arquivo muito grande. O tamanho máximo permitido é 10MB.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $permitidos = [
            'application/pdf' => '.pdf',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
            'image/jpeg' => '.jpg',
            'image/png' => '.png'
        ];
        $fileMimeType = mime_content_type($arquivo['tmp_name']);

        if (!array_key_exists($fileMimeType, $permitidos)) {
            $_SESSION['mensagem_erro'] = "Tipo de arquivo não permitido ({$fileMimeType}). Permitidos: PDF, DOC, DOCX, JPG, PNG.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $nomeOriginal = pathinfo($arquivo['name'], PATHINFO_FILENAME);
        $extensao = $permitidos[$fileMimeType];
        $nomeSeguro = preg_replace("/[^a-zA-Z0-9_.-]/", "_", $nomeOriginal);
        $nomeArquivoServidor = 'doc_' . $idCliente . '_' . $idUsuario . '_' . time() . '_' . $nomeSeguro . $extensao;
        
        $caminhoDestino = self::UPLOAD_DIR . $nomeArquivoServidor;

        if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
            $caminhoParaBanco = 'uploads/documentos_clientes/' . $nomeArquivoServidor;

            if ($this->documentoModel->adicionar($idCliente, $idUsuario, $nomeDocumentoForm, $tipoDocumentoForm, $caminhoParaBanco)) {
                $_SESSION['mensagem_sucesso'] = "Documento anexado com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao salvar informações do documento no banco de dados.";
                if (file_exists($caminhoDestino)) {
                    unlink($caminhoDestino);
                }
            }
        } else {
            $_SESSION['mensagem_erro'] = "Falha ao mover o arquivo para o diretório de destino. Verifique as permissões da pasta: " . self::UPLOAD_DIR;
        }

        header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
        exit;
    }

    /**
     * Exclui um documento.
     */
    public function excluir() {
        if (empty($_GET['id_documento']) || empty($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do documento ou do cliente não fornecido para exclusão.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        $idDocumento = filter_input(INPUT_GET, 'id_documento', FILTER_VALIDATE_INT);
        $idCliente = filter_input(INPUT_GET, 'id_cliente', FILTER_VALIDATE_INT);

        if (!$idDocumento || !$idCliente) {
            $_SESSION['mensagem_erro'] = "IDs inválidos para exclusão.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        $documento = $this->documentoModel->buscarPorId($idDocumento);

        if ($documento) {
            $caminhoArquivoServidor = __DIR__ . '/../' . $documento['caminho_arquivo'];

            if ($this->documentoModel->excluir($idDocumento)) {
                if (file_exists($caminhoArquivoServidor)) {
                    if (unlink($caminhoArquivoServidor)) {
                        $_SESSION['mensagem_sucesso'] = "Documento excluído com sucesso!";
                    } else {
                        $_SESSION['mensagem_aviso'] = "Registro do documento excluído do banco, mas falha ao remover o arquivo físico do servidor. Verifique o arquivo: " . htmlspecialchars($documento['caminho_arquivo']);
                         error_log("Falha ao excluir arquivo físico: " . $caminhoArquivoServidor);
                    }
                } else {
                     $_SESSION['mensagem_aviso'] = "Registro do documento excluído do banco, mas o arquivo físico não foi encontrado no servidor: " . htmlspecialchars($documento['caminho_arquivo']);
                     error_log("Arquivo físico não encontrado para exclusão: " . $caminhoArquivoServidor);
                }
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao excluir o registro do documento do banco de dados.";
            }
        } else {
            $_SESSION['mensagem_erro'] = "Documento não encontrado para exclusão.";
        }

        header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
        exit;
    }

     /**
     * Permite o download seguro de um arquivo.
     */
    public function baixar() {
        if (empty($_GET['id_documento'])) {
            http_response_code(400);
            echo "ID do documento não fornecido.";
            exit;
        }

        $idDocumento = filter_input(INPUT_GET, 'id_documento', FILTER_VALIDATE_INT);
        if (!$idDocumento) {
            http_response_code(400);
            echo "ID do documento inválido.";
            exit;
        }

        $documento = $this->documentoModel->buscarPorId($idDocumento);

        if (!$documento || empty($documento['caminho_arquivo'])) {
            http_response_code(404);
            echo "Documento não encontrado ou caminho inválido.";
            exit;
        }

        $caminhoCompletoArquivo = __DIR__ . '/../' . $documento['caminho_arquivo'];

        if (!file_exists($caminhoCompletoArquivo) || !is_readable($caminhoCompletoArquivo)) {
            http_response_code(404);
            error_log("Tentativa de baixar arquivo inexistente ou não legível: " . $caminhoCompletoArquivo);
            echo "Arquivo não encontrado no servidor ou não legível.";
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $caminhoCompletoArquivo);
        finfo_close($finfo);

        $nomeArquivoDownload = basename($documento['nome_documento']);
        if (strpos($nomeArquivoDownload, '.') === false) {
            $nomeArquivoDownload = basename($documento['caminho_arquivo']);
        }
        $nomeArquivoDownload = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nomeArquivoDownload);


        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $nomeArquivoDownload . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminhoCompletoArquivo));
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        readfile($caminhoCompletoArquivo);
        exit;
    }
}
