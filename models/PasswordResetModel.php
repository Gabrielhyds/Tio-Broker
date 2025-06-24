<?php
class PasswordResetModel
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $conn;

    // Construtor da classe, que recebe a conexão ao ser instanciada.
    public function __construct($conn)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->conn = $conn;
    }

    // Cria um novo token de redefinição de senha no banco de dados.
    public function createToken($user_id, $token)
    {
        // Prepara uma instrução SQL para inserir o ID do usuário, o token e a data/hora atual.
        $stmt = $this->conn->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW())");
        // Associa (bind) o ID do usuário (inteiro 'i') e o token (string 's') à instrução.
        $stmt->bind_param("is", $user_id, $token);
        // Executa a instrução e retorna true em caso de sucesso ou false em caso de falha.
        return $stmt->execute();
    }

    // Busca e valida um token no banco de dados.
    public function getToken($token)
    {
        // Define a SQL para buscar um token que seja válido e tenha sido criado na última hora.
        $sql = "SELECT * FROM password_resets WHERE token = ? AND created_at >= (NOW() - INTERVAL 1 HOUR)";
        // Prepara a consulta.
        $stmt = $this->conn->prepare($sql);
        // Associa o token (string 's') à consulta.
        $stmt->bind_param("s", $token);
        // Executa a consulta.
        $stmt->execute();
        // Obtém o conjunto de resultados.
        $result = $stmt->get_result();
        // Retorna os dados do token como um array associativo, ou null se não for encontrado ou tiver expirado.
        return $result->fetch_assoc();
    }

    // Deleta um token do banco de dados após ele ter sido usado.
    public function deleteToken($token)
    {
        // Prepara uma instrução SQL para deletar o registro do token.
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE token = ?");
        // Associa o token (string 's') à instrução.
        $stmt->bind_param("s", $token);
        // Executa a instrução de exclusão.
        $stmt->execute();
    }
}
