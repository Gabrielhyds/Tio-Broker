<?php
// models/DocumentoModel.php

class DocumentoModel {
    private $db; // Sua instância de conexão MySQLi

    /**
     * Construtor que recebe a conexão MySQLi.
     * @param mysqli $connection A instância da conexão MySQLi.
     */
    public function __construct($connection) {
        if ($connection instanceof mysqli) {
            $this->db = $connection;
        } else {
            // Tratar erro se a conexão não for uma instância de mysqli
            error_log("DocumentoModel: Conexão inválida fornecida.");
            throw new Exception("Conexão com banco de dados inválida para DocumentoModel.");
        }
    }

    /**
     * Adiciona um novo registro de documento ao banco de dados.
     *
     * @param int $idCliente ID do cliente.
     * @param int $idUsuario ID do usuário que fez o upload.
     * @param string $nomeDocumento Nome original ou fornecido para o documento.
     * @param string $tipoDocumento Tipo/categoria do documento.
     * @param string $caminhoArquivo Caminho onde o arquivo foi salvo no servidor.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function adicionar($idCliente, $idUsuario, $nomeDocumento, $tipoDocumento, $caminhoArquivo) {
        $sql = "INSERT INTO documentos (id_cliente, id_usuario, nome_documento, tipo_documento, caminho_arquivo, data_upload) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::adicionar: " . $this->db->error);
            return false;
        }

        // 'i' para integer, 's' para string
        $stmt->bind_param("iisss", $idCliente, $idUsuario, $nomeDocumento, $tipoDocumento, $caminhoArquivo);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("MySQLi execute() error em DocumentoModel::adicionar: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Busca todos os documentos associados a um cliente.
     * Faz join com a tabela de usuários para pegar o nome de quem fez o upload.
     *
     * @param int $idCliente ID do cliente.
     * @return array Lista de documentos ou array vazio.
     */
    public function buscarPorCliente($idCliente) {
        // Ajuste o nome da tabela de usuários e a coluna do nome do usuário se necessário
        $sql = "SELECT d.*, u.nome AS nome_usuario_upload 
                FROM documentos d
                LEFT JOIN usuario u ON d.id_usuario = u.id_usuario 
                WHERE d.id_cliente = ?
                ORDER BY d.data_upload DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::buscarPorCliente: " . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $idCliente);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $documentos = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $documentos;
        } else {
            error_log("MySQLi execute() error em DocumentoModel::buscarPorCliente: " . $stmt->error);
            $stmt->close();
            return [];
        }
    }

    /**
     * Busca um documento específico pelo seu ID.
     *
     * @param int $idDocumento ID do documento.
     * @return array|null Dados do documento ou null se não encontrado/erro.
     */
    public function buscarPorId($idDocumento) {
        $sql = "SELECT * FROM documentos WHERE id_documento = ?";
        
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::buscarPorId: " . $this->db->error);
            return null;
        }

        $stmt->bind_param("i", $idDocumento);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $documento = $result->fetch_assoc(); // Retorna um array associativo ou null
            $stmt->close();
            return $documento;
        } else {
            error_log("MySQLi execute() error em DocumentoModel::buscarPorId: " . $stmt->error);
            $stmt->close();
            return null;
        }
    }

    /**
     * Exclui um registro de documento do banco de dados.
     *
     * @param int $idDocumento ID do documento a ser excluído.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function excluir($idDocumento) {
        $sql = "DELETE FROM documentos WHERE id_documento = ?";
        
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::excluir: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("i", $idDocumento);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("MySQLi execute() error em DocumentoModel::excluir: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
}
