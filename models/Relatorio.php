<?php
class Relatorio
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Busca os dados de leads no banco com base nos filtros.
     */
    public function gerarRelatorioLeads($filtros)
    {
        // AJUSTE MELHORADO: Lança uma exceção se a conexão for nula.
        // Isso é mais claro do que retornar um array vazio.
        if ($this->connection === null) {
            throw new Exception("Erro no Model 'Relatorio': A conexão com o banco de dados é nula. Verifique o Controller.");
        }

        // NOTA: A tabela no seu SQL de criação é 'leads' (plural)
        // A query SQL usa 'criado_em' e 'atualizado_em' (do seu SQL)
        $sql = "SELECT status_pipeline AS status, COUNT(*) AS total,
                       AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) AS tempo_medio
                FROM leads
                WHERE is_deleted = 0"; // Adicionado para ignorar excluídos
        
        $params = [];
        $types = '';

        if (!empty($filtros['dataInicio'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filtros['dataInicio'];
            $types .= 's';
        }

        if (!empty($filtros['dataFim'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filtros['dataFim'];
            $types .= 's';
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND status_pipeline = ?";
            $params[] = $filtros['status'];
            $types .= 's';
        }
        
        if (!empty($filtros['responsavel'])) {
            $sql .= " AND id_usuario_responsavel = ?";
            $params[] = $filtros['responsavel'];
            $types .= 'i'; // Assumindo que é um ID
        }
        
        if (!empty($filtros['imobiliaria'])) {
            $sql .= " AND id_imobiliaria = ?";
            $params[] = $filtros['imobiliaria'];
            $types .= 'i'; // Assumindo que é um ID
        }

        $sql .= " GROUP BY status_pipeline";

        $stmt = $this->connection->prepare($sql);

        // Adiciona verificação de falha no prepare
        if ($stmt === false) {
             throw new Exception("Erro no Model 'Relatorio': Falha ao preparar a consulta SQL: " . $this->connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }

        $stmt->close();
        return $dados;
    }
}
?>

