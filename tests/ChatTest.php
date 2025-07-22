<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
require_once __DIR__ . '/../models/Chat.php'; 

// ====================================================================================
// Bloco de Correção para erros de ambiente (CLI)
// Define classes e constantes "dummy" do MySQLi caso a extensão não esteja 
// carregada, permitindo que o PHPUnit crie os mocks sem erros.
// ====================================================================================
if (!class_exists('mysqli')) {
    class mysqli {
        public $insert_id = 1; // Simula um ID de inserção para testes
        public function prepare($query) {}
        public $error;
    }
}
if (!class_exists('mysqli_stmt')) {
    class mysqli_stmt {
        public function bind_param($types, ...$vars) {}
        public function execute() {}
        public function get_result() {}
        public function close() {}
        public $error;
    }
}
if (!class_exists('mysqli_result')) {
    class mysqli_result {
        public function fetch_all($resulttype = 0) {}
        public function fetch_assoc() {}
    }
}
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}
// ====================================================================================


/**
 * Classe de teste para a classe Chat.
 * Utiliza mocks para simular o comportamento do banco de dados (mysqli)
 * e testar cada método da classe Chat de forma isolada.
 */
class ChatTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $chat;

    /**
     * Configuração inicial executada antes de cada teste.
     */
    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->connMock = $this->createMock(mysqli::class);
        $this->chat = new Chat($this->connMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testCriarConversaPrivada()
    {
        $id_origem = 1;
        $id_destino = 2;
        $id_conversa_esperado = 123;

        // Simula a criação da conversa e o retorno do ID
        $this->connMock->insert_id = $id_conversa_esperado;
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $id_conversa_retornado = $this->chat->criarConversaPrivada($id_origem, $id_destino);

        $this->assertEquals($id_conversa_esperado, $id_conversa_retornado);
    }

    public function testAdicionarUsuarioNaConversa()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        // Espera que o método execute seja chamado e retorne true
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        // O método não tem retorno, então apenas verificamos a execução
        $this->chat->adicionarUsuarioNaConversa(1, 1);
    }

    public function testBuscarConversaPrivadaEntreExistente()
    {
        $id1 = 1;
        $id2 = 2;
        $conversaEsperada = ['id_conversa' => 45];

        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($conversaEsperada);

        $resultado = $this->chat->buscarConversaPrivadaEntre($id1, $id2);

        $this->assertEquals($conversaEsperada['id_conversa'], $resultado);
    }

    public function testListarConversasPorUsuario()
    {
        $id_usuario = 1;
        $conversasEsperadas = [
            ['id_conversa' => 1, 'nome_conversa' => 'Grupo A'],
            ['id_conversa' => 2, 'nome_conversa' => 'Grupo B']
        ];

        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($conversasEsperadas);

        $resultado = $this->chat->listarConversasPorUsuario($id_usuario);

        $this->assertEquals($conversasEsperadas, $resultado);
    }

    public function testListarMensagensDaConversa()
    {
        $id_conversa = 1;
        $mensagens = [
            ['id_mensagem' => 10, 'mensagem' => 'Olá!', 'id_usuario' => 1, 'nome_usuario' => 'João', 'foto' => 'joao.jpg'],
            ['id_mensagem' => 11, 'mensagem' => 'Tudo bem?', 'id_usuario' => 2, 'nome_usuario' => 'Maria', 'foto' => 'maria.jpg']
        ];
        $reacoes = [
            10 => [['reacao' => '👍', 'total' => 1, 'nomes_usuarios' => 'Maria']]
        ];

        // Configura o mock para retornar duas vezes (uma para mensagens, outra para reações)
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        
        // Simula o retorno das mensagens
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($mensagens);

        // O método `buscarReacoesParaMensagens` será testado separadamente,
        // então aqui podemos simplificar assumindo que ele funciona.
        // Para um teste mais rigoroso, criaríamos um mock do próprio Chat.
        // Por simplicidade, vamos assumir que a segunda chamada a `prepare` é para as reações.
        // (Esta é uma simplificação. O ideal seria refatorar para injetar dependências).
        
        $resultado = $this->chat->listarMensagensDaConversa($id_conversa);
        
        // Apenas verificamos a estrutura básica, já que as reações são complexas de mockar aqui.
        $this->assertCount(2, $resultado);
        $this->assertArrayHasKey('reacoes', $resultado[0]);
    }

    public function testEnviarMensagem()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->chat->enviarMensagem(1, 1, 'Teste de mensagem');

        $this->assertTrue($sucesso);
    }
    
    public function testObterDestinatarioDaConversa()
    {
        $id_conversa = 1;
        $id_usuario_atual = 1;
        $id_destinatario_esperado = 2;
        
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['id_usuario' => $id_destinatario_esperado]);
        
        $resultado = $this->chat->obterDestinatarioDaConversa($id_conversa, $id_usuario_atual);
        
        $this->assertEquals($id_destinatario_esperado, $resultado);
    }

    public function testMarcarComoLidas()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute');
        
        $this->chat->marcarComoLidas(1, 1);
    }
    
    public function testAdicionarOuAtualizarReacao()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        
        $sucesso = $this->chat->adicionarOuAtualizarReacao(1, 1, '❤️');
        
        $this->assertTrue($sucesso);
    }
    
    public function testBuscarReacoesParaMensagens()
    {
        $ids_mensagens = [10, 11];
        $reacoesEsperadas = [
            10 => [['id_mensagem' => 10, 'reacao' => '👍', 'total' => 1, 'nomes_usuarios' => 'Maria']]
        ];

        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')
             ->will($this->onConsecutiveCalls(
                 ['id_mensagem' => 10, 'reacao' => '👍', 'total' => 1, 'nomes_usuarios' => 'Maria'],
                 null // Termina o loop while
             ));
             
        $resultado = $this->chat->buscarReacoesParaMensagens($ids_mensagens);
        
        $this->assertEquals($reacoesEsperadas, $resultado);
    }

    // =================================================================
    // Testes de Falha (quando prepare() retorna false)
    // =================================================================

    public function testCriarConversaPrivadaFalhaNoPrepare()
    {
        // Força o prepare a falhar
        $this->connMock->method('prepare')->willReturn(false);
        
        // Espera-se uma exceção ou um erro, dependendo da implementação.
        // Como o código original não trata o 'false', ele vai gerar um erro do PHP.
        // O ideal seria que o método retornasse 'false' ou lançasse uma exceção.
        $this->expectException(Error::class);
        
        $this->chat->criarConversaPrivada(1, 2);
    }
    
    public function testBuscarConversaPrivadaEntreFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $this->expectException(Error::class);
        $this->chat->buscarConversaPrivadaEntre(1, 2);
    }

    public function testListarConversasPorUsuarioFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $this->expectException(Error::class);
        $this->chat->listarConversasPorUsuario(1);
    }
    
    public function testEnviarMensagemFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $this->expectException(Error::class);
        $this->chat->enviarMensagem(1, 1, 'teste');
    }
}
