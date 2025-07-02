<?php
class Imovel
{
    private $conn;

    public function __construct($conexao)
    {
        $this->conn = $conexao;
    }

    public function listarTodos()
    {
        $query = "SELECT * FROM imovel ORDER BY data_cadastro DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id)
    {
        $query = "SELECT * FROM imovel WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cadastrar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        $query = "INSERT INTO imovel (titulo, descricao, tipo, status, preco, endereco, latitude, longitude)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssssdssd",
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

    public function editar($dados, $imagens = [], $videos = [], $documentos = [])
    {
        $tiposValidos = ['venda', 'locacao', 'temporada', 'lancamento'];
        $statusesValidos = ['disponivel', 'reservado', 'vendido', 'indisponivel'];

        if (!in_array($dados['tipo'], $tiposValidos)) {
            throw new Exception("Tipo inválido: " . $dados['tipo']);
        }
        if (!in_array($dados['status'], $statusesValidos)) {
            throw new Exception("Status inválido: " . $dados['status']);
        }

        $query = "UPDATE imovel SET titulo = ?, descricao = ?, tipo = ?, status = ?, preco = ?, endereco = ?, latitude = ?, longitude = ? 
                  WHERE id_imovel = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssssdssdi",
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

    public function excluir($id)
    {
        $query = "DELETE FROM imovel WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

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

    public function buscarArquivos($idImovel, $tipo)
    {
        $tabela = "imovel_" . $tipo;
        $query = "SELECT * FROM $tabela WHERE id_imovel = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idImovel);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function excluirArquivo($tipo, $idArquivo)
    {
        $tiposValidos = ['imagem', 'video', 'documento'];
        if (!in_array($tipo, $tiposValidos)) {
            throw new Exception("Tipo de arquivo inválido para exclusão.");
        }

        $tabela = "imovel_" . $tipo;
        $query = "DELETE FROM $tabela WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idArquivo);
        return $stmt->execute();
    }
}
