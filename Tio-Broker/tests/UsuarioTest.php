<?php

use PHPUnit\Framework\TestCase;

// Inclui a classe que será testada, ajustando o caminho para a pasta 'models'.
require_once __DIR__ . '/../models/Usuario.php';

// ====================================================================================
// Bloco de Correção e Definições
// ====================================================================================

// Define a constante do MySQLi se não existir (comum em ambiente CLI)
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}

// Define classes "dummy" do MySQLi caso a extensão não esteja carregada,
// permitindo que o PHPUnit crie os mocks sem erros.
if (!class_exists('mysqli')) {
    class mysqli {
        public function prepare($query) {}
        public function query($query) {}
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
        public $num_rows;
    }
}
// ====================================================================================


/**
 * Classe de teste para a classe Usuario.
 * Utiliza mocks para simular a interação com o banco de dados.
 */
class UsuarioTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $usuario;

    /**
     * Configura o ambiente de teste antes de cada método de teste ser executado.
     */
    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->connMock = $this->createMock(mysqli::class);
        $this->usuario = new Usuario($this->connMock);
    }

    protected function tearDown(): void
    {
        unset($this->usuario, $this->connMock, $this->stmtMock, $this->resultMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testCadastrarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $resultado = $this->usuario->cadastrar('Teste', 'teste@email.com', '123', '123', 'senha', 'Admin', 1);
        $this->assertTrue($resultado);
    }

    public function testLoginComSucesso()
    {
        $dadosUsuario = ['id_usuario' => 1, 'nome' => 'Teste', 'email' => 'teste@email.com', 'senha' => md5('senha123')];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->num_rows = 1; // CORREÇÃO: Define a propriedade diretamente
        $this->resultMock->method('fetch_assoc')->willReturn($dadosUsuario);
        $resultado = $this->usuario->login('teste@email.com', 'senha123');
        $this->assertEquals($dadosUsuario, $resultado);
    }
    
    public function testBuscarPorIdComSucesso()
    {
        $dadosUsuario = ['id_usuario' => 1, 'nome' => 'Teste'];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($dadosUsuario);
        $resultado = $this->usuario->buscarPorId(1);
        $this->assertEquals($dadosUsuario, $resultado);
    }

    public function testAtualizarPerfilComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $resultado = $this->usuario->atualizarPerfil(1, 'Novo Nome', 'novo@email.com', '12345', md5('novasenha'), 'foto.jpg');
        $this->assertTrue($resultado);
    }
    
    public function testAtualizarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $resultado = $this->usuario->atualizar(1, 'Nome', 'email@email.com', '123', '123', 'Admin', 1);
        $this->assertTrue($resultado);
    }

    public function testExcluirComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $resultado = $this->usuario->excluir(1);
        $this->assertTrue($resultado);
    }
    
    public function testListarPorImobiliariaComSucesso()
    {
        $listaEsperada = [['id_usuario' => 1, 'nome' => 'Corretor A']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($listaEsperada);
        $resultado = $this->usuario->listarPorImobiliaria(1);
        $this->assertEquals($listaEsperada, $resultado);
    }
    
    public function testListarImobiliariasComUsuariosComSucesso()
    {
        $listaEsperada = [['id_imobiliaria' => 1, 'nome' => 'Imobiliaria X']];
        $this->connMock->method('query')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($listaEsperada);
        $resultado = $this->usuario->listarImobiliariasComUsuarios();
        $this->assertEquals($listaEsperada, $resultado);
    }

    public function testRestaurarComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $resultado = $this->usuario->restaurar(1);
        $this->assertTrue($resultado);
    }

    public function testContarTotalComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(['total' => 10]);
        $resultado = $this->usuario->contarTotal();
        $this->assertEquals(10, $resultado);
    }

    // =================================================================
    // Testes de Falha (quando o prepare retorna false)
    // =================================================================
    // NOTA: Estes testes assumem que a classe Usuario foi/será refatorada
    // para tratar falhas no `prepare` e retornar `false`/`null`, em vez de
    // causar um erro fatal. Testar o comportamento atual (erro fatal) é
    // mais complexo e menos útil para garantir a qualidade do código.

    public function testCadastrarFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->usuario->cadastrar('Teste', 'teste@email.com', '123', '123', '123', 'Admin', 1);
        $this->assertFalse($resultado);
    }
    
    public function testBuscarPorIdFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->usuario->buscarPorId(1);
        $this->assertNull($resultado); // Esperado, pois a cadeia de chamadas em um `false` resulta em null.
    }

    public function testAtualizarPerfilFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->usuario->atualizarPerfil(1, 'Nome', 'email@email.com', '123');
        $this->assertFalse($resultado);
    }

    public function testAtualizarFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->usuario->atualizar(1, 'Nome', 'email@email.com', '123', '123', 'Admin', 1);
        $this->assertFalse($resultado);
    }

    public function testExcluirFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->usuario->excluir(1);
        $this->assertFalse($resultado);
    }
    
    // O teste para login com falha no prepare não foi adicionado porque a classe original
    // usa `die()`, o que interrompe a execução dos testes. A classe deveria ser
    // refatorada para lançar uma exceção ou retornar false para ser testável.
}
