<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
require_once __DIR__ . '/../models/Imobiliaria.php'; 

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
 * Classe de teste para a classe Imobiliaria.
 */
class ImobiliariaTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $imobiliaria;

    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->connMock = $this->createMock(mysqli::class);
        $this->imobiliaria = new Imobiliaria($this->connMock);
    }

    public function testCadastrarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($this->imobiliaria->cadastrar('Imobiliária Teste', '12.345.678/0001-99'));
    }

    public function testContarTotal()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['total' => 5]);
        $this->assertEquals(5, $this->imobiliaria->contarTotal());
    }

    public function testListarPaginado()
    {
        $imobiliariasEsperadas = [['id_imobiliaria' => 1, 'nome' => 'Imob A'], ['id_imobiliaria' => 2, 'nome' => 'Imob B']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($imobiliariasEsperadas);
        $this->assertEquals($imobiliariasEsperadas, $this->imobiliaria->listarPaginado(1, 10));
    }

    public function testExcluirComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($this->imobiliaria->excluir(1));
    }

    public function testBuscarPorId()
    {
        $imobiliariaEsperada = ['id_imobiliaria' => 1, 'nome' => 'Imobiliária Teste'];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($imobiliariaEsperada);
        $this->assertEquals($imobiliariaEsperada, $this->imobiliaria->buscarPorId(1));
    }

    public function testAtualizarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($this->imobiliaria->atualizar(1, 'Novo Nome', '99.888.777/0001-66'));
    }

    public function testTemUsuariosVinculadosTrue()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['total' => 3]);
        $this->assertTrue($this->imobiliaria->temUsuariosVinculados(1));
    }

    public function testTemUsuariosVinculadosFalse()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['total' => 0]);
        $this->assertFalse($this->imobiliaria->temUsuariosVinculados(1));
    }

    public function testListarTodas()
    {
        $imobiliariasEsperadas = [['id_imobiliaria' => 1, 'nome' => 'Imob A']];
        $this->connMock->method('query')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($imobiliariasEsperadas);
        $this->assertEquals($imobiliariasEsperadas, $this->imobiliaria->listarTodas());
    }

    public function testListarTodasComFalhaNaQuery()
    {
        $this->connMock->method('query')->willReturn(false);
        $this->assertEquals([], $this->imobiliaria->listarTodas());
    }
    
    public function testRestaurarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($this->imobiliaria->restaurar(1));
    }

    public function testContarTotalExcluidos()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['total' => 2]);
        $this->assertEquals(2, $this->imobiliaria->contarTotalExcluidos());
    }

    public function testListarExcluidosPaginado()
    {
        $excluidosEsperados = [['id_imobiliaria' => 3, 'nome' => 'Imob C (Excluída)']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($excluidosEsperados);
        $this->assertEquals($excluidosEsperados, $this->imobiliaria->listarExcluidosPaginado(1, 10));
    }

    // Testes de Falha (prepare retorna false ou execute retorna false)
    // A classe Imobiliaria não verifica o retorno de prepare, então um erro seria lançado.
    // O teste espera por esse erro para passar.

    public function testCadastrarFalhaNoPrepare()
    {
        $this->expectException(Error::class);
        $this->connMock->method('prepare')->willReturn(false);
        $this->imobiliaria->cadastrar('Falha', '00.000');
    }

    public function testAtualizarFalhaNoPrepare()
    {
        $this->expectException(Error::class);
        $this->connMock->method('prepare')->willReturn(false);
        $this->imobiliaria->atualizar(1, 'Falha', '00.000');
    }

    public function testExcluirFalhaNoPrepare()
    {
        $this->expectException(Error::class);
        $this->connMock->method('prepare')->willReturn(false);
        $this->imobiliaria->excluir(1);
    }
}
