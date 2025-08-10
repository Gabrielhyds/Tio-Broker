<?php
// Caminho: src/ChatServer.php

namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Chat;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    private $conversations;
    private $userConnections; 
    private $onlineUsers;

    public function __construct(Chat $chatModel) {
        $this->clients = new \SplObjectStorage;
        $this->conversations = [];
        $this->userConnections = [];
        $this->onlineUsers = [];
        $this->chatModel = $chatModel;
        echo "Servidor de Chat inicializado com o modelo.\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nova conexão! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!isset($data['action'])) return;

        switch ($data['action']) {
            case 'register':
                $this->handleRegister($from, $data);
                break;
            case 'subscribe':
                $this->handleSubscribe($from, $data);
                break;
            case 'message':
                $this->handleMessage($from, $data, $msg);
                break;
            case 'typing_start':
            case 'typing_stop':
                $this->handleTyping($from, $data, $msg);
                break;
            // **NOVO**: Lida com eventos de reação
            case 'reaction':
                $this->handleReaction($from, $data, $msg);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        if (isset($conn->userId)) {
            $disconnectedUserId = $conn->userId;
            unset($this->userConnections[$disconnectedUserId]);
            unset($this->onlineUsers[$disconnectedUserId]);
            $this->broadcast(json_encode(['action' => 'user_offline', 'user_id' => $disconnectedUserId]));
            echo "Utilizador {$disconnectedUserId} desconectado.\n";
        }
        foreach ($this->conversations as &$clients_na_conversa) {
            unset($clients_na_conversa[$conn->resourceId]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // --- MÉTODOS PRIVADOS ---

    private function handleRegister(ConnectionInterface $conn, array $data) { /* ... */ }
    private function handleSubscribe(ConnectionInterface $conn, array $data) { /* ... */ }
    private function handleMessage(ConnectionInterface $from, array $data, string $msg) { /* ... */ }
    private function handleTyping(ConnectionInterface $from, array $data, string $msg) { /* ... */ }

    // **NOVO**: Método para lidar com reações
    private function handleReaction(ConnectionInterface $from, array $data, string $msg) {
        if (!isset($from->userId) || !isset($data['id_mensagem']) || !isset($data['reacao']) || !isset($data['id_conversa'])) {
            return;
        }

        try {
            $this->chatModel->adicionarOuAtualizarReacao($data['id_mensagem'], $from->userId, $data['reacao']);
        } catch (\Exception $e) {
            echo "Erro ao salvar reação: " . $e->getMessage() . "\n";
            return;
        }

        // Retransmite o evento para todos na conversa para que as UIs sejam atualizadas.
        $this->retransmitirParaConversa($data['id_conversa'], json_encode([
            'action' => 'reaction_update',
            'id_conversa' => $data['id_conversa']
        ]), null); // Envia para todos, incluindo quem reagiu
    }

    protected function retransmitirParaConversa($id_conversa, $msg, $remetente) {
        if (isset($this->conversations[$id_conversa])) {
            foreach ($this->conversations[$id_conversa] as $client) {
                if ($remetente !== $client) {
                    $client->send($msg);
                }
            }
        }
    }

    protected function broadcast($msg, $except = null) {
        foreach ($this->clients as $client) {
            if ($client !== $except) {
                $client->send($msg);
            }
        }
    }
}
