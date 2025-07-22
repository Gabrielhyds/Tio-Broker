<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
// Assumindo que a classe Cliente está em 'app/models/Cliente.php'
require_once __DIR__ . '/../models/Cliente.php'; 

// ====================================================================================
// Bloco de Correção para erros de ambiente (CLI)
// Define classes e constantes "dummy" do MySQLi caso a extensão não esteja 
// carregada, permitindo que o PHPUnit crie os mocks sem erros.
// ====================================================================================
if (!class_exists('mysqli')) {
    class mysqli {
        public $insert_id = 1;
        public $errno = 0;
        public $error = '';
        public function prepare($query) {}
        public function query($query) {}
    }
}
if (!class_exists('mysqli_stmt')) {
    class mysqli_stmt {
        public $errno = 0;
        public $error = '';
        public function bind_param($types, ...$vars) {}
        public function execute() {}
        public function get_result() {}
        public function close() {}
    }
}
if (!class_exists('mysqli_result')) {
    class mysqli_result {
        public $num_rows = 0;
        public function fetch_all($resulttype = 0) {}
        public function fetch_assoc() {}
    }
}
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}
// ====================================================================================


/**
 * Classe de teste para a classe Cliente.
 */
class ClienteTest extends TestCase
{
    private $dbMock;
    private $stmtMock;
    private $resultMock;
    private $cliente;

    /**
     * Configuração inicial executada antes de cada teste.
     */
    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->dbMock = $this->createMock(mysqli::class);
        $this->cliente = new Cliente($this->dbMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testBuscarPorCpfEncontrado()
    {
        $cpf = '123.456.789-00';
        $clienteEsperado = ['id_cliente' => 1, 'cpf' => $cpf];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($clienteEsperado);

        $resultado = $this->cliente->buscarPorCpf($cpf);
        $this->assertEquals($clienteEsperado, $resultado);
    }
    
    public function testBuscarPorCpfNaoEncontrado()
    {
        $cpf = '000.000.000-00';

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null);

        $resultado = $this->cliente->buscarPorCpf($cpf);
        $this->assertNull($resultado);
    }

    public function testCadastrar()
    {
        $dados = [
            'nome' => 'João da Silva', 'numero' => '19999998888', 'cpf' => '111.222.333-44',
            'empreendimento' => 'Residencial Alegria', 'renda' => 3000.00, 'entrada' => 10000.00,
            'fgts' => 5000.00, 'subsidio' => 2000.00, 'foto' => 'foto.jpg', 'tipo_lista' => 'quente',
            'id_usuario' => 1, 'id_imobiliaria' => 1
        ];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->cliente->cadastrar($dados);
        $this->assertTrue($sucesso);
    }

    public function testListar()
    {
        $clientesEsperados = [
            ['id_cliente' => 1, 'nome' => 'Ana'],
            ['id_cliente' => 2, 'nome' => 'Bruno']
        ];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        
        // Simula o loop while
        $this->resultMock->method('fetch_assoc')
             ->will($this->onConsecutiveCalls(
                 ['id_cliente' => 1, 'nome' => 'Ana'],
                 ['id_cliente' => 2, 'nome' => 'Bruno'],
                 null
             ));

        $resultado = $this->cliente->listar();
        $this->assertEquals($clientesEsperados, $resultado);
    }

    public function testBuscarPorId()
    {
        $idCliente = 1;
        $clienteEsperado = ['id_cliente' => $idCliente, 'nome' => 'Carlos'];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($clienteEsperado);

        $resultado = $this->cliente->buscarPorId($idCliente);
        $this->assertEquals($clienteEsperado, $resultado);
    }

    public function testAtualizar()
    {
        $idCliente = 1;
        $dados = [
            'nome' => 'João da Silva Santos', 'numero' => '19999998888', 'cpf' => '111.222.333-44',
            'empreendimento' => 'Residencial Felicidade', 'renda' => 3500.00, 'entrada' => 12000.00,
            'fgts' => 6000.00, 'subsidio' => 1500.00, 'foto' => 'nova_foto.jpg', 'tipo_lista' => 'frio'
        ];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->cliente->atualizar($idCliente, $dados);
        $this->assertTrue($sucesso);
    }

    public function testExcluir()
    {
        $idCliente = 1;
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->cliente->excluir($idCliente);
        $this->assertTrue($sucesso);
    }

    public function testListarTodos()
    {
        $clientesEsperados = [['id_cliente' => 1, 'nome' => 'Cliente Um']];
        $this->resultMock->num_rows = 1;
        $this->resultMock->method('fetch_assoc')
             ->will($this->onConsecutiveCalls($clientesEsperados[0], null));
        $this->dbMock->method('query')->willReturn($this->resultMock);
        
        $resultado = $this->cliente->listarTodos();
        $this->assertEquals($clientesEsperados, $resultado);
    }

    // =================================================================
    // Testes de Falha
    // =================================================================
    
    public function testBuscarPorCpfFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $resultado = $this->cliente->buscarPorCpf('123.456.789-00');
        $this->assertNull($resultado);
    }

    public function testCadastrarFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $sucesso = $this->cliente->cadastrar([]);
        $this->assertFalse($sucesso);
    }

    public function testListarFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $resultado = $this->cliente->listar();
        $this->assertEquals([], $resultado);
    }

    public function testBuscarPorIdFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $resultado = $this->cliente->buscarPorId(1);
        $this->assertNull($resultado);
    }

    public function testAtualizarFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $sucesso = $this->cliente->atualizar(1, []);
        $this->assertFalse($sucesso);
    }

    public function testExcluirFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $sucesso = $this->cliente->excluir(1);
        $this->assertFalse($sucesso);
    }
    
    public function testListarTodosFalhaNaQuery()
    {
        $this->dbMock->method('query')->willReturn(false);
        $resultado = $this->cliente->listarTodos();
        $this->assertEquals([], $resultado);
    }

    // --- NOVOS TESTES: Falha na execução ---

    public function testListarFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha no execute
        $resultado = $this->cliente->listar();
        $this->assertEquals([], $resultado);
    }

    public function testBuscarPorIdFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha no execute
        $resultado = $this->cliente->buscarPorId(1);
        $this->assertNull($resultado);
    }
    
    public function testCadastrarFalhaNaExecucao()
    {
        // CORREÇÃO: Fornece dados mínimos para evitar o erro "Undefined index".
        $dadosMinimos = [
            'nome' => 'Teste', 'numero' => '123', 'cpf' => '123', 'tipo_lista' => 'frio',
            'id_usuario' => 1, 'id_imobiliaria' => 1, 'empreendimento' => null,
            'renda' => null, 'entrada' => null, 'fgts' => null, 'subsidio' => null, 'foto' => null
        ];
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha no execute
        $sucesso = $this->cliente->cadastrar($dadosMinimos);
        $this->assertFalse($sucesso);
    }

    public function testAtualizarFalhaNaExecucao()
    {
        // CORREÇÃO: Fornece dados mínimos para evitar o erro "Undefined index".
        $dadosMinimos = [
            'nome' => 'Teste', 'numero' => '123', 'cpf' => '123', 'tipo_lista' => 'frio',
            'empreendimento' => null, 'renda' => null, 'entrada' => null, 'fgts' => null, 
            'subsidio' => null, 'foto' => null
        ];
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha no execute
        $sucesso = $this->cliente->atualizar(1, $dadosMinimos);
        $this->assertFalse($sucesso);
    }

    public function testExcluirFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false); // Simula falha no execute
        $sucesso = $this->cliente->excluir(1);
        $this->assertFalse($sucesso);
    }
}
