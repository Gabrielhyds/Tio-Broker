<?php

class DocumentoModel
{
    private $db;

    /**
     * Construtor da classe que recebe a conexão com o banco de dados.
     * @param mysqli $db A instância da conexão MySQLi.
     */
    public function __construct($db)
    {
        // CORREÇÃO: Padronizado o nome do parâmetro para '$db' e mantida a verificação.
        if ($db instanceof mysqli) {
            $this->db = $db;
        } else {
            error_log("DocumentoModel: Conexão inválida fornecida.");
            throw new Exception("Conexão com banco de dados inválida para DocumentoModel.");
        }
    }

    /**
     * Adiciona um novo registro de documento ao banco de dados.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function adicionar($idCliente, $idUsuario, $nomeDocumento, $tipoDocumento, $caminhoArquivo)
    {
        $sql = "INSERT INTO documentos (id_cliente, id_usuario, nome_documento, tipo_documento, caminho_arquivo, data_upload) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::adicionar: " . $this->db->error);
            return false;
        }
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
     * Busca todos os documentos associados a um cliente específico.
     * @param int $idCliente O ID do cliente.
     * @return array Retorna uma lista de documentos.
     */
    public function buscarPorCliente($idCliente)
    {
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
     * @param int $idDocumento O ID do documento.
     * @return array|null Retorna os dados do documento ou null se não for encontrado.
     */
    public function buscarPorId($idDocumento)
    {
        $sql = "SELECT * FROM documentos WHERE id_documento = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("MySQLi prepare() error em DocumentoModel::buscarPorId: " . $this->db->error);
            return null;
        }
        $stmt->bind_param("i", $idDocumento);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $documento = $result->fetch_assoc();
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
     * @param int $idDocumento O ID do documento a ser excluído.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function excluir($idDocumento)
    {
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
