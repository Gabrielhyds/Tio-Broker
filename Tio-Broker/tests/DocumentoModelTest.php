<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
require_once __DIR__ . '/../models/DocumentoModel.php'; 

// ====================================================================================
// Bloco de Correção para erros de ambiente (CLI)
// Define classes e constantes "dummy" do MySQLi caso a extensão não esteja 
// carregada, permitindo que o PHPUnit crie os mocks sem erros.
// ====================================================================================
if (!class_exists('mysqli')) {
    class mysqli {
        public $errno = 0;
        public $error = '';
        public function prepare($query) {}
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
        public function fetch_all($resulttype = 0) {}
        public function fetch_assoc() {}
    }
}
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}
// ====================================================================================


/**
 * Classe de teste para a classe DocumentoModel.
 */
class DocumentoModelTest extends TestCase
{
    private $dbMock;
    private $stmtMock;
    private $resultMock;
    private $documentoModel;

    /**
     * Configuração inicial executada antes de cada teste.
     */
    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->dbMock = $this->createMock(mysqli::class);
        $this->documentoModel = new DocumentoModel($this->dbMock);
    }

    public function testConstrutorComConexaoInvalidaDeveLancarExcecao()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Conexão com banco de dados inválida para DocumentoModel.");
        new DocumentoModel(new stdClass()); // Passa um objeto inválido
    }

    // =================================================================
    // Testes para o método adicionar
    // =================================================================

    public function testAdicionarComSucesso()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->documentoModel->adicionar(1, 1, 'RG.pdf', 'RG', '/path/to/rg.pdf');
        $this->assertTrue($sucesso);
    }

    public function testAdicionarFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $sucesso = $this->documentoModel->adicionar(1, 1, 'RG.pdf', 'RG', '/path/to/rg.pdf');
        $this->assertFalse($sucesso);
    }

    public function testAdicionarFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $sucesso = $this->documentoModel->adicionar(1, 1, 'RG.pdf', 'RG', '/path/to/rg.pdf');
        $this->assertFalse($sucesso);
    }

    // =================================================================
    // Testes para o método buscarPorCliente
    // =================================================================

    public function testBuscarPorClienteComSucesso()
    {
        $idCliente = 1;
        $documentosEsperados = [
            ['id_documento' => 1, 'nome_documento' => 'RG.pdf'],
            ['id_documento' => 2, 'nome_documento' => 'CPF.pdf']
        ];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($documentosEsperados);

        $resultado = $this->documentoModel->buscarPorCliente($idCliente);
        $this->assertEquals($documentosEsperados, $resultado);
    }

    public function testBuscarPorClienteSemResultados()
    {
        $idCliente = 1;
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn([]);

        $resultado = $this->documentoModel->buscarPorCliente($idCliente);
        $this->assertEquals([], $resultado);
    }
    
    public function testBuscarPorClienteFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $resultado = $this->documentoModel->buscarPorCliente(1);
        $this->assertEquals([], $resultado);
    }

    public function testBuscarPorClienteFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $resultado = $this->documentoModel->buscarPorCliente(1);
        $this->assertEquals([], $resultado);
    }

    // =================================================================
    // Testes para o método buscarPorId
    // =================================================================

    public function testBuscarPorIdComSucesso()
    {
        $idDocumento = 1;
        $documentoEsperado = ['id_documento' => $idDocumento, 'nome_documento' => 'Contrato.pdf'];

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($documentoEsperado);

        $resultado = $this->documentoModel->buscarPorId($idDocumento);
        $this->assertEquals($documentoEsperado, $resultado);
    }
    
    public function testBuscarPorIdNaoEncontrado()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null);

        $resultado = $this->documentoModel->buscarPorId(999);
        $this->assertNull($resultado);
    }

    public function testBuscarPorIdFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $resultado = $this->documentoModel->buscarPorId(1);
        $this->assertNull($resultado);
    }
    
    public function testBuscarPorIdFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $resultado = $this->documentoModel->buscarPorId(1);
        $this->assertNull($resultado);
    }

    // =================================================================
    // Testes para o método excluir
    // =================================================================

    public function testExcluirComSucesso()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->documentoModel->excluir(1);
        $this->assertTrue($sucesso);
    }

    public function testExcluirFalhaNoPrepare()
    {
        $this->dbMock->method('prepare')->willReturn(false);
        $sucesso = $this->documentoModel->excluir(1);
        $this->assertFalse($sucesso);
    }

    public function testExcluirFalhaNaExecucao()
    {
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $sucesso = $this->documentoModel->excluir(1);
        $this->assertFalse($sucesso);
    }
}
