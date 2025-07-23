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
     * Cadastra um novo imóvel e seus arquivos associados usando uma transação.
     * Lança uma exceção em caso de erro para que o rollback possa ser acionado.
     */
    public function cadastrar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        // Inicia a transação
        $this->connection->begin_transaction();

        try {
            $query = "INSERT INTO imovel (id_imobiliaria, titulo, descricao, tipo, status, preco, endereco, latitude, longitude)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                throw new Exception("Erro ao preparar a query de cadastro: " . $this->connection->error);
            }

            $stmt->bind_param(
                "issssdssd",
                $dados['id_imobiliaria'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['tipo'],
                $dados['status'],
                $dados['preco'],
                $dados['endereco'],
                $dados['latitude'],
                $dados['longitude']
            );

            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar o cadastro do imóvel: " . $stmt->error);
            }

            $idImovel = $stmt->insert_id;

            // Se chegou até aqui, o imóvel foi inserido. Agora, salvamos os arquivos.
            // Se qualquer um desses métodos lançar uma exceção, o catch abaixo fará o rollback.
            $this->salvarArquivos($idImovel, 'imagem', $imagens);
            $this->salvarArquivos($idImovel, 'video', $videos);
            $this->salvarArquivos($idImovel, 'documento', $documentos);

            // Se tudo correu bem, confirma a transação
            $this->connection->commit();
            
            return $idImovel; // Retorna o ID do imóvel criado

        } catch (Exception $e) {
            // Em caso de qualquer erro, desfaz a transação
            $this->connection->rollback();
            // Lança a exceção novamente para que o código que chamou saiba do erro
            throw $e;
        }
    }

    /**
     * Edita um imóvel existente e adiciona novos arquivos.
     * Lança uma exceção em caso de erro.
     */
    public function editar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        $query = "UPDATE imovel SET id_imobiliaria = ?, titulo = ?, descricao = ?, tipo = ?, status = ?, preco = ?, endereco = ?, latitude = ?, longitude = ? 
                  WHERE id_imovel = ?";

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query de edição: " . $this->connection->error);
        }

        $stmt->bind_param(
            "issssdssdi",
            $dados['id_imobiliaria'],
            $dados['titulo'],
            $dados['descricao'],
            $dados['tipo'],
            $dados['status'],
            $dados['preco'],
            $dados['endereco'],
            $dados['latitude'],
            $dados['longitude'],
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
     * Exclui um imóvel e todos os seus arquivos associados (registros e arquivos físicos)
     * usando uma transação. Lança uma exceção em caso de erro.
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
    }

    /**
     * Exclui um arquivo específico (registro e arquivo físico).
     * Lança uma exceção em caso de erro.
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
            } else {
                throw new Exception("Erro ao excluir o registro do arquivo: " . $stmt->error);
            }
        } else {
            throw new Exception("Arquivo não encontrado no banco de dados.");
        }
    }

     /**
     * Salva os caminhos dos arquivos no banco de dados. Lança exceção em caso de erro.
     * Este método agora faz parte da transação iniciada em `cadastrar`.
     */
    private function salvarArquivos($idImovel, $tipo, $arquivos)
    {
        if (empty($arquivos) || !is_array($arquivos)) {
            return; // Nenhum arquivo para salvar
        }

        $tabela = "imovel_" . $tipo; // Ex: imovel_imagem
        $query = "INSERT INTO $tabela (id_imovel, caminho) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a query para salvar arquivos do tipo '$tipo': " . $this->connection->error);
        }

        foreach ($arquivos as $caminhoRelativo) {
            if (empty($caminhoRelativo)) continue;

            $stmt->bind_param("is", $idImovel, $caminhoRelativo);
            if (!$stmt->execute()) {
                // Se a execução falhar, lança uma exceção que será capturada pelo bloco try/catch em `cadastrar`
                throw new Exception("Erro ao salvar o arquivo '$caminhoRelativo' no banco: " . $stmt->error);
            }
        }
    }


    /**
     * Busca um imóvel pelo seu ID, com tratamento de erros robusto.
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
     * Busca todos os arquivos de um determinado tipo para um imóvel, com tratamento de erros.
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
     * Lista todos os imóveis de uma imobiliária específica, com tratamento de erros e depuração.
     */
    public function buscarPorImobiliaria($id_imobiliaria)
    {
        // Reseta as informações de depuração para esta chamada específica
        $this->debugInfo = [
            'metodo' => 'buscarPorImobiliaria',
            'id_passado' => $id_imobiliaria,
            'query_sql' => '',
            'num_resultados' => 0,
            'erro' => null
        ];

        if (empty($id_imobiliaria) || !is_numeric($id_imobiliaria)) {
            $this->debugInfo['erro'] = 'ID da imobiliária é inválido ou está vazio.';
            return [];
        }

        $query = "SELECT * FROM imovel WHERE id_imobiliaria = ? ORDER BY data_cadastro DESC";
        $this->debugInfo['query_sql'] = $query; // Salva a query para depuração

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            $this->debugInfo['erro'] = "Erro ao preparar a query: " . $this->connection->error;
            throw new Exception($this->debugInfo['erro']);
        }

        $stmt->bind_param("i", $id_imobiliaria);
        if (!$stmt->execute()) {
            $this->debugInfo['erro'] = "Erro ao executar a query: " . $stmt->error;
            throw new Exception($this->debugInfo['erro']);
        }

        $result = $stmt->get_result();
        if (!$result) {
            $this->debugInfo['erro'] = "Erro ao obter o resultado da busca: " . $this->connection->error;
            throw new Exception($this->debugInfo['erro']);
        }

        // Salva o número de linhas encontradas para depuração
        $this->debugInfo['num_resultados'] = $result->num_rows;

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- MÉTODOS AUXILIARES ---

    /**
     * Busca o caminho de um arquivo pelo seu ID de registro.
     */
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

    /**
     * Exclui todos os registros de arquivos de um imóvel. Usado na transação de exclusão.
     */
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
