<?php
class Imovel
{
    private $conn;

    public function __construct($conexao)
    {
        $this->conn = $conexao;
    }

    /**
     * Cadastra um novo imóvel no banco de dados, associando-o à imobiliária correta.
     */
    public function cadastrar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        // ** AJUSTE **: Adicionado id_imobiliaria na query.
        $query = "INSERT INTO imovel (id_imobiliaria, titulo, descricao, tipo, status, preco, endereco, latitude, longitude)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        // ** AJUSTE **: Adicionado "i" para o id_imobiliaria.
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

        if ($stmt->execute()) {
            $idImovel = $stmt->insert_id;
            $this->salvarArquivos($idImovel, 'imagem', $imagens);
            $this->salvarArquivos($idImovel, 'video', $videos);
            $this->salvarArquivos($idImovel, 'documento', $documentos);
            return true;
        }

        return false;
    }

    /**
     * Edita um imóvel existente.
     */
    public function editar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        // ** AJUSTE **: Adicionado id_imobiliaria na query de update.
        $query = "UPDATE imovel SET id_imobiliaria = ?, titulo = ?, descricao = ?, tipo = ?, status = ?, preco = ?, endereco = ?, latitude = ?, longitude = ? 
                  WHERE id_imovel = ?";

        $stmt = $this->conn->prepare($query);
        // ** AJUSTE **: Adicionado "i" para id_imobiliaria e no final para id_imovel.
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

        if ($stmt->execute()) {
            $this->salvarArquivos($dados['id_imovel'], 'imagem', $imagens);
            $this->salvarArquivos($dados['id_imovel'], 'video', $videos);
            $this->salvarArquivos($dados['id_imovel'], 'documento', $documentos);
            return true;
        }

        return false;
    }

    /**
     * Exclui um imóvel do banco de dados.
     */
    public function excluir($id)
    {
        // Antes de excluir, apaga os arquivos associados para limpeza (opcional, mas recomendado)
        // $this->excluirTodosArquivosDeUmImovel($id);

        $query = "DELETE FROM imovel WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Salva os caminhos dos arquivos no banco de dados.
     */
    private function salvarArquivos($idImovel, $tipo, $arquivos)
    {
        if (empty($arquivos)) return;

        $tabela = "imovel_" . $tipo;
        foreach ($arquivos as $arquivo) {
            $query = "INSERT INTO $tabela (id_imovel, caminho) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("is", $idImovel, $arquivo);
            $stmt->execute();
        }
    }

    /**
     * Exclui um arquivo específico (imagem, vídeo ou documento).
     */
    public function excluirArquivo($tipo, $idArquivo)
    {
        $tiposValidos = ['imagem', 'video', 'documento'];
        if (!in_array($tipo, $tiposValidos)) {
            return false;
        }

        // Opcional: buscar o caminho do arquivo para deletá-lo do servidor
        // $arquivo = $this->buscarCaminhoArquivoPorId($tipo, $idArquivo);
        // if ($arquivo && file_exists(UPLOADS_DIR . $arquivo)) {
        //     unlink(UPLOADS_DIR . $arquivo);
        // }

        $tabela = "imovel_" . $tipo;
        $query = "DELETE FROM $tabela WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idArquivo);
        return $stmt->execute();
    }

    /**
     * Busca um imóvel pelo seu ID.
     */
    public function buscarPorId($id)
    {
        $query = "SELECT * FROM imovel WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Busca todos os arquivos de um determinado tipo para um imóvel.
     */
    public function buscarArquivos($idImovel, $tipo)
    {
        $tabela = "imovel_" . $tipo;
        $query = "SELECT * FROM $tabela WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idImovel);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lista TODOS os imóveis, filtrando por imobiliária (sem paginação).
     * Este método foi adicionado para ser compatível com a sua nova view.
     */
    public function listarTodos($id_imobiliaria_logada, $permissao_usuario)
    {
        $sql = "SELECT * FROM imovel";
        $params = [];
        $types = '';

        if ($permissao_usuario !== 'SuperAdmin') {
            $sql .= " WHERE id_imobiliaria = ?";
            $params[] = $id_imobiliaria_logada;
            $types .= 'i';
        }

        $sql .= " ORDER BY data_cadastro DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista os imóveis de forma paginada, filtrando por imobiliária.
     */
    public function listarPaginado($pagina_atual, $limite, $id_imobiliaria_logada, $permissao_usuario, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "SELECT * FROM imovel";
        $params = [];
        $types = '';
        $whereClauses = [];

        if ($permissao_usuario !== 'SuperAdmin') {
            $whereClauses[] = "id_imobiliaria = ?";
            $params[] = $id_imobiliaria_logada;
            $types .= 'i';
        }

        if (!empty($filtro)) {
            $whereClauses[] = "(titulo LIKE ? OR endereco LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY data_cadastro DESC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Conta o total de imóveis, respeitando a permissão do usuário e o filtro.
     */
    public function contarTotal($id_imobiliaria_logada, $permissao_usuario, $filtro = null)
    {
        $sql = "SELECT COUNT(id_imovel) as total FROM imovel";
        $params = [];
        $types = '';
        $whereClauses = [];

        if ($permissao_usuario !== 'SuperAdmin') {
            $whereClauses[] = "id_imobiliaria = ?";
            $params[] = $id_imobiliaria_logada;
            $types .= 'i';
        }

        if (!empty($filtro)) {
            $whereClauses[] = "(titulo LIKE ? OR endereco LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }
}
