<?php
// Define o caminho do arquivo, geralmente usado para referência.
// models/DocumentoModel.php

// Define a classe DocumentoModel, responsável pela lógica de acesso aos dados dos documentos.
class DocumentoModel
{
    // Declara uma propriedade privada para armazenar a instância da conexão com o banco de dados.
    private $db;

    /**
     * Construtor da classe que recebe a conexão com o banco de dados.
     * @param mysqli $connection A instância da conexão MySQLi.
     */
    public function __construct($connection)
    {
        // Verifica se a conexão fornecida é uma instância válida da classe mysqli.
        if ($connection instanceof mysqli) {
            // Se for válida, atribui a conexão à propriedade $db da classe.
            $this->db = $connection;
        } else {
            // Se a conexão for inválida, registra um erro no log do servidor.
            error_log("DocumentoModel: Conexão inválida fornecida.");
            // Lança uma exceção para interromper a execução, pois o modelo não pode funcionar sem uma conexão.
            throw new Exception("Conexão com banco de dados inválida para DocumentoModel.");
        }
    }

    /**
     * Adiciona um novo registro de documento ao banco de dados.
     *
     * @param int $idCliente ID do cliente ao qual o documento pertence.
     * @param int $idUsuario ID do usuário que realizou o upload.
     * @param string $nomeDocumento Nome original ou fornecido para o documento.
     * @param string $tipoDocumento Categoria do documento (ex: "RG", "Contrato").
     * @param string $caminhoArquivo Caminho onde o arquivo foi salvo no servidor.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function adicionar($idCliente, $idUsuario, $nomeDocumento, $tipoDocumento, $caminhoArquivo)
    {
        // Define a instrução SQL para inserir os dados do novo documento. `NOW()` insere a data/hora atual.
        $sql = "INSERT INTO documentos (id_cliente, id_usuario, nome_documento, tipo_documento, caminho_arquivo, data_upload) 
                VALUES (?, ?, ?, ?, ?, NOW())";

        // Prepara a instrução SQL para execução, o que previne injeção de SQL.
        $stmt = $this->db->prepare($sql);
        // Se a preparação falhar, registra um erro e retorna false.
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::adicionar: " . $this->db->error);
            return false;
        }

        // Associa os parâmetros aos placeholders (?) na instrução SQL. 'i' para integer, 's' para string.
        $stmt->bind_param("iisss", $idCliente, $idUsuario, $nomeDocumento, $tipoDocumento, $caminhoArquivo);

        // Executa a instrução preparada.
        if ($stmt->execute()) {
            // Se a execução for bem-sucedida, fecha o statement.
            $stmt->close();
            // Retorna true para indicar sucesso.
            return true;
        } else {
            // Se a execução falhar, registra o erro.
            error_log("MySQLi execute() error em DocumentoModel::adicionar: " . $stmt->error);
            // Fecha o statement.
            $stmt->close();
            // Retorna false para indicar falha.
            return false;
        }
    }

    /**
     * Busca todos os documentos associados a um cliente específico.
     * Realiza um JOIN com a tabela de usuários para obter o nome de quem fez o upload.
     *
     * @param int $idCliente O ID do cliente.
     * @return array Retorna uma lista de documentos ou um array vazio se nada for encontrado.
     */
    public function buscarPorCliente($idCliente)
    {
        // Define a consulta SQL para selecionar documentos, juntando com a tabela de usuários.
        $sql = "SELECT d.*, u.nome AS nome_usuario_upload 
                FROM documentos d
                LEFT JOIN usuario u ON d.id_usuario = u.id_usuario 
                WHERE d.id_cliente = ?
                ORDER BY d.data_upload DESC";

        // Prepara a consulta SQL.
        $stmt = $this->db->prepare($sql);
        // Se a preparação falhar, registra o erro e retorna um array vazio.
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::buscarPorCliente: " . $this->db->error);
            return [];
        }

        // Associa o ID do cliente ao placeholder da consulta.
        $stmt->bind_param("i", $idCliente);

        // Executa a consulta.
        if ($stmt->execute()) {
            // Obtém o conjunto de resultados.
            $result = $stmt->get_result();
            // Busca todos os resultados e os retorna como um array de arrays associativos.
            $documentos = $result->fetch_all(MYSQLI_ASSOC);
            // Fecha o statement.
            $stmt->close();
            // Retorna a lista de documentos.
            return $documentos;
        } else {
            // Se a execução falhar, registra o erro.
            error_log("MySQLi execute() error em DocumentoModel::buscarPorCliente: " . $stmt->error);
            // Fecha o statement.
            $stmt->close();
            // Retorna um array vazio para indicar falha ou ausência de resultados.
            return [];
        }
    }

    /**
     * Busca um documento específico pelo seu ID.
     *
     * @param int $idDocumento O ID do documento.
     * @return array|null Retorna os dados do documento em um array associativo, ou null se não for encontrado.
     */
    public function buscarPorId($idDocumento)
    {
        // Define a consulta SQL para buscar um único documento pelo seu ID.
        $sql = "SELECT * FROM documentos WHERE id_documento = ?";

        // Prepara a consulta.
        $stmt = $this->db->prepare($sql);
        // Se a preparação falhar, registra o erro e retorna null.
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::buscarPorId: " . $this->db->error);
            return null;
        }

        // Associa o ID do documento ao placeholder.
        $stmt->bind_param("i", $idDocumento);

        // Executa a consulta.
        if ($stmt->execute()) {
            // Obtém o resultado.
            $result = $stmt->get_result();
            // Busca a primeira (e única) linha do resultado.
            $documento = $result->fetch_assoc();
            // Fecha o statement.
            $stmt->close();
            // Retorna os dados do documento (ou null se não houver resultado).
            return $documento;
        } else {
            // Se a execução falhar, registra o erro.
            error_log("MySQLi execute() error em DocumentoModel::buscarPorId: " . $stmt->error);
            // Fecha o statement.
            $stmt->close();
            // Retorna null para indicar falha.
            return null;
        }
    }

    /**
     * Exclui um registro de documento do banco de dados com base no seu ID.
     *
     * @param int $idDocumento O ID do documento a ser excluído.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function excluir($idDocumento)
    {
        // Define a instrução SQL para deletar um documento.
        $sql = "DELETE FROM documentos WHERE id_documento = ?";

        // Prepara a instrução.
        $stmt = $this->db->prepare($sql);
        // Se a preparação falhar, registra o erro e retorna false.
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::excluir: " . $this->db->error);
            return false;
        }

        // Associa o ID do documento ao placeholder.
        $stmt->bind_param("i", $idDocumento);

        // Executa a instrução.
        if ($stmt->execute()) {
            // Se a exclusão for bem-sucedida, fecha o statement.
            $stmt->close();
            // Retorna true.
            return true;
        } else {
            // Se a exclusão falhar, registra o erro.
            error_log("MySQLi execute() error em DocumentoModel::excluir: " . $stmt->error);
            // Fecha o statement.
            $stmt->close();
            // Retorna false.
            return false;
        }
    }
}
