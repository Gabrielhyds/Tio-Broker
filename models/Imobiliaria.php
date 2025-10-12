<?php

require_once __DIR__ . '/../config/validadores.php';

class Imobiliaria
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $conn;

    // Construtor da classe, que recebe a conexão ao ser instanciada.
    public function __construct($conn)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->conn = $conn;
    }

    /**
     * Cadastra uma nova imobiliária (Pessoa Física ou Jurídica).
     *
     * @param string $nome O nome/razão social.
     * @param string $documento O CPF ou CNPJ.
     * @param string $tipo_pessoa 'F' para Física, 'J' para Jurídica.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     * @throws Exception Lança uma exceção se a query falhar ou o CPF for inválido.
     */
    public function cadastrar($nome, $documento, $tipo_pessoa = 'J')
    {
        $query = "INSERT INTO imobiliaria (nome, cpf, cnpj, tipo_pessoa) VALUES (?, ?, ?, ?)";
        
        $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);
        
        // CORREÇÃO: Inicializa as variáveis como NULL em vez de strings vazias.
        $cpf = null;
        $cnpj = null;

        if ($tipo_pessoa === 'F') {
            // Valida o CPF antes de continuar
            if (!validarCpf($documentoLimpo)) {
                throw new Exception("CPF inválido. Por favor, verifique o número digitado.");
            }
            // Se for Pessoa Física, a variável $cpf recebe o número do documento.
            $cpf = $documentoLimpo;
        } else {
            // Se for Pessoa Jurídica, a variável $cnpj recebe o número do documento.
            $cnpj = $documentoLimpo;
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            throw new Exception("Erro ao preparar a query: " . $this->conn->error);
        }

        // Faz o bind dos 4 parâmetros para a query. O bind_param lida corretamente com valores NULL.
        $stmt->bind_param("ssss", $nome, $cpf, $cnpj, $tipo_pessoa);

        return $stmt->execute();
    }

    /**
     * Conta o total de imobiliárias ativas, com um filtro de busca opcional.
     */
    public function contarTotal($filtro = null)
    {
        // Define a consulta SQL base para contar imobiliárias não deletadas (exclusão lógica).
        $sql = "SELECT COUNT(id_imobiliaria) as total FROM imobiliaria WHERE is_deleted = 0";
        // Inicializa arrays para os parâmetros e seus tipos.
        $params = [];
        $types = "";

        // Se um filtro de busca foi fornecido.
        if (!empty($filtro)) {
            // Adiciona a condição de busca ao SQL (nome, CNPJ ou ID).
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ? OR id_imobiliaria = ?)";
            // Prepara o termo de busca para a cláusula LIKE.
            $searchTerm = "%{$filtro}%";
            // Garante que o filtro numérico seja um inteiro.
            $numericFilter = is_numeric($filtro) ? (int)$filtro : 0;
            // Monta o array de parâmetros para a consulta.
            $params = [$searchTerm, str_replace(['.', '/', '-'], '', $searchTerm), $numericFilter];
            // Define os tipos dos parâmetros (string, string, integer).
            $types = "ssi";
        }

        // Prepara a consulta final.
        $stmt = $this->conn->prepare($sql);
        // Se houver parâmetros, associa-os à consulta.
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        // Executa a consulta.
        $stmt->execute();
        // Obtém o resultado.
        $resultado = $stmt->get_result()->fetch_assoc();
        // Retorna o total encontrado, ou 0 se não houver resultado.
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista as imobiliárias ativas de forma paginada, com filtro opcional.
     */
    public function listarPaginado($pagina_atual, $limite, $filtro = null)
    {
        // Calcula o ponto de partida (offset) para a paginação.
        $offset = ($pagina_atual - 1) * $limite;
        // Consulta base para listar imobiliárias ativas e contar seus usuários também ativos.
        $sql = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria AND u.is_deleted = 0
            WHERE i.is_deleted = 0
        ";
        $params = [];
        $types = '';

        // Adiciona a lógica de filtro de busca, se fornecido.
        if (!empty($filtro)) {
            $sql .= " AND (i.nome LIKE ? OR i.cnpj LIKE ? OR i.id_imobiliaria = ?)";
            $searchTerm = "%{$filtro}%";
            $numericFilter = is_numeric($filtro) ? (int)$filtro : 0;
            $params = [$searchTerm, str_replace(['.', '/', '-'], '', $searchTerm), $numericFilter];
            $types = "ssi";
        }

        // Adiciona o agrupamento, ordenação e limites de paginação à consulta.
        $sql .= " GROUP BY i.id_imobiliaria ORDER BY i.nome ASC LIMIT ? OFFSET ?";
        // Adiciona os parâmetros de limite e offset.
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        // Prepara e executa a consulta final.
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        // Retorna todos os resultados como um array associativo, ou um array vazio.
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * "Exclui" uma imobiliária logicamente, definindo is_deleted = 1.
     */
    public function excluir($id)
    {
        // Prepara um UPDATE para marcar a imobiliária como deletada (soft delete).
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET is_deleted = 1 WHERE id_imobiliaria = ?");
        // Associa o ID da imobiliária.
        $stmt->bind_param("i", $id);
        // Executa e retorna o status do sucesso.
        return $stmt->execute();
    }

    /**
     * Busca uma imobiliária ativa específica pelo seu ID.
     */
    public function buscarPorId($id)
    {
        // Prepara uma consulta para buscar uma imobiliária que não esteja deletada.
        $stmt = $this->conn->prepare("SELECT * FROM imobiliaria WHERE id_imobiliaria = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        // Retorna os dados da imobiliária como um array associativo.
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza os dados de uma imobiliária existente.
     */
    public function atualizar($id, $nome, $documento, $tipo_pessoa)
    {
        if ($tipo_pessoa === 'F') {
            $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cpf = ?, tipo_pessoa = ? WHERE id_imobiliaria = ?");
        } else {
            $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ?, tipo_pessoa = ? WHERE id_imobiliaria = ?");
        }

        $stmt->bind_param("sssi", $nome, $documento, $tipo_pessoa, $id);
        return $stmt->execute();
    }

    /**
     * Verifica se uma imobiliária possui usuários ativos vinculados.
     */
    public function temUsuariosVinculados($id_imobiliaria)
    {
        // Prepara uma consulta para contar usuários ativos vinculados à imobiliária.
        $stmt = $this->conn->prepare("SELECT COUNT(id_usuario) as total FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        // Retorna true se o total for maior que 0, false caso contrário.
        return $result['total'] > 0;
    }

    /**
     * Lista todas as imobiliárias ativas (para preencher selects de formulário).
     */
    public function listarTodas()
    {
        // Prepara uma query simples para buscar ID e nome de imobiliárias ativas.
        $query = "SELECT id_imobiliaria, nome FROM imobiliaria WHERE is_deleted = 0 ORDER BY nome ASC";
        // Executa a query diretamente (é segura, pois não há input do usuário).
        $resultado = $this->conn->query($query);
        // Se a consulta falhar, retorna um array vazio.
        if (!$resultado) return [];
        // Retorna todos os resultados.
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // --- MÉTODOS NOVOS PARA RESTAURAÇÃO E LISTAGEM DE EXCLUÍDOS ---

    /**
     * Restaura uma imobiliária que foi "excluída" logicamente.
     */
    public function restaurar($id)
    {
        // Prepara um UPDATE para reverter a exclusão lógica, definindo is_deleted = 0.
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET is_deleted = 0 WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Conta o total de imobiliárias "excluídas" (lixeira), com filtro opcional.
     */
    public function contarTotalExcluidos($filtro = null)
    {
        // Consulta base para contar imobiliárias marcadas como deletadas.
        $sql = "SELECT COUNT(id_imobiliaria) as total FROM imobiliaria WHERE is_deleted = 1";
        $params = [];
        $types = "";

        // Adiciona a lógica de filtro, se fornecido.
        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        // Prepara e executa a contagem.
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista as imobiliárias "excluídas" (da lixeira) de forma paginada.
     */
    public function listarExcluidosPaginado($pagina_atual, $limite, $filtro = null)
    {
        // Calcula o offset para paginação.
        $offset = ($pagina_atual - 1) * $limite;
        // Consulta base para selecionar imobiliárias marcadas como deletadas.
        $sql = "SELECT * FROM imobiliaria WHERE is_deleted = 1";
        $params = [];
        $types = '';

        // Adiciona a lógica de filtro de busca.
        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        // Adiciona ordenação e limites de paginação.
        $sql .= " ORDER BY nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        // Prepara, executa e retorna os resultados.
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
