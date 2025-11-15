<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO DE CONFIGURAÇÃO (config.php)
|--------------------------------------------------------------------------
| Responsável por estabelecer a conexão com o banco de dados.
|
| CORREÇÃO: Agora testa múltiplos pares de USUÁRIO e SENHA para
| funcionar tanto em produção quanto em desenvolvimento local (XAMPP).
*/

// Define uma função chamada conectarBanco
function conectarBanco()
{
    // 1. Host: (Sem mudança)
    $host = getenv("DB_HOST") ?: "localhost";

    // 2. Database: (Sem mudança)
    $databasename = "tio_Broker";

    // 3. Credenciais: Lista de pares [usuário, senha] para tentar
    $credenciaisTentativas = [
        // 1ª Tentativa: Produção (via env ou hardcoded)

    
        
        // =================================================================
        // ATENÇÃO AQUI! É AQUI QUE VOCÊ PRECISA MEXER
        // =================================================================
        // 2ª Tentativa: Local (XAMPP/WAMP padrão)
        // O erro "Access denied for user 'root'@'localhost' (using password: NO)"
        // significa que seu XAMPP NÃO aceita 'root' com senha em BRANCO.
        //
        // COLOQUE A SENHA QUE VOCÊ USA PARA ACESSAR O PHPMYADMIN AQUI:
        [
            'user' => "root",
            'pass' => "root" // <-- TROQUE ISSO PELA SUA SENHA DO XAMPP/MYSQL
        ],
        
        // 3ª Tentativa: Local (Outra configuração comum de dev)
        [
            'user' => "root",
            'pass' => "root" // Deixa essa, ele tenta "root" se a de cima falhar
        ],
        [
            'user' => getenv("DB_USER") ?: "tio_broker_user",
            'pass' => getenv("DB_PASS") ?: "dev2025"
        ],
    ];

    // Tenta conectar com cada par de credenciais
    foreach ($credenciaisTentativas as $cred) {
// ... (o resto do arquivo não muda) ...
        try {
            // Desativa erros do mysqli para tratar com try-catch
            mysqli_report(MYSQLI_REPORT_OFF);
            
            // Tenta criar uma nova conexão com o par atual
            $conexao = new mysqli($host, $cred['user'], $cred['pass'], $databasename);

            // Se NÃO deu erro na conexão, ela funcionou
            if (!$conexao->connect_error) {
                // Define o charset para UTF-8
                $conexao->set_charset("utf8mb4");
                // Habilita erros do mysqli como exceções
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                return $conexao;
            }
        } catch (mysqli_sql_exception $e) {
            // Se a senha/usuário estiver errado, $e será lançado.
            // O 'continue' pula para o próximo par da lista.
            continue;
        }
    }

    // Se saiu do loop, nenhuma credencial funcionou.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    die("❌ Falha ao conectar ao banco de dados com todas as tentativas. Verifique as credenciais em config.php.");
}

// Executa a função e armazena a conexão na variável global
$connection = conectarBanco();

?>