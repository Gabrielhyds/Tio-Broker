<?php
class UserModel
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $conn;

    // Construtor da classe, que recebe a conexão ao ser instanciada.
    public function __construct($conn)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->conn = $conn;
    }

    // Busca um usuário no banco de dados pelo seu endereço de e-mail.
    public function findByEmail($email)
    {
        // Prepara uma instrução SQL para selecionar todos os dados de um usuário com base no e-mail.
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");
        // Associa (bind) o parâmetro de e-mail (string 's') à instrução preparada.
        $stmt->bind_param("s", $email);
        // Executa a consulta.
        $stmt->execute();
        // Obtém o conjunto de resultados da consulta.
        $result = $stmt->get_result();
        // Retorna os dados do usuário como um array associativo, ou null se não for encontrado.
        return $result->fetch_assoc();
    }

    // Atualiza a senha de um usuário específico no banco de dados.
    public function updatePassword($user_id, $newPassword)
    {
        // Prepara uma instrução SQL para atualizar o campo 'senha' de um usuário com base no seu ID.
        // ATENÇÃO: O nome da coluna de ID no WHERE é 'id', pode precisar ser 'id_usuario' dependendo do seu banco.
        $stmt = $this->conn->prepare("UPDATE usuario SET senha = ? WHERE id = ?");
        // Associa a nova senha (string 's') e o ID do usuário (inteiro 'i') à instrução.
        $stmt->bind_param("si", $newPassword, $user_id);
        // Executa a instrução e retorna true em caso de sucesso ou false em caso de falha.
        return $stmt->execute();
    }
}
