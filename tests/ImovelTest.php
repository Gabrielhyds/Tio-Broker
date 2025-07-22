<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
require_once __DIR__ . '/../models/Imovel.php'; 

// ====================================================================================
// Bloco de Correção para erros de ambiente (CLI)
// Define classes e constantes "dummy" do MySQLi caso a extensão não esteja 
// carregada, permitindo que o PHPUnit crie os mocks sem erros.
// ====================================================================================
if (!class_exists('mysqli')) {
    class mysqli {
        public $errno = 0;
        public $error = '';
        public $insert_id = 1;
        public function prepare($query) {}
        public function query($query) {}
        public function begin_transaction() {}
        public function commit() {}
        public function rollback() {}
    }
}
if (!class_exists('mysqli_stmt')) {
    class mysqli_stmt {
        public $errno = 0;
        public $error = '';
        public $insert_id = 1;
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
// Define a constante global se não existir, para o teste de exclusão de arquivo físico.
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', '/tmp/');
}
// ====================================================================================


/**
 * Classe de teste para a classe Imovel.
 */
class ImovelTest extends TestCase
{
    private $connectionMock;
    private $stmtMock;
    private $resultMock;
    private $imovel;

    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->connectionMock = $this->createMock(mysqli::class);
        $this->imovel = new Imovel($this->connectionMock);
    }

    private function getDadosImovelMock()
    {
        return [
            'id_imobiliaria' => 1,
            'titulo' => 'Casa Teste',
            'descricao' => 'Descrição da casa',
            'tipo' => 'Casa',
            'status' => 'Disponível',
            'preco' => 500000.00,
            'endereco' => 'Rua Teste, 123',
            'latitude' => -22.90,
            'longitude' => -47.06,
            'id_imovel' => 1 // Para edição
        ];
    }

    public function testCadastrarComSucesso()
    {
        $dados = $this->getDadosImovelMock();
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->insert_id = 123; // Simula o ID do imóvel inserido

        // O método não retorna nada em sucesso, então o teste passa se nenhuma exceção for lançada.
        $this->imovel->cadastrar($dados, ['img1.jpg'], ['vid1.mp4']);
        $this->addToAssertionCount(1); // Garante que o teste não seja marcado como arriscado
    }

    public function testCadastrarFalhaAoPrepararQuery()
    {
        $this->expectException(Exception::class);
        $this->connectionMock->method('prepare')->willReturn(false);
        $this->imovel->cadastrar($this->getDadosImovelMock());
    }

    public function testCadastrarFalhaAoExecutar()
    {
        $this->expectException(Exception::class);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $this->imovel->cadastrar($this->getDadosImovelMock());
    }

    public function testEditarComSucesso()
    {
        $dados = $this->getDadosImovelMock();
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $this->imovel->editar($dados);
        $this->addToAssertionCount(1);
    }

    public function testEditarFalhaAoPreparar()
    {
        $this->expectException(Exception::class);
        $this->connectionMock->method('prepare')->willReturn(false);
        $this->imovel->editar($this->getDadosImovelMock());
    }

    public function testExcluirComSucesso()
    {
        // Simula a busca de arquivos (retorna vazio para simplificar)
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn([]);

        // Espera que os métodos de transação sejam chamados
        $this->connectionMock->expects($this->once())->method('begin_transaction');
        $this->connectionMock->expects($this->once())->method('commit');
        $this->connectionMock->expects($this->never())->method('rollback');

        $this->imovel->excluir(1);
    }

    public function testExcluirFalhaNaTransacao()
    {
        $this->expectException(Exception::class);

        // Simula a busca de arquivos
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn([]);

        // Configura o execute para falhar na hora de deletar o imóvel
        $this->stmtMock->method('execute')->will($this->onConsecutiveCalls(true, true, true, false));

        // Espera que o rollback seja chamado
        $this->connectionMock->expects($this->once())->method('begin_transaction');
        $this->connectionMock->expects($this->never())->method('commit');
        $this->connectionMock->expects($this->once())->method('rollback');

        $this->imovel->excluir(1);
    }

    public function testExcluirArquivoComTipoInvalido()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Tipo de arquivo inválido.");
        $this->imovel->excluirArquivo('invalido', 1);
    }

    public function testBuscarPorId()
    {
        $imovelEsperado = $this->getDadosImovelMock();
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($imovelEsperado);

        $resultado = $this->imovel->buscarPorId(1);
        $this->assertEquals($imovelEsperado, $resultado);
    }

    public function testBuscarArquivos()
    {
        $arquivosEsperados = [['id' => 1, 'caminho' => 'img.jpg']];
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($arquivosEsperados);
        
        $resultado = $this->imovel->buscarArquivos(1, 'imagem');
        $this->assertEquals($arquivosEsperados, $resultado);
    }

    public function testBuscarPorImobiliaria()
    {
        $imoveisEsperados = [$this->getDadosImovelMock()];
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->willReturn($imoveisEsperados);

        $resultado = $this->imovel->buscarPorImobiliaria(1);
        $this->assertEquals($imoveisEsperados, $resultado);
    }
}
