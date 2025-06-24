<?php

class Cliente
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $db;

    // Construtor da classe, que recebe a conexão com o banco de dados.
    public function __construct($db)
    {
        // Atribui a conexão recebida à propriedade $db da classe.
        $this->db = $db;
    }

    // Método para cadastrar um novo cliente no banco de dados.
    public function cadastrar($dados)
    {
        // Utiliza o operador de coalescência nula para garantir que, se 'foto' não for enviado, seu valor seja null.
        $dados['foto'] = $dados['foto'] ?? null;
        // O mesmo para 'empreendimento'.
        $dados['empreendimento'] = $dados['empreendimento'] ?? null;

        // Converte valores monetários vazios para null para evitar erros no banco de dados.
        $dados['renda'] = !empty($dados['renda']) ? $dados['renda'] : null;
        $dados['entrada'] = !empty($dados['entrada']) ? $dados['entrada'] : null;
        $dados['fgts'] = !empty($dados['fgts']) ? $dados['fgts'] : null;
        $dados['subsidio'] = !empty($dados['subsidio']) ? $dados['subsidio'] : null;

        // Define a instrução SQL para inserir um novo cliente. `NOW()` insere a data e hora atuais.
        $sql = "INSERT INTO cliente (nome, numero, cpf, empreendimento, renda, entrada, fgts, subsidio, foto, tipo_lista, id_usuario, id_imobiliaria, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        // Prepara a consulta SQL para execução, o que ajuda a prevenir injeção de SQL.
        $stmt = $this->db->prepare($sql);
        // Se a preparação da consulta falhar, registra um erro e retorna falso.
        if (!$stmt) {
            error_log("Falha no prepare (cadastrar): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }
        // Associa (bind) os valores do array $dados aos placeholders (?) na consulta SQL.
        // A string "ssssddddssii" especifica os tipos de dados de cada parâmetro (string, double, integer).
        $stmt->bind_param(
            "ssssddddssii",
            $dados['nome'],
            $dados['numero'],
            $dados['cpf'],
            $dados['empreendimento'],
            $dados['renda'],
            $dados['entrada'],
            $dados['fgts'],
            $dados['subsidio'],
            $dados['foto'],
            $dados['tipo_lista'],
            $dados['id_usuario'],
            $dados['id_imobiliaria']
        );
        // Executa a consulta preparada.
        $success = $stmt->execute();
        // Se a execução falhar, registra um erro detalhado.
        if (!$success) {
            error_log("Falha na execução (cadastrar): (" . $stmt->errno . ") " . $stmt->error . " SQL: " . $sql);
        }
        // Fecha o statement para liberar recursos.
        $stmt->close();
        // Retorna true em caso de sucesso ou false em caso de falha.
        return $success;
    }

    // Método para listar todos os clientes.
    public function listar($idImobiliaria = null, $idUsuario = null, $isSuperAdmin = false)
    {
        // Inicializa um array vazio para armazenar os clientes.
        $clientes = [];
        // Define a ordenação padrão dos resultados.
        $orderBy = "ORDER BY c.nome ASC";

        // Define a consulta SQL para selecionar todos os clientes, juntando com as tabelas de usuário e imobiliária para obter nomes.
        $sql = "SELECT c.*, u.nome as nome_corretor, im.nome as nome_imobiliaria
                FROM cliente c 
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario 
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria
                {$orderBy}";

        // Prepara a consulta.
        $stmt = $this->db->prepare($sql);

        // Se a preparação falhar, registra o erro e retorna um array vazio.
        if (!$stmt) {
            error_log("Falha no prepare (listar todos os clientes): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return $clientes;
        }

        // Executa a consulta.
        if (!$stmt->execute()) {
            error_log("Falha na execução (listar todos os clientes): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return $clientes;
        }

        // Obtém o conjunto de resultados.
        $result = $stmt->get_result();

        // Itera sobre cada linha do resultado e a adiciona ao array de clientes.
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        // Fecha o statement.
        $stmt->close();
        // Retorna o array de clientes.
        return $clientes;
    }

    // Busca um cliente específico pelo seu ID.
    public function buscarPorId($idCliente)
    {
        // Define a consulta SQL para buscar um cliente e seus dados relacionados (corretor, imobiliária).
        $sql = "SELECT c.*, u.nome as nome_corretor, u.email as email_corretor, u.telefone as telefone_corretor, 
                       im.nome as nome_imobiliaria
                FROM cliente c
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria
                WHERE c.id_cliente = ?";
        // Prepara a consulta.
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare em buscarPorId: (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return null;
        }
        // Associa o ID do cliente ao placeholder da consulta.
        $stmt->bind_param("i", $idCliente);

        // Executa a consulta.
        if (!$stmt->execute()) {
            error_log("Falha na execução (buscarPorId): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return null;
        }

        // Obtém o resultado.
        $result = $stmt->get_result();
        // Busca a primeira (e única) linha do resultado como um array associativo.
        $cliente = $result->fetch_assoc();
        // Fecha o statement.
        $stmt->close();
        // Retorna os dados do cliente ou null se não for encontrado.
        return $cliente;
    }

    /**
     * Atualiza os dados de um cliente existente no banco de dados.
     * @param int $idCliente O ID do cliente a ser atualizado.
     * @param array $dados Os novos dados do cliente.
     * @return bool True em sucesso, false em falha.
     */
    public function atualizar($idCliente, $dados)
    {
        // Define a instrução SQL para atualizar os dados de um cliente.
        $sql = "UPDATE cliente SET 
                    nome = ?, 
                    numero = ?, 
                    cpf = ?, 
                    empreendimento = ?, 
                    renda = ?, 
                    entrada = ?, 
                    fgts = ?, 
                    subsidio = ?, 
                    foto = ?, 
                    tipo_lista = ?
                WHERE id_cliente = ?";

        // Prepara a consulta.
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (atualizar): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return false;
        }

        // Garante que os valores numéricos opcionais sejam NULL se estiverem vazios.
        $renda = !empty($dados['renda']) ? (float)$dados['renda'] : null;
        $entrada = !empty($dados['entrada']) ? (float)$dados['entrada'] : null;
        $fgts = !empty($dados['fgts']) ? (float)$dados['fgts'] : null;
        $subsidio = !empty($dados['subsidio']) ? (float)$dados['subsidio'] : null;
        $foto = $dados['foto'] ?? null;
        $empreendimento = $dados['empreendimento'] ?? null;

        // Associa os parâmetros à consulta preparada.
        $stmt->bind_param(
            "ssssddddssi", // s: string, d: double, i: integer
            $dados['nome'],
            $dados['numero'],
            $dados['cpf'],
            $empreendimento,
            $renda,
            $entrada,
            $fgts,
            $subsidio,
            $foto,
            $dados['tipo_lista'],
            $idCliente
        );

        // Executa a atualização.
        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (atualizar): (" . $stmt->errno . ") " . $stmt->error);
        }
        // Fecha o statement.
        $stmt->close();
        // Retorna o status do sucesso.
        return $success;
    }

    // Exclui um cliente do banco de dados pelo seu ID.
    public function excluir($idCliente)
    {
        // Define a instrução SQL para deletar um cliente.
        $sql = "DELETE FROM cliente WHERE id_cliente = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare em excluir: (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return false;
        }
        // Associa o ID do cliente à consulta.
        $stmt->bind_param("i", $idCliente);
        // Executa a exclusão.
        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (excluir): (" . $stmt->errno . ") " . $stmt->error);
        }
        // Fecha o statement.
        $stmt->close();
        // Retorna o status do sucesso.
        return $success;
    }

    // Lista todos os clientes, retornando apenas ID e nome (útil para selects de formulário).
    public function listarTodos()
    {
        // Inicializa um array vazio para os clientes.
        $clientes = [];

        // Define a consulta SQL simples.
        $sql = "SELECT id_cliente, nome FROM cliente ORDER BY nome";
        // Executa a consulta diretamente (seguro, pois não há input do usuário).
        $result = $this->db->query($sql);

        // Se a consulta retornou resultados.
        if ($result && $result->num_rows > 0) {
            // Itera sobre os resultados e os adiciona ao array.
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }

        // Retorna o array de clientes.
        return $clientes;
    }
}
