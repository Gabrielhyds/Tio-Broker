<?php
class Empreendimento {
    private $connection;
    // Define o caminho absoluto para a pasta de uploads.
    // __DIR__ é a pasta do arquivo atual (models)
    // /../../ sobe dois níveis (para a raiz do projeto)
    // /uploads/empreendimentos/ é a pasta de destino final
    private $uploadDir = __DIR__ . '/../../uploads/empreendimentos/';


    public function __construct($connection) {
        $this->connection = $connection; // mysqli

        // VERIFICAÇÃO E CRIAÇÃO DA PASTA
        // Verifica se o diretório de uploads já não existe
        if (!is_dir($this->uploadDir)) {
            // Se não existir, tenta criar a pasta recursivamente (o 'true' permite criar subpastas como 'uploads' e 'empreendimentos')
            // O @ suprime o warning padrão do PHP, pois vamos tratar o erro com uma mensagem mais clara.
            if (!@mkdir($this->uploadDir, 0777, true)) {
                // Se a criação da pasta falhar, o script é interrompido com uma mensagem de erro útil.
                // A causa mais comum para esta falha é a falta de permissão de escrita para o servidor web (Apache).
                die("Erro crítico: Não foi possível criar a pasta de uploads em '{$this->uploadDir}'. Verifique as permissões de escrita do servidor na pasta raiz do seu projeto.");
            }
        }
    }

    public function listarTodos() {
        $result = $this->connection->query("SELECT * FROM empreendimento WHERE is_deleted = 0");
        $empreendimentos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $empreendimentos[] = $row;
            }
        }
        return $empreendimentos;
    }

    public function buscarPorId($id) {
        $stmt = $this->connection->prepare("SELECT * FROM empreendimento WHERE id_empreendimento = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function criar($dados) {
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
        
        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }
    
    public function salvarMidias($empreendimentoId, $arquivos) {
        if (isset($arquivos['imagens']) && is_array($arquivos['imagens']['name'])) {
            $this->salvarArquivo($empreendimentoId, $arquivos['imagens'], 'imagem');
        }
        if (isset($arquivos['videos']) && is_array($arquivos['videos']['name'])) {
            $this->salvarArquivo($empreendimentoId, $arquivos['videos'], 'video');
        }
        if (isset($arquivos['documentos']) && is_array($arquivos['documentos']['name'])) {
            $this->salvarArquivo($empreendimentoId, $arquivos['documentos'], 'documento');
        }
    }

    private function salvarArquivo($empreendimentoId, $arquivos, $tipo) {
        $tabela = "empreendimento_{$tipo}";
        $sql = "INSERT INTO {$tabela} (id_empreendimento, caminho) VALUES (?, ?)";
        
        $total_files = count($arquivos['name']);

        for ($i = 0; $i < $total_files; $i++) {
            if ($arquivos['error'][$i] === UPLOAD_ERR_OK) {
                $nomeArquivo = uniqid() . '-' . basename($arquivos['name'][$i]);
                $caminhoDestino = $this->uploadDir . $nomeArquivo;
                $caminhoRelativo = 'uploads/empreendimentos/' . $nomeArquivo;

                if (move_uploaded_file($arquivos['tmp_name'][$i], $caminhoDestino)) {
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bind_param("is", $empreendimentoId, $caminhoRelativo);
                    $stmt->execute();
                }
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
        $preco_min = !empty($dados['preco_min']) ? $dados['preco_min'] : null;
        $preco_max = !empty($dados['preco_max']) ? $dados['preco_max'] : null;
        $data_inicio = !empty($dados['data_inicio']) ? $dados['data_inicio'] : null;
        $data_entrega = !empty($dados['data_entrega']) ? $dados['data_entrega'] : null;

        $stmt->bind_param(
            "sssssssssddssi",
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
            $data_entrega,
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

