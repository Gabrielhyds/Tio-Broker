<?php
// models/Empreendimento.php
class Empreendimento
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Métodos para controle de transação
    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }

    /**
     * Lista todos os empreendimentos e busca a primeira imagem de cada um.
     */
    public function listarTodos()
    {
        // Query atualizada para buscar a imagem principal usando uma subquery
        $sql = "
            SELECT 
                e.*, 
                (SELECT caminho FROM empreendimento_imagem WHERE id_empreendimento = e.id_empreendimento ORDER BY id LIMIT 1) AS imagem_principal
            FROM 
                empreendimento e
            WHERE 
                e.is_deleted = 0
            ORDER BY
                e.criado_em DESC
        ";

        $result = $this->connection->query($sql);
        $empreendimentos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $empreendimentos[] = $row;
            }
        }
        return $empreendimentos;
    }

    public function buscarPorId($id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM empreendimento WHERE id_empreendimento = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Cria um novo empreendimento e salva os caminhos das mídias.
     */
    public function criar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        $sql = "
            INSERT INTO empreendimento
            (id_imobiliaria, nome, descricao, categoria, status, responsavel, endereco, cidade, estado, cep, preco_min, preco_max, data_inicio, data_entrega)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->connection->prepare($sql);

        $id_imobiliaria = !empty($dados['id_imobiliaria']) ? $dados['id_imobiliaria'] : null;
        $responsavel = !empty($dados['responsavel']) ? $dados['responsavel'] : null;
        $preco_min = !empty($dados['preco_min']) ? $dados['preco_min'] : null;
        $preco_max = !empty($dados['preco_max']) ? $dados['preco_max'] : null;
        $data_inicio = !empty($dados['data_inicio']) ? $dados['data_inicio'] : null;
        $data_entrega = !empty($dados['data_entrega']) ? $dados['data_entrega'] : null;

        $stmt->bind_param(
            "isssssssssddss",
            $id_imobiliaria,
            $dados['nome'],
            $dados['descricao'],
            $dados['categoria'],
            $dados['status'],
            $responsavel,
            $dados['endereco'],
            $dados['cidade'],
            $dados['estado'],
            $dados['cep'],
            $preco_min,
            $preco_max,
            $data_inicio,
            $data_entrega
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao salvar o empreendimento: " . $stmt->error);
        }
        
        $empreendimentoId = $this->connection->insert_id;

        // Salva os caminhos das mídias
        $this->salvarCaminhosMidia($empreendimentoId, 'imagem', $imagens);
        $this->salvarCaminhosMidia($empreendimentoId, 'video', $videos);
        $this->salvarCaminhosMidia($empreendimentoId, 'documento', $documentos);

        return $empreendimentoId;
    }

    /**
     * Função auxiliar para inserir os caminhos das mídias no banco.
     */
    private function salvarCaminhosMidia($empreendimentoId, $tipo, $caminhos)
    {
        if (empty($caminhos)) {
            return;
        }

        $tabela = "empreendimento_{$tipo}";
        $sql = "INSERT INTO {$tabela} (id_empreendimento, caminho) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);

        foreach ($caminhos as $caminho) {
            $stmt->bind_param("is", $empreendimentoId, $caminho);
            if (!$stmt->execute()) {
                // Se falhar, lança uma exceção para que o rollback seja acionado
                throw new Exception("Erro ao salvar caminho da mídia ({$tipo}): " . $stmt->error);
            }
        }
    }
    public function atualizar($id, $dados) {
        $sql = "
            UPDATE empreendimento SET
                nome = ?, descricao = ?, categoria = ?, status = ?, responsavel = ?,
                endereco = ?, cidade = ?, estado = ?, cep = ?, preco_min = ?,
                preco_max = ?, data_inicio = ?, data_entrega = ?
            WHERE id_empreendimento = ?
        ";

        $stmt = $this->connection->prepare($sql);

        $responsavel = $dados['responsavel'] ?? null;
        $preco_min = !empty($dados['preco_min']) ? (float)$dados['preco_min'] : null;
        $preco_max = !empty($dados['preco_max']) ? (float)$dados['preco_max'] : null;
        $data_inicio = !empty($dados['data_inicio']) ? $dados['data_inicio'] : null;
        $data_entrega = !empty($dados['data_entrega']) ? $dados['data_entrega'] : null;

        $stmt->bind_param(
            "sssssssssddssi",
            $dados['nome'], $dados['descricao'], $dados['categoria'], $dados['status'], $responsavel,
            $dados['endereco'], $dados['cidade'], $dados['estado'], $dados['cep'],
            $preco_min, $preco_max, $data_inicio, $data_entrega,
            $id
        );

        return $stmt->execute();
    }

    public function deletar($id) {
        $sql = "UPDATE empreendimento SET is_deleted = 1 WHERE id_empreendimento = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

