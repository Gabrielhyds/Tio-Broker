<?php

use PHPUnit\Framework\TestCase;

// Inclui o arquivo do Model que será testado.
// A estrutura de pastas sugere que o arquivo está em 'app/models/'.
// O autoloader do Composer geralmente cuida disso, mas para testes unitários isolados,
// um require_once pode ser necessário se o autoloader não estiver configurado.
require_once __DIR__ . '/../models/AgendaModel.php';

// ====================================================================================
// Bloco de Correção para o Erro "UnknownTypeException" e "Undefined Constant"
// Define classes e constantes "dummy" do MySQLi caso a extensão não esteja carregada
// no ambiente de linha de comando (CLI), permitindo que o PHPUnit crie os mocks.
// ====================================================================================
if (!class_exists('mysqli')) {
    class mysqli {
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
// Define a constante MYSQLI_ASSOC se ela não existir
if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}
// ====================================================================================


/**
 * Classe de teste para AgendaModel.
 * * Utiliza mocks para simular o comportamento do banco de dados (mysqli)
 * e testar cada método da classe AgendaModel de forma isolada.
 */
class AgendaModelTest extends TestCase
{
    private $connectionMock;
    private $stmtMock;
    private $resultMock;
    private $agendaModel;

    /**
     * Configuração inicial executada antes de cada teste.
     * Inicializa os mocks necessários para simular a conexão com o banco de dados.
     */
    protected function setUp(): void
    {
        // Cria um mock para a classe mysqli_result
        $this->resultMock = $this->createMock(mysqli_result::class);

        // Cria um mock para a classe mysqli_stmt (statement)
        $this->stmtMock = $this->createMock(mysqli_stmt::class);

        // Cria um mock para a classe mysqli (conexão)
        $this->connectionMock = $this->createMock(mysqli::class);

        // Instancia a classe que será testada, injetando o mock da conexão
        $this->agendaModel = new AgendaModel($this->connectionMock);
    }

    // =================================================================
    // Testes de Sucesso
    // =================================================================

    /**
     * Teste para a função buscarEventosPorUsuario.
     * Verifica se a função retorna corretamente um array de eventos.
     */
    public function testBuscarEventosPorUsuario()
    {
        $id_usuario = 1;
        $eventosEsperados = [
            ['id_evento' => 1, 'titulo' => 'Reunião de Equipe', 'id_usuario' => $id_usuario],
            ['id_evento' => 2, 'titulo' => 'Consulta Médica', 'id_usuario' => $id_usuario]
        ];

        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_all')->with(MYSQLI_ASSOC)->willReturn($eventosEsperados);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->buscarEventosPorUsuario($id_usuario);

        $this->assertEquals($eventosEsperados, $resultado);
    }

    /**
     * Teste para a função buscarEventoPorId.
     * Verifica se a função retorna um único evento quando ele existe.
     */
    public function testBuscarEventoPorIdEncontrado()
    {
        $id_evento = 1;
        $id_usuario = 1;
        $eventoEsperado = ['id_evento' => $id_evento, 'titulo' => 'Evento Teste', 'id_usuario' => $id_usuario];

        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn($eventoEsperado);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->buscarEventoPorId($id_evento, $id_usuario);

        $this->assertEquals($eventoEsperado, $resultado);
    }
    
    /**
     * Teste para a função buscarEventoPorId quando o evento não é encontrado.
     * Verifica se a função retorna null.
     */
    public function testBuscarEventoPorIdNaoEncontrado()
    {
        $id_evento = 999;
        $id_usuario = 1;

        $this->stmtMock->method('get_result')->willReturn($this->resultMock);
        $this->resultMock->method('fetch_assoc')->willReturn(null);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->buscarEventoPorId($id_evento, $id_usuario);

        $this->assertNull($resultado);
    }

    /**
     * Teste para a função criarEvento.
     * Verifica se a função retorna true em caso de sucesso na inserção.
     */
    public function testCriarEvento()
    {
        $dadosEvento = [
            'id_usuario' => 1, 'id_cliente' => '10', 'id_imovel' => '20',
            'titulo' => 'Novo Evento', 'descricao' => 'Descrição do novo evento',
            'data_inicio' => '2025-12-25 10:00:00', 'data_fim' => '2025-12-25 11:00:00',
            'tipo_evento' => 'Reunião', 'lembrete' => 1, 'feedback' => 'Aguardando'
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->criarEvento($dadosEvento);

        $this->assertTrue($resultado);
    }

    /**
     * Teste para a função atualizarEvento.
     * Verifica se a função retorna true em caso de sucesso na atualização.
     */
    public function testAtualizarEvento()
    {
        $id_evento = 1;
        $dadosEvento = [
            'id_usuario' => 1, 'id_cliente' => '11', 'id_imovel' => '22',
            'titulo' => 'Evento Atualizado', 'descricao' => 'Descrição atualizada',
            'data_inicio' => '2025-12-26 10:00:00', 'data_fim' => '2025-12-26 11:00:00',
            'tipo_evento' => 'Visita', 'lembrete' => 0, 'feedback' => 'Realizado'
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->atualizarEvento($id_evento, $dadosEvento);

        $this->assertTrue($resultado);
    }

    /**
     * Teste para a função atualizarDataEvento.
     * Verifica se a função de arrastar e soltar (drag-and-drop) funciona.
     */
    public function testAtualizarDataEvento()
    {
        $id_evento = 1;
        $id_usuario = 1;
        $data_inicio = '2025-12-27 14:00:00';
        $data_fim = '2025-12-27 15:00:00';

        $this->stmtMock->method('execute')->willReturn(true);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->atualizarDataEvento($id_evento, $data_inicio, $data_fim, $id_usuario);

        $this->assertTrue($resultado);
    }

    /**
     * Teste para a função excluirEvento.
     * Verifica se a função retorna true em caso de sucesso na exclusão.
     */
    public function testExcluirEvento()
    {
        $id_evento = 1;
        $id_usuario = 1;

        $this->stmtMock->method('execute')->willReturn(true);
        $this->connectionMock->method('prepare')->willReturn($this->stmtMock);

        $resultado = $this->agendaModel->excluirEvento($id_evento, $id_usuario);

        $this->assertTrue($resultado);
    }

    // =================================================================
    // Testes de Falha (quando o prepare retorna false)
    // =================================================================

    /**
     * Testa o comportamento de buscarEventosPorUsuario quando prepare() falha.
     * Deve retornar um array vazio.
     */
    public function testBuscarEventosPorUsuarioFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->buscarEventosPorUsuario(1);
        $this->assertEquals([], $resultado);
    }

    /**
     * Testa o comportamento de buscarEventoPorId quando prepare() falha.
     * Deve retornar null.
     */
    public function testBuscarEventoPorIdFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->buscarEventoPorId(1, 1);
        $this->assertNull($resultado);
    }

    /**
     * Testa o comportamento de criarEvento quando prepare() falha.
     * Deve retornar false.
     */
    public function testCriarEventoFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->criarEvento([]);
        $this->assertFalse($resultado);
    }

    /**
     * Testa o comportamento de atualizarEvento quando prepare() falha.
     * Deve retornar false.
     */
    public function testAtualizarEventoFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->atualizarEvento(1, []);
        $this->assertFalse($resultado);
    }

    /**
     * Testa o comportamento de atualizarDataEvento quando prepare() falha.
     * Deve retornar false.
     */
    public function testAtualizarDataEventoFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->atualizarDataEvento(1, '2025-01-01', '2025-01-01', 1);
        $this->assertFalse($resultado);
    }

    /**
     * Testa o comportamento de excluirEvento quando prepare() falha.
     * Deve retornar false.
     */
    public function testExcluirEventoFalhaNoPrepare()
    {
        $this->connectionMock->method('prepare')->willReturn(false);
        $resultado = $this->agendaModel->excluirEvento(1, 1);
        $this->assertFalse($resultado);
    }
}
