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
    protected $chatModel;

    public function __construct(Chat $chatModel) {
        $this->clients = new \SplObjectStorage;
        $this->conversations = [];
        $this->userConnections = [];
        $this->onlineUsers = [];
        $this->chatModel = $chatModel;
        echo "Servidor de Chat inicializado.\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nova conexão! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!isset($data['action'])) return;

        switch ($data['action']) {
            case 'register':       $this->handleRegister($from, $data); break;
            case 'subscribe':      $this->handleSubscribe($from, $data); break;
            case 'message':        $this->handleMessage($from, $data); break;
            case 'typing_start':
            case 'typing_stop':    $this->handleTyping($from, $data, $msg); break;
            case 'reaction':       $this->handleReaction($from, $data); break;
            case 'edit_message':   $this->handleEditMessage($from, $data); break;
            case 'delete_message': $this->handleDeleteMessage($from, $data); break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        if (isset($conn->userId)) {
            $uid = $conn->userId;
            unset($this->userConnections[$uid], $this->onlineUsers[$uid]);
            $this->broadcast(json_encode(['action' => 'user_offline', 'user_id' => $uid]));
            echo "Usuário {$uid} desconectado.\n";
        }
        foreach ($this->conversations as &$clients) {
            unset($clients[$conn->resourceId]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro ({$conn->resourceId}): {$e->getMessage()}\n";
        $conn->close();
    }

    private function handleRegister(ConnectionInterface $conn, array $data) {
        if (!isset($data['id_usuario'])) return;
        $userId = (int)$data['id_usuario'];
        $conn->userId = $userId;
        $this->userConnections[$userId] = $conn;
        $this->onlineUsers[$userId] = true;

        // envia lista de online para quem entrou
        $conn->send(json_encode([
            'action'   => 'online_list',
            'user_ids' => array_keys($this->onlineUsers)
        ]));

        // avisa os outros que este usuário ficou online
        $this->broadcast(json_encode(['action' => 'user_online', 'user_id' => $userId]), $conn);
    }

    private function handleSubscribe(ConnectionInterface $conn, array $data) {
        if (!isset($data['id_conversa'])) return;
        $cid = (int)$data['id_conversa'];
        if (!isset($this->conversations[$cid])) $this->conversations[$cid] = [];
        $this->conversations[$cid][$conn->resourceId] = $conn;
    }

    private function handleMessage(ConnectionInterface $from, array $data) {
        if (!isset($from->userId, $data['id_conversa'], $data['id_usuario'], $data['mensagem'])) return;
        if ((int)$from->userId !== (int)$data['id_usuario']) return;

        $cid = (int)$data['id_conversa'];

        try {
            $newId = $this->chatModel->enviarMensagem($cid, (int)$data['id_usuario'], (string)$data['mensagem']);
            if ($newId) {
                $data['id_mensagem'] = $newId;
                $this->retransmitirParaConversa($cid, json_encode($data), null);
            }
        } catch (\Exception $e) {
            echo "[ERRO DB] salvar mensagem: ".$e->getMessage()."\n";
        }

        // notificação (se destinatário não está com a conversa aberta)
        if (isset($data['id_destino'])) {
            $recipientId = (int)$data['id_destino'];
            if (isset($this->userConnections[$recipientId])) {
                $recipientConn = $this->userConnections[$recipientId];
                $recipientInRoom = isset($this->conversations[$cid][$recipientConn->resourceId]);
                if (!$recipientInRoom) {
                    $recipientConn->send(json_encode([
                        'action' => 'notification',
                        'from_user_id' => (int)$data['id_usuario'],
                        'message_text' => (string)$data['mensagem'],
                        'id_conversa' => $cid
                    ]));
                }
            }
        }
    }

    private function handleTyping(ConnectionInterface $from, array $data, string $msg) {
        if (!isset($from->userId, $data['id_conversa'])) return;
        $this->retransmitirParaConversa((int)$data['id_conversa'], $msg, $from);
    }

    private function handleReaction(ConnectionInterface $from, array $data) {
        if (!isset($from->userId, $data['id_mensagem'], $data['reacao'], $data['id_conversa'])) return;
        try {
            $this->chatModel->adicionarOuAtualizarReacao((int)$data['id_mensagem'], (int)$from->userId, (string)$data['reacao']);
            $this->retransmitirParaConversa((int)$data['id_conversa'], json_encode([
                'action' => 'reaction_update',
                'id_conversa' => (int)$data['id_conversa']
            ]), null);
        } catch (\Exception $e) {
            echo "Erro reação: ".$e->getMessage()."\n";
        }
    }

    private function handleEditMessage(ConnectionInterface $from, array $data) {
        if (!isset($from->userId, $data['id_mensagem'], $data['nova_mensagem'], $data['id_conversa'])) return;
        try {
            if ($this->chatModel->editarMensagem((int)$data['id_mensagem'], (int)$from->userId, (string)$data['nova_mensagem'])) {
                $this->retransmitirParaConversa((int)$data['id_conversa'], json_encode([
                    'action' => 'message_updated',
                    'id_mensagem' => (int)$data['id_mensagem'],
                    'nova_mensagem' => (string)$data['nova_mensagem']
                ]), null);
            }
        } catch (\Exception $e) {
            echo "Erro editar: ".$e->getMessage()."\n";
        }
    }

    private function handleDeleteMessage(ConnectionInterface $from, array $data) {
        if (!isset($from->userId, $data['id_mensagem'], $data['id_conversa'])) return;
        try {
            if ($this->chatModel->apagarMensagem((int)$data['id_mensagem'], (int)$from->userId)) {
                $this->retransmitirParaConversa((int)$data['id_conversa'], json_encode([
                    'action' => 'message_deleted',
                    'id_mensagem' => (int)$data['id_mensagem']
                ]), null);
            }
        } catch (\Exception $e) {
            echo "Erro apagar: ".$e->getMessage()."\n";
        }
    }

    protected function retransmitirParaConversa(int $id_conversa, string $msg, ?ConnectionInterface $remetente) {
        if (!isset($this->conversations[$id_conversa])) return;
        foreach ($this->conversations[$id_conversa] as $client) {
            if ($remetente === null || $remetente !== $client) {
                $client->send($msg);
            }
        }
    }

    protected function broadcast(string $msg, ?ConnectionInterface $except = null) {
        foreach ($this->clients as $client) {
            if ($client !== $except) {
                $client->send($msg);
            }
        }
    }
}
