<?php
// Caminho: src/ChatServer.php

namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Chat;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    private $conversations;
    protected $chatModel;

    public function __construct(Chat $chatModel) {
        $this->clients = new \SplObjectStorage;
        $this->conversations = [];
        $this->chatModel = $chatModel;
        echo "Servidor de Chat inicializado com o modelo.\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nova conexão! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (!isset($data['action']) || !isset($data['id_conversa'])) {
            return;
        }
        
        $id_conversa = $data['id_conversa'];

        switch ($data['action']) {
            case 'subscribe':
                $this->conversations[$id_conversa][$from->resourceId] = $from;
                echo "Usuário {$from->resourceId} inscrito na conversa {$id_conversa}\n";
                break;

            case 'message':
                // **CORREÇÃO DE DELAY**: Primeiro, retransmite a mensagem para que chegue instantaneamente.
                $this->retransmitirParaConversa($id_conversa, $msg, $from);

                // Depois, salva a mensagem no banco de dados em segundo plano.
                try {
                    $this->chatModel->enviarMensagem(
                        $id_conversa,
                        $data['id_usuario'],
                        $data['mensagem']
                    );
                } catch (\Exception $e) {
                    // Se houver um erro no banco, ele será logado no terminal do servidor,
                    // mas a mensagem já foi entregue aos utilizadores.
                    echo "Erro ao salvar mensagem: " . $e->getMessage() . "\n";
                    return;
                }
                break;

            // Lida com os eventos de digitação
            case 'typing_start':
            case 'typing_stop':
                // Apenas retransmite o evento para os outros clientes, não salva no banco
                $this->retransmitirParaConversa($id_conversa, $msg, $from);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->conversations as &$clients_na_conversa) {
            unset($clients_na_conversa[$conn->resourceId]);
        }
        echo "Conexão {$conn->resourceId} desconectada.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // Função auxiliar para evitar repetição de código
    protected function retransmitirParaConversa($id_conversa, $msg, $remetente) {
        if (isset($this->conversations[$id_conversa])) {
            foreach ($this->conversations[$id_conversa] as $client) {
                // Não envia a mensagem de volta para quem a enviou
                if ($remetente !== $client) {
                    $client->send($msg);
                }
            }
        }
    }
}
