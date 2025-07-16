<?php
class ConfiguracaoDAO
{
    private $conexao;

    // O construtor agora recebe uma conexão MySQLi.
    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    /**
     * Salva ou atualiza as configurações de um usuário usando MySQLi.
     * Utiliza a sintaxe "INSERT ... ON DUPLICATE KEY UPDATE" do MySQL (UPSERT).
     *
     * @param int $idUsuario O ID do usuário.
     * @param array $configuracoes Um array associativo com as configurações.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function salvarConfiguracoes(int $idUsuario, array $configuracoes): bool
    {
        // Converte o array de configurações para uma string JSON.
        $configuracoesJson = json_encode($configuracoes);

        // A query SQL para inserir ou atualizar (UPSERT) permanece a mesma.
        $sql = "INSERT INTO configuracoes (id_usuario, dados_json) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE dados_json = ?";

        // Prepara a declaração.
        $stmt = $this->conexao->prepare($sql);
        if ($stmt === false) {
            // Em caso de erro na preparação, você pode logar o erro.
            // error_log("Erro ao preparar a query: " . $this->conexao->error);
            return false;
        }

        // Associa os parâmetros ('i' para integer, 's' para string).
        // Note que dados_json é passado duas vezes para a query.
        $stmt->bind_param('iss', $idUsuario, $configuracoesJson, $configuracoesJson);

        // Executa a query.
        $sucesso = $stmt->execute();

        // Fecha a declaração.
        $stmt->close();

        return $sucesso;
    }

    /**
     * Busca as configurações de um usuário no banco usando MySQLi.
     *
     * @param int $idUsuario O ID do usuário.
     * @return array|null Retorna um array com as configurações ou null se não encontrar.
     */
    public function buscarConfiguracoes(int $idUsuario): ?array
    {
        $sql = "SELECT dados_json FROM configuracoes WHERE id_usuario = ?";

        // Prepara a declaração.
        $stmt = $this->conexao->prepare($sql);
        if ($stmt === false) {
            // error_log("Erro ao preparar a query de busca: " . $this->conexao->error);
            return null;
        }

        // Associa o parâmetro.
        $stmt->bind_param('i', $idUsuario);

        // Executa a query.
        $stmt->execute();

        // Obtém o resultado.
        $resultadoQuery = $stmt->get_result();

        // Busca a linha como um array associativo.
        $resultado = $resultadoQuery->fetch_assoc();

        // Fecha a declaração.
        $stmt->close();

        if ($resultado && isset($resultado['dados_json'])) {
            // Decodifica a string JSON de volta para um array PHP.
            return json_decode($resultado['dados_json'], true);
        }

        return null; // Retorna null se não encontrar configurações para o usuário.
    }
}
