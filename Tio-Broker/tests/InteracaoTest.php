<?php

use PHPUnit\Framework\TestCase;

// Inclui a classe que será testada
require_once __DIR__ . '/../models/Interacao.php';

// ====================================================================================
// Bloco de Correção e Definições
// ====================================================================================
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}
if (!class_exists('mysqli')) {
    class mysqli {
        public function prepare($query) {}
        public function query($query) {}
        public $error;
        public $errno;
    }
}
if (!class_exists('mysqli_stmt')) {
    class mysqli_stmt {
        public function bind_param($types, ...$vars) {}
        public function execute() {}
        public function get_result() {}
        public function close() {}
        public $error;
        public $errno;
    }
}
if (!class_exists('mysqli_result')) {
    class mysqli_result {
        public function fetch_all($resulttype = 0) {}
        public function fetch_assoc() {}
        public $num_rows;
    }
}
// ====================================================================================


/**
 * Classe de teste para a classe Interacao.
 */
class InteracaoTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $interacaoModel;

    /**
     * Configura o ambiente de teste antes de cada método.
     */
    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->connMock = $this->createMock(mysqli::class);
        // A classe Interacao será instanciada em cada teste para lidar com o construtor.
    }

    protected function tearDown(): void
    {
        unset($this->interacaoModel, $this->connMock, $this->stmtMock, $this->resultMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testConstrutorComConexaoValida()
    {
        // Instancia o modelo com o mock válido. Nenhuma exceção deve ser lançada.
        $this->interacaoModel = new Interacao($this->connMock);
        $this->assertInstanceOf(Interacao::class, $this->interacaoModel);
    }

    public function testCadastrarComSucesso()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $dados = [
            'id_cliente' => 1,
            'id_usuario' => 2,
            'tipo_interacao' => 'Telefone',
            'descricao' => 'Conversa inicial com o cliente.'
        ];

        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $resultado = $this->interacaoModel->cadastrar($dados);
        $this->assertTrue($resultado);
    }

    public function testListarPorClienteComSucesso()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $interacoesEsperadas = [
            ['id_interacao' => 1, 'descricao' => 'Primeiro contato'],
            ['id_interacao' => 2, 'descricao' => 'Visita agendada']
        ];
        
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);

        // Simula o loop do fetch_assoc
        $this->resultMock->method('fetch_assoc')
             ->will($this->onConsecutiveCalls(
                 $interacoesEsperadas[0],
                 $interacoesEsperadas[1],
                 null // Termina o loop
             ));

        $resultado = $this->interacaoModel->listarPorCliente(1);
        $this->assertEquals($interacoesEsperadas, $resultado);
    }
    
    public function testListarPorClienteSemResultados()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null); // Nenhum resultado

        $resultado = $this->interacaoModel->listarPorCliente(99);
        $this->assertEquals([], $resultado);
    }

    // =================================================================
    // Testes de Falha
    // =================================================================

    public function testConstrutorComConexaoInvalida()
    {
        // Espera que uma exceção seja lançada ao passar um objeto inválido.
        $this->expectException(Exception::class);
        $this->interacaoModel = new Interacao(new stdClass()); // Usa um objeto qualquer
    }

    public function testCadastrarFalhaNoPrepare()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->interacaoModel->cadastrar([]);
        $this->assertFalse($resultado);
    }

    public function testListarPorClienteFalhaNoPrepare()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->interacaoModel->listarPorCliente(1);
        $this->assertEquals([], $resultado);
    }
    
    public function testCadastrarFalhaNaExecucao()
    {
        $this->interacaoModel = new Interacao($this->connMock);
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha na execução

        $resultado = $this->interacaoModel->cadastrar(['id_cliente' => 1, 'id_usuario' => 1, 'tipo_interacao' => 't', 'descricao' => 'd']);
        $this->assertFalse($resultado);
    }
}
