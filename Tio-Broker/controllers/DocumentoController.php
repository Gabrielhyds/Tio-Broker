<?php
// controllers/DocumentoController.php

// Garante que uma sessão PHP seja iniciada. Se nenhuma sessão estiver ativa, ela é criada.
// Isso é crucial para usar a superglobal $_SESSION para mensagens de feedback e dados do usuário.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclui o arquivo do Model de Documento. O Model é responsável por toda a interação
// com a tabela 'documentos' no banco de dados (inserir, buscar, excluir, etc.).
require_once __DIR__ . '/../models/DocumentoModel.php';

/**
 * Classe DocumentoController
 * Gerencia todas as ações relacionadas a documentos, como upload (adicionar), excluir e baixar.
 * Atua como intermediário entre a View (interface do usuário) и o Model (lógica de banco de dados).
 */
class DocumentoController
{
    // Propriedade para armazenar a instância do DocumentoModel.
    private $documentoModel;
    // Propriedade para armazenar a conexão com o banco de dados (mysqli), se necessário para outras lógicas no controller.
    private $db;

    // Define uma constante com o caminho absoluto para o diretório de uploads.
    // Usar __DIR__ torna o caminho mais robusto e independente do local de onde o script é chamado.
    const UPLOAD_DIR = __DIR__ . '/../uploads/documentos_clientes/';


    /**
     * Construtor da classe. É chamado automaticamente quando um objeto DocumentoController é criado.
     * Recebe a conexão com o banco de dados como um parâmetro.
     * @param mysqli $connection A instância da conexão MySQLi.
     */
    public function __construct($connection)
    {
        // Verifica se a conexão fornecida é um objeto mysqli válido.
        if ($connection instanceof mysqli) {
            // Guarda a conexão na propriedade $db (opcional, mas pode ser útil).
            $this->db = $connection;
            // Cria uma nova instância de DocumentoModel, passando a conexão para ele.
            $this->documentoModel = new DocumentoModel($connection);
        } else {
            // Se a conexão for inválida, registra um erro no log do servidor.
            error_log("DocumentoController: Conexão inválida fornecida.");
            // Lança uma exceção para interromper a execução, pois o controller não pode funcionar sem o banco de dados.
            throw new Exception("Conexão com banco de dados inválida para DocumentoController.");
        }

        // Verifica se o diretório de upload (definido na constante UPLOAD_DIR) não existe.
        if (!is_dir(self::UPLOAD_DIR)) {
            // Se não existir, tenta criar o diretório com permissões 0775 (leitura, escrita e execução para dono/grupo, leitura/execução para outros).
            // O terceiro parâmetro 'true' permite a criação de diretórios aninhados (recursivo).
            if (!mkdir(self::UPLOAD_DIR, 0775, true)) {
                // Se a criação do diretório falhar, registra um erro crítico no log.
                error_log("Falha crítica: Não foi possível criar o diretório de uploads: " . self::UPLOAD_DIR);
                // Em uma aplicação real, seria ideal tratar esse erro de forma mais robusta (ex: exibir uma página de erro).
            }
        }
    }

    /**
     * Processa o upload de um novo documento vindo de um formulário.
     */
    public function adicionar()
    {
        // Verifica se o método da requisição HTTP é POST. O envio de formulários com arquivos deve usar POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Se não for POST, define uma mensagem de erro na sessão.
            $_SESSION['mensagem_erro'] = "Método de requisição inválido.";
            // Redireciona o usuário de volta para a lista de clientes.
            header('Location: index.php?controller=cliente&action=listar');
            // Interrompe a execução do script.
            exit;
        }

        // Verifica se os campos obrigatórios do formulário e o arquivo foram enviados.
        if (empty($_POST['id_cliente']) || empty($_POST['nome_documento']) || empty($_POST['tipo_documento']) || empty($_FILES['arquivo_documento']['name'])) {
            // Se algum campo estiver faltando, define uma mensagem de erro.
            $_SESSION['mensagem_erro'] = "Todos os campos marcados com * são obrigatórios, incluindo o arquivo.";
            // Prepara um fallback para o ID do cliente para redirecionar de volta para a página correta.
            $idClienteFallback = isset($_POST['id_cliente']) ? '&id_cliente=' . $_POST['id_cliente'] : '';
            // Redireciona o usuário para a página anterior (de onde o formulário foi enviado) ou para a página de detalhes do cliente.
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?controller=cliente&action=mostrar' . $idClienteFallback));
            exit;
        }

        // Verifica se existe um usuário logado na sessão.
        if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario']['id_usuario'])) {
            // Se não houver, a ação não é permitida. Redireciona para a página de login.
            $_SESSION['mensagem_erro'] = "Sessão de usuário inválida ou expirada. Faça login novamente.";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Filtra e valida os dados recebidos do formulário para segurança.
        $idCliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT); // Valida se o ID do cliente é um inteiro.
        $nomeDocumentoForm = trim(filter_var($_POST['nome_documento'], FILTER_SANITIZE_STRING)); // Remove espaços e tags HTML do nome.
        $tipoDocumentoForm = trim(filter_var($_POST['tipo_documento'], FILTER_SANITIZE_STRING)); // Remove espaços e tags HTML do tipo.
        $idUsuario = (int)$_SESSION['usuario']['id_usuario']; // Pega o ID do usuário da sessão e converte para inteiro.

        // Se o ID do cliente não for um inteiro válido após a filtragem, a operação é abortada.
        if (!$idCliente) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        // Pega as informações do arquivo enviado através da superglobal $_FILES.
        $arquivo = $_FILES['arquivo_documento'];

        // Verifica se ocorreu algum erro durante o processo de upload do arquivo.
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            // Mapeia os códigos de erro de upload para mensagens legíveis.
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => "O arquivo excede a diretiva upload_max_filesize no php.ini.",
                UPLOAD_ERR_FORM_SIZE  => "O arquivo excede a diretiva MAX_FILE_SIZE especificada no formulário HTML.",
                UPLOAD_ERR_PARTIAL    => "O upload do arquivo foi feito parcialmente.",
                UPLOAD_ERR_NO_FILE    => "Nenhum arquivo foi enviado.",
                UPLOAD_ERR_NO_TMP_DIR => "Faltando uma pasta temporária.",
                UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo no disco.",
                UPLOAD_ERR_EXTENSION  => "Uma extensão do PHP interrompeu o upload do arquivo.",
            ];
            // Define a mensagem de erro correspondente ao código ou uma mensagem genérica.
            $_SESSION['mensagem_erro'] = "Erro no upload do arquivo: " . ($uploadErrors[$arquivo['error']] ?? "Erro desconhecido.");
            // Redireciona de volta para a página anterior.
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Define o tamanho máximo permitido para o arquivo (aqui, 10 MB).
        $maxFileSize = 10 * 1024 * 1024; // 10 MB
        // Compara o tamanho do arquivo enviado com o limite máximo.
        if ($arquivo['size'] > $maxFileSize) {
            $_SESSION['mensagem_erro'] = "Arquivo muito grande. O tamanho máximo permitido é 10MB.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Define uma lista (array) de tipos de arquivo (MIME types) permitidos e suas extensões correspondentes.
        $permitidos = [
            'application/pdf' => '.pdf',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
            'image/jpeg' => '.jpg',
            'image/png' => '.png'
        ];
        // Determina o MIME type real do arquivo lendo seu conteúdo, o que é mais seguro do que confiar na extensão.
        $fileMimeType = mime_content_type($arquivo['tmp_name']);

        // Verifica se o MIME type do arquivo está na lista de tipos permitidos.
        if (!array_key_exists($fileMimeType, $permitidos)) {
            $_SESSION['mensagem_erro'] = "Tipo de arquivo não permitido ({$fileMimeType}). Permitidos: PDF, DOC, DOCX, JPG, PNG.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Extrai o nome original do arquivo sem a extensão.
        $nomeOriginal = pathinfo($arquivo['name'], PATHINFO_FILENAME);
        // Pega a extensão correta com base no MIME type verificado.
        $extensao = $permitidos[$fileMimeType];
        // Limpa o nome original, substituindo caracteres não alfanuméricos por underscores para criar um nome seguro.
        $nomeSeguro = preg_replace("/[^a-zA-Z0-9_.-]/", "_", $nomeOriginal);
        // Monta um nome de arquivo único para ser salvo no servidor, evitando conflitos.
        // Inclui IDs, timestamp e o nome seguro.
        $nomeArquivoServidor = 'doc_' . $idCliente . '_' . $idUsuario . '_' . time() . '_' . $nomeSeguro . $extensao;

        // Monta o caminho completo de destino onde o arquivo será salvo.
        $caminhoDestino = self::UPLOAD_DIR . $nomeArquivoServidor;

        // Tenta mover o arquivo temporário (do upload) para o diretório de destino permanente.
        if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
            // Se o arquivo foi movido com sucesso, define o caminho relativo que será salvo no banco de dados.
            $caminhoParaBanco = 'uploads/documentos_clientes/' . $nomeArquivoServidor;

            // Chama o método do Model para inserir as informações do documento no banco de dados.
            if ($this->documentoModel->adicionar($idCliente, $idUsuario, $nomeDocumentoForm, $tipoDocumentoForm, $caminhoParaBanco)) {
                // Se a inserção no banco for bem-sucedida, define uma mensagem de sucesso.
                $_SESSION['mensagem_sucesso'] = "Documento anexado com sucesso!";
            } else {
                // Se a inserção no banco falhar, define uma mensagem de erro.
                $_SESSION['mensagem_erro'] = "Erro ao salvar informações do documento no banco de dados.";
                // "Rollback": Como o registro no banco falhou, o arquivo físico que já foi salvo é excluído para manter a consistência.
                if (file_exists($caminhoDestino)) {
                    unlink($caminhoDestino);
                }
            }
        } else {
            // Se `move_uploaded_file` falhar, provavelmente é um problema de permissão na pasta de uploads.
            $_SESSION['mensagem_erro'] = "Falha ao mover o arquivo para o diretório de destino. Verifique as permissões da pasta: " . self::UPLOAD_DIR;
        }

        // Após toda a lógica, redireciona o usuário para a página de detalhes do cliente correspondente.
        header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
        exit;
    }

    /**
     * Processa a exclusão de um documento existente.
     */
    public function excluir()
    {
        // Verifica se os IDs do documento e do cliente foram passados via GET na URL.
        if (empty($_GET['id_documento']) || empty($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do documento ou do cliente não fornecido para exclusão.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        // Filtra e valida os IDs recebidos.
        $idDocumento = filter_input(INPUT_GET, 'id_documento', FILTER_VALIDATE_INT);
        $idCliente = filter_input(INPUT_GET, 'id_cliente', FILTER_VALIDATE_INT);

        // Se algum dos IDs for inválido, aborta a operação.
        if (!$idDocumento || !$idCliente) {
            $_SESSION['mensagem_erro'] = "IDs inválidos para exclusão.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        // Busca os dados do documento no banco de dados usando seu ID.
        // Isso é necessário para obter o caminho do arquivo físico e excluí-lo.
        $documento = $this->documentoModel->buscarPorId($idDocumento);

        // Se o documento foi encontrado no banco de dados.
        if ($documento) {
            // Monta o caminho absoluto para o arquivo no servidor.
            $caminhoArquivoServidor = __DIR__ . '/../' . $documento['caminho_arquivo'];

            // Tenta excluir o registro do documento do banco de dados.
            if ($this->documentoModel->excluir($idDocumento)) {
                // Se o registro foi excluído do banco, agora tenta excluir o arquivo físico.
                if (file_exists($caminhoArquivoServidor)) {
                    // Tenta remover o arquivo do servidor.
                    if (unlink($caminhoArquivoServidor)) {
                        // Se ambos (registro e arquivo) foram excluídos com sucesso.
                        $_SESSION['mensagem_sucesso'] = "Documento excluído com sucesso!";
                    } else {
                        // Se o registro foi excluído, mas o arquivo físico não pôde ser removido.
                        $_SESSION['mensagem_aviso'] = "Registro do documento excluído do banco, mas falha ao remover o arquivo físico do servidor. Verifique o arquivo: " . htmlspecialchars($documento['caminho_arquivo']);
                        error_log("Falha ao excluir arquivo físico: " . $caminhoArquivoServidor);
                    }
                } else {
                    // Se o registro foi excluído, mas o arquivo físico correspondente não foi encontrado no servidor.
                    $_SESSION['mensagem_aviso'] = "Registro do documento excluído do banco, mas o arquivo físico não foi encontrado no servidor: " . htmlspecialchars($documento['caminho_arquivo']);
                    error_log("Arquivo físico não encontrado para exclusão: " . $caminhoArquivoServidor);
                }
            } else {
                // Se a exclusão do registro no banco de dados falhou.
                $_SESSION['mensagem_erro'] = "Erro ao excluir o registro do documento do banco de dados.";
            }
        } else {
            // Se o ID do documento fornecido não corresponde a nenhum registro no banco.
            $_SESSION['mensagem_erro'] = "Documento não encontrado para exclusão.";
        }

        // Ao final, redireciona o usuário de volta para a página de detalhes do cliente.
        header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
        exit;
    }

    /**
     * Força o download seguro de um arquivo, escondendo sua localização real no servidor.
     */
    public function baixar()
    {
        // Verifica se o ID do documento foi fornecido na URL.
        if (empty($_GET['id_documento'])) {
            // Responde com um código de erro HTTP 400 (Bad Request).
            http_response_code(400);
            echo "ID do documento não fornecido.";
            exit;
        }

        // Valida o ID do documento.
        $idDocumento = filter_input(INPUT_GET, 'id_documento', FILTER_VALIDATE_INT);
        if (!$idDocumento) {
            http_response_code(400);
            echo "ID do documento inválido.";
            exit;
        }

        // Busca os dados do documento no banco de dados.
        $documento = $this->documentoModel->buscarPorId($idDocumento);

        // Se o documento não for encontrado ou não tiver um caminho de arquivo associado.
        if (!$documento || empty($documento['caminho_arquivo'])) {
            // Responde com um código de erro HTTP 404 (Not Found).
            http_response_code(404);
            echo "Documento não encontrado ou caminho inválido.";
            exit;
        }

        // Monta o caminho absoluto completo para o arquivo no servidor.
        $caminhoCompletoArquivo = __DIR__ . '/../' . $documento['caminho_arquivo'];

        // Verifica se o arquivo realmente existe no caminho especificado e se o script tem permissão para lê-lo.
        if (!file_exists($caminhoCompletoArquivo) || !is_readable($caminhoCompletoArquivo)) {
            http_response_code(404);
            error_log("Tentativa de baixar arquivo inexistente ou não legível: " . $caminhoCompletoArquivo);
            echo "Arquivo não encontrado no servidor ou não legível.";
            exit;
        }

        // Usa a extensão Fileinfo para obter o MIME type do arquivo de forma segura.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $caminhoCompletoArquivo);
        finfo_close($finfo);

        // Define o nome que o arquivo terá ao ser baixado pelo usuário.
        // Usa o 'nome_documento' do banco de dados como preferência.
        $nomeArquivoDownload = basename($documento['nome_documento']);
        // Se o nome do documento no banco não tiver uma extensão, usa o nome do arquivo do servidor como fallback.
        if (strpos($nomeArquivoDownload, '.') === false) {
            $nomeArquivoDownload = basename($documento['caminho_arquivo']);
        }
        // Limpa o nome do arquivo para garantir que seja seguro.
        $nomeArquivoDownload = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nomeArquivoDownload);


        // Define os cabeçalhos HTTP para forçar o navegador a iniciar o download.
        header('Content-Description: File Transfer'); // Descrição do conteúdo.
        header('Content-Type: ' . $mimeType); // Define o tipo de conteúdo do arquivo.
        header('Content-Disposition: attachment; filename="' . $nomeArquivoDownload . '"'); // Força o download com o nome de arquivo especificado.
        header('Expires: 0'); // Impede o cache.
        header('Cache-Control: must-revalidate'); // Força a revalidação do cache.
        header('Pragma: public'); // Para compatibilidade com navegadores mais antigos.
        header('Content-Length: ' . filesize($caminhoCompletoArquivo)); // Informa o tamanho do arquivo.

        // Limpa qualquer saída (output) que tenha sido bufferizada antes de enviar os cabeçalhos.
        // Essencial para evitar corromper o arquivo.
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Lê o arquivo e o envia diretamente para o navegador.
        readfile($caminhoCompletoArquivo);
        // Interrompe a execução do script, pois a resposta já foi enviada.
        exit;
    }
}
