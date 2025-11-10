<?php
/*
|-------------------------------------------------------------------------- 
| ARQUIVO: server.php
|-------------------------------------------------------------------------- 
| Este script inicia o servidor WebSocket do Ratchet integrado ao TioBroker.
| Ele conecta ao banco MySQL dentro do container e instancia o ChatServer.
| 
| Porta WebSocket: 8080
| Uso no frontend: ws://localhost:8080
*/

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\ChatServer;
use App\Models\Chat;

// 1. Autoloader do Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Conexão com o banco de dados (ajustada para ambiente Docker)
function conectarBanco()
{
    $host = "db";                  // serviço MySQL do docker-compose
    $databasename = "tio_broker";  // banco criado no docker-compose
    $username = "root";             // usuário do banco
    $password = "root";             // senha do banco

    mysqli_report(MYSQLI_REPORT_OFF);

    $conexao = new mysqli($host, $username, $password, $databasename);

    if ($conexao->connect_error) {
        die("❌ Falha ao conectar ao banco de dados MySQL ({$conexao->connect_error})\n");
    }

    echo "✅ Conexão MySQL estabelecida com sucesso.\n";
    return $conexao;
}

// 3. Conecta ao banco
$connection = conectarBanco();

// 4. Instancia o modelo e servidor de chat
$chatModel = new Chat($connection);
$chatServer = new ChatServer($chatModel);

// 5. Cria e inicia o servidor WebSocket
$server = IoServer::factory(
    new HttpServer(
        new WsServer($chatServer)
    ),
    8080
);

echo "🚀 Servidor WebSocket Ratchet rodando na porta 8080...\n";
echo "📡 Aguardando conexões...\n";

// 6. Inicia o loop
$server->run();
