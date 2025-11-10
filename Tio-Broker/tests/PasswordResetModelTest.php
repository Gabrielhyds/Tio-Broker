<?php

use PHPUnit\Framework\TestCase;

// Inclui a classe que será testada
require_once __DIR__ . '/../models/PasswordResetModel.php';

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
 * Classe de teste para a classe PasswordResetModel.
 */
class PasswordResetModelTest extends TestCase
{
    private $connMock;
    private $stmtMock;
    private $resultMock;
    private $passwordResetModel;

    /**
     * Configura o ambiente de teste antes de cada método.
     */
    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(mysqli_stmt::class);
        $this->resultMock = $this->createMock(mysqli_result::class);
        $this->connMock = $this->createMock(mysqli::class);
        $this->passwordResetModel = new PasswordResetModel($this->connMock);
    }

    protected function tearDown(): void
    {
        unset($this->passwordResetModel, $this->connMock, $this->stmtMock, $this->resultMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    public function testCreateTokenComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $resultado = $this->passwordResetModel->createToken(1, 'meu_token_seguro_123');
        $this->assertTrue($resultado);
    }

    public function testGetTokenComSucesso()
    {
        $tokenEsperado = ['user_id' => 1, 'token' => 'token_valido'];
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($tokenEsperado);

        $resultado = $this->passwordResetModel->getToken('token_valido');
        $this->assertEquals($tokenEsperado, $resultado);
    }
    
    public function testGetTokenNaoEncontrado()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null);

        $resultado = $this->passwordResetModel->getToken('token_invalido');
        $this->assertNull($resultado);
    }

    public function testInvalidateTokenComSucesso()
    {
        $this->connMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);

        $resultado = $this->passwordResetModel->invalidateToken('token_a_ser_invalidado');
        $this->assertTrue($resultado);
    }

    // =================================================================
    // Testes de Falha (quando o prepare retorna false)
    // =================================================================

    public function testCreateTokenFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->passwordResetModel->createToken(1, 'token_qualquer');
        $this->assertFalse($resultado);
    }

    public function testGetTokenFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->passwordResetModel->getToken('token_qualquer');
        $this->assertNull($resultado);
    }

    public function testInvalidateTokenFalhaNoPrepare()
    {
        $this->connMock->method('prepare')->willReturn(false);
        $resultado = $this->passwordResetModel->invalidateToken('token_qualquer');
        $this->assertFalse($resultado);
    }
}
