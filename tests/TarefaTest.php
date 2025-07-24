<?php

use PHPUnit\Framework\TestCase;

// Inclui a classe que será testada
require_once __DIR__ . '/../models/Tarefa.php';

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
 * Classe de teste para a classe Tarefa.
 */
class TarefaTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $tarefa;

    /**
     * Configura o ambiente de teste antes de cada método.
     */
    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->connMock = $this->createMock(mysqli::class);
        $this->tarefa = new Tarefa($this->connMock);
    }

    protected function tearDown(): void
    {
        unset($this->tarefa, $this->connMock, $this->stmtMock, $this->resultMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testListarParaSuperAdmin()
    {
        $tarefasEsperadas = [['id_tarefa' => 1, 'descricao' => 'Tarefa Admin']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($tarefasEsperadas);

        $resultado = $this->tarefa->listar(null, 'SuperAdmin');
        $this->assertEquals($tarefasEsperadas, $resultado);
    }

    public function testListarParaImobiliaria()
    {
        $tarefasEsperadas = [['id_tarefa' => 2, 'descricao' => 'Tarefa Imobiliaria 1']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($tarefasEsperadas);

        $resultado = $this->tarefa->listar(1, 'Admin');
        $this->assertEquals($tarefasEsperadas, $resultado);
    }

    public function testBuscarPorIdComSucesso()
    {
        $tarefaEsperada = ['id_tarefa' => 5, 'descricao' => 'Tarefa específica'];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($tarefaEsperada);
        
        $resultado = $this->tarefa->buscarPorId(5);
        $this->assertEquals($tarefaEsperada, $resultado);
    }

    public function testCriarComSucesso()
    {
        $dados = [
            'id_usuario' => 1, 'id_cliente' => 2, 'id_imobiliaria' => 3,
            'descricao' => 'Nova tarefa', 'status' => 'Pendente',
            'prioridade' => 'Alta', 'prazo' => '2025-12-31'
        ];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        
        $resultado = $this->tarefa->criar($dados);
        $this->assertTrue($resultado);
    }

    public function testAtualizarComSucesso()
    {
        $dados = [
            'id_tarefa' => 1, 'id_usuario' => 1, 'id_cliente' => 2, 'id_imobiliaria' => 3,
            'descricao' => 'Tarefa atualizada', 'status' => 'Concluída',
            'prioridade' => 'Baixa', 'prazo' => '2026-01-15'
        ];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $resultado = $this->tarefa->atualizar($dados);
        $this->assertTrue($resultado);
    }

    public function testExcluirComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $resultado = $this->tarefa->excluir(10);
        $this->assertTrue($resultado);
    }

    public function testListarPorPermissaoComSucesso()
    {
        $tarefasEsperadas = [['id_tarefa' => 1, 'descricao' => 'Tarefa para Admin']];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($tarefasEsperadas);

        $resultado = $this->tarefa->listarPorPermissao(1, 1, 'Admin');
        $this->assertEquals($tarefasEsperadas, $resultado);
    }

    // =================================================================
    // Testes de Falha (quando o prepare retorna false)
    // =================================================================

    public function testListarFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->listar(1, 'Admin');
        $this->assertEquals([], $resultado);
    }

    public function testBuscarPorIdFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->buscarPorId(1);
        $this->assertNull($resultado);
    }

    public function testCriarFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->criar([]);
        $this->assertFalse($resultado);
    }

    public function testAtualizarFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->atualizar([]);
        $this->assertFalse($resultado);
    }

    public function testExcluirFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->excluir(1);
        $this->assertFalse($resultado);
    }

    public function testListarPorPermissaoFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->tarefa->listarPorPermissao(1, 1, 'Admin');
        $this->assertEquals([], $resultado);
    }
}