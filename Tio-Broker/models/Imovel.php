<?php

class Imovel
{
    /**
     * @var mysqli A conexão com o banco de dados.
     */
    public $connection;

    /**
     * @var array Armazena informações de depuração para a última operação.
     */
    public $debugInfo = [];

    public function __construct($conexao)
    {
        $this->connection = $conexao;
    }

    /**
     * Cadastra um novo imóvel com a estrutura de endereço refatorada.
     */
    public function cadastrar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        try {
            // ✅ CORREÇÃO: Query atualizada para os novos campos de endereço.
            $query = "INSERT INTO imovel (id_imobiliaria, titulo, descricao, tipo, status, preco, endereco, cep, numero, complemento, bairro, cidade, estado)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Erro ao preparar a query de cadastro: " . $this->connection->error);
            }

            // ✅ CORREÇÃO: bind_param atualizado para corresponder aos novos campos.
            $stmt->bind_param(
                "issssdsssssss",
                $dados['id_imobiliaria'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['tipo'],
                $dados['status'],
                $dados['preco'],
                $dados['endereco'],
                $dados['cep'],
                $dados['numero'],
                $dados['complemento'],
                $dados['bairro'],
                $dados['cidade'],
                $dados['estado']
            );

            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar o cadastro do imóvel: " . $stmt->error);
            }

            $idImovel = $stmt->insert_id;

            $this->salvarArquivos($idImovel, 'imagem', $imagens);
            $this->salvarArquivos($idImovel, 'video', $videos);
            $this->salvarArquivos($idImovel, 'documento', $documentos);
            
            return $idImovel;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edita um imóvel existente com a estrutura de endereço refatorada.
     */
    public function editar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        // ✅ CORREÇÃO: Query atualizada para os novos campos de endereço.
        $query = "UPDATE imovel SET 
                    id_imobiliaria = ?, titulo = ?, descricao = ?, tipo = ?, 
                    status = ?, preco = ?, endereco = ?, cep = ?, numero = ?, 
                    complemento = ?, bairro = ?, cidade = ?, estado = ? 
                  WHERE id_imovel = ?";

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query de edição: " . $this->connection->error);
        }

        // ✅ CORREÇÃO: bind_param atualizado para corresponder aos novos campos.
        $stmt->bind_param(
            "issssdsssssssi",
            $dados['id_imobiliaria'],
            $dados['titulo'],
            $dados['descricao'],
            $dados['tipo'],
            $dados['status'],
            $dados['preco'],
            $dados['endereco'],
            $dados['cep'],
            $dados['numero'],
            $dados['complemento'],
            $dados['bairro'],
            $dados['cidade'],
            $dados['estado'],
            $dados['id_imovel']
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a edição do imóvel: " . $stmt->error);
        }

        $this->salvarArquivos($dados['id_imovel'], 'imagem', $imagens);
        $this->salvarArquivos($dados['id_imovel'], 'video', $videos);
        $this->salvarArquivos($dados['id_imovel'], 'documento', $documentos);
    }

    /**
     * Exclui um imóvel e todos os seus arquivos associados.
     */
    public function excluir($id)
    {
        $imagens = $this->buscarArquivos($id, 'imagem');
        $videos = $this->buscarArquivos($id, 'video');
        $documentos = $this->buscarArquivos($id, 'documento');
        $todosArquivos = array_merge($imagens, $videos, $documentos);

        $this->connection->begin_transaction();
        try {
            $this->excluirRegistrosDeArquivosPorImovelId($id, 'imagem');
            $this->excluirRegistrosDeArquivosPorImovelId($id, 'video');
            $this->excluirRegistrosDeArquivosPorImovelId($id, 'documento');

            $query = "DELETE FROM imovel WHERE id_imovel = ?";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) throw new Exception("Erro ao preparar a query de exclusão do imóvel.");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) throw new Exception("Erro ao excluir o registro do imóvel: " . $stmt->error);

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }

        foreach ($todosArquivos as $arquivo) {
            $caminhoCompleto = UPLOADS_DIR . str_replace('uploads/', '', $arquivo['caminho']);
            if (file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }
        }
        return true;
    }

    /**
     * Exclui um arquivo específico (registro e arquivo físico).
     */
    public function excluirArquivo($tipo, $idArquivo)
    {
        $tiposValidos = ['imagem', 'video', 'documento'];
        if (!in_array($tipo, $tiposValidos)) {
            throw new Exception("Tipo de arquivo inválido.");
        }

        $caminhoRelativo = $this->buscarCaminhoArquivoPorId($tipo, $idArquivo);

        if ($caminhoRelativo) {
            $tabela = "imovel_" . $tipo;
            $query = "DELETE FROM $tabela WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) throw new Exception("Erro ao preparar a query de exclusão de arquivo.");

            $stmt->bind_param("i", $idArquivo);
            if ($stmt->execute()) {
                $caminhoCompleto = UPLOADS_DIR . str_replace('uploads/', '', $caminhoRelativo);
                if (file_exists($caminhoCompleto)) {
                    unlink($caminhoCompleto);
                }
                return true;
            } else {
                throw new Exception("Erro ao excluir o registro do arquivo: " . $stmt->error);
            }
        } else {
            throw new Exception("Arquivo não encontrado no banco de dados.");
        }
    }

    /**
     * Salva os caminhos dos arquivos no banco de dados.
     */
    private function salvarArquivos($idImovel, $tipo, $arquivos)
    {
        if (empty($arquivos) || !is_array($arquivos)) {
            return;
        }

        $tabela = "imovel_" . $tipo;
        $query = "INSERT INTO $tabela (id_imovel, caminho) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query para salvar arquivos do tipo '$tipo': " . $this->connection->error);
        }

        foreach ($arquivos as $caminhoRelativo) {
            if (empty($caminhoRelativo)) continue;

            $stmt->bind_param("is", $idImovel, $caminhoRelativo);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao salvar o arquivo '$caminhoRelativo' no banco: " . $stmt->error);
            }
        }
    }

    /**
     * Busca um imóvel pelo seu ID.
     */
    public function buscarPorId($id)
    {
        if (empty($id) || !is_numeric($id)) return null;

        $query = "SELECT * FROM imovel WHERE id_imovel = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query (buscarPorId): " . $this->connection->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a query (buscarPorId): " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Busca todos os arquivos de um determinado tipo para um imóvel.
     */
    public function buscarArquivos($idImovel, $tipo)
    {
        if (empty($idImovel) || !is_numeric($idImovel)) return [];

        $tabela = "imovel_" . $tipo;
        $query = "SELECT id, caminho FROM $tabela WHERE id_imovel = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query (buscarArquivos): " . $this->connection->error);
        }
        $stmt->bind_param("i", $idImovel);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a query (buscarArquivos): " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lista todos os imóveis de uma imobiliária específica.
     */
    public function buscarPorImobiliaria($id_imobiliaria)
    {
        $this->debugInfo = [];
        if (empty($id_imobiliaria) || !is_numeric($id_imobiliaria)) {
            return [];
        }

        $query = "SELECT * FROM imovel WHERE id_imobiliaria = ? ORDER BY data_cadastro DESC";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query: " . $this->connection->error);
        }

        $stmt->bind_param("i", $id_imobiliaria);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- MÉTODOS AUXILIARES ---

    private function buscarCaminhoArquivoPorId($tipo, $idArquivo)
    {
        if (empty($idArquivo) || !is_numeric($idArquivo)) return null;

        $tabela = "imovel_" . $tipo;
        $query = "SELECT caminho FROM $tabela WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query (buscarCaminhoArquivoPorId): " . $this->connection->error);
        }
        $stmt->bind_param("i", $idArquivo);
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a query (buscarCaminhoArquivoPorId): " . $stmt->error);
        }
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['caminho'] ?? null;
    }

    private function excluirRegistrosDeArquivosPorImovelId($idImovel, $tipo)
    {
        $tabela = "imovel_" . $tipo;
        $query = "DELETE FROM $tabela WHERE id_imovel = ?";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) throw new Exception("Erro ao preparar a query de exclusão de registros de arquivos.");
        $stmt->bind_param("i", $idImovel);
        if (!$stmt->execute()) throw new Exception("Erro ao excluir registros da tabela $tabela: " . $stmt->error);
    }
}
