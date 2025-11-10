<?php

use PHPUnit\Framework\TestCase;

// Adapte o caminho conforme a estrutura do seu projeto.
require_once __DIR__ . '/../models/ConfiguracaoDAO.php'; 

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
        public function fetch_assoc() {}
    }
}
// ====================================================================================


/**
 * Classe de teste para a classe ConfiguracaoDAO.
 */
class ConfiguracaoDAOTest extends TestCase
{
    private $conexaoMock;
    private $stmtMock;
    private $resultMock;
    private $configuracaoDAO;

    /**
     * Configuração inicial executada antes de cada teste.
     */
    protected function setUp(): void
    {
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->conexaoMock = $this->createMock(mysqli::class);
        $this->configuracaoDAO = new ConfiguracaoDAO($this->conexaoMock);
    }

    // =================================================================
    // Testes para o método salvarConfiguracoes
    // =================================================================

    public function testSalvarConfiguracoesComSucesso()
    {
        $idUsuario = 1;
        $configuracoes = [
            'language' => 'pt-br',
            'appearance' => ['theme' => 'dark'],
            'accessibility' => ['fontSize' => '16px', 'narrator' => true],
            'notifications' => ['sound' => true, 'visual' => false]
        ];

        $this->conexaoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);

        $sucesso = $this->configuracaoDAO->salvarConfiguracoes($idUsuario, $configuracoes);
        $this->assertTrue($sucesso);
    }

    public function testSalvarConfiguracoesFalhaNoPrepare()
    {
        $idUsuario = 1;
        $configuracoes = ['language' => 'en', 'appearance' => [], 'accessibility' => [], 'notifications' => []]; // Dados mínimos

        $this->conexaoMock->method('prepare')->willReturn(false);

        $sucesso = $this->configuracaoDAO->salvarConfiguracoes($idUsuario, $configuracoes);
        $this->assertFalse($sucesso);
    }

    public function testSalvarConfiguracoesFalhaNaExecucao()
    {
        $idUsuario = 1;
        $configuracoes = [
            'language' => 'pt-br',
            'appearance' => ['theme' => 'dark'],
            'accessibility' => ['fontSize' => '16px', 'narrator' => true],
            'notifications' => ['sound' => true, 'visual' => false]
        ];

        $this->conexaoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);

        $sucesso = $this->configuracaoDAO->salvarConfiguracoes($idUsuario, $configuracoes);
        $this->assertFalse($sucesso);
    }


    // =================================================================
    // Testes para o método buscarConfiguracoes
    // =================================================================

    public function testBuscarConfiguracoesComSucesso()
    {
        $idUsuario = 1;
        $dbSettings = [
            'idioma' => 'pt-br',
            'tema' => 'dark',
            'tamanho_fonte' => '16px',
            'notificacao_sonora' => 1,
            'notificacao_visual' => 0,
            'narrador' => 1
        ];

        $configuracoesEsperadas = [
            'language' => 'pt-br',
            'appearance' => ['theme' => 'dark'],
            'accessibility' => ['fontSize' => '16px', 'narrator' => true],
            'notifications' => ['sound' => true, 'visual' => false]
        ];
        
        $this->conexaoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($dbSettings);

        $resultado = $this->configuracaoDAO->buscarConfiguracoes($idUsuario);
        $this->assertEquals($configuracoesEsperadas, $resultado);
    }

    public function testBuscarConfiguracoesNaoEncontrado()
    {
        $idUsuario = 999;

        $this->conexaoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null);

        $resultado = $this->configuracaoDAO->buscarConfiguracoes($idUsuario);
        $this->assertNull($resultado);
    }

    public function testBuscarConfiguracoesFalhaNoPrepare()
    {
        $idUsuario = 1;
        $this->conexaoMock->method('prepare')->willReturn(false);

        $resultado = $this->configuracaoDAO->buscarConfiguracoes($idUsuario);
        $this->assertNull($resultado);
    }
}
