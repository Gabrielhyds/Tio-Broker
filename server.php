<?php
// Salve este arquivo como: server.php (na raiz do seu projeto TIO-BROKER/)

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\ChatServer;
use App\Models\Chat;

// 1. Carrega o autoloader do Composer. Ele é responsável por encontrar todas as classes.
require __DIR__ . '/vendor/autoload.php';

// 2. Lógica de conexão com o banco de dados integrada
function conectarBanco()
{
    $host = "localhost";
    $databasename = "tio_broker";
    $username = "root";
    $senhaTentativas = ["", "root"];

    foreach ($senhaTentativas as $senha) {
        try {
            // Suprime avisos de conexão para um tratamento de erro mais limpo
            $conexao = @new mysqli($host, $username, $senha, $databasename);

            if (!$conexao->connect_error) {
                return $conexao;
            }
        } catch (mysqli_sql_exception $e) {
            continue;
        }
    }

    die("❌ Falha ao conectar ao banco de dados. Verifique as credenciais.");
}

// Executa a função para obter a conexão
$connection = conectarBanco();

// 3. Cria a instância do seu Modelo de Chat
$chatModel = new Chat($connection);

// 4. Cria a instância do "Controlador" de Chat
$chatServer = new ChatServer($chatModel);

// 5. Inicia o servidor WebSocket
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $chatServer
        )
    ),
    8080 // A porta em que o servidor irá escutar.
);

echo "Servidor de Chat em tempo real iniciado com sucesso na porta 8080...\n";
echo "Pressione CTRL+C para parar o servidor.\n";

// 6. Inicia o loop do servidor.
$server->run();
