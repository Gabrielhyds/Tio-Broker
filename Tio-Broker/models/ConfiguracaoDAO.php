<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: models/DAO/ConfiguracaoDAO.php (VERSÃO CORRIGIDA)
|--------------------------------------------------------------------------
| Este DAO foi reescrito para interagir com as colunas individuais
| na tabela 'usuario', em vez de uma tabela 'configuracoes' separada.
*/

class ConfiguracaoDAO
{
    private $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    /**
     * ATUALIZADO: Salva as configurações diretamente na tabela 'usuario'.
     */
    public function salvarConfiguracoes(int $idUsuario, array $configuracoes): bool
    {
        // Query SQL para ATUALIZAR as colunas de configuração na tabela 'usuario'.
        $sql = "UPDATE usuario SET 
                    idioma = ?, 
                    tema = ?, 
                    tamanho_fonte = ?, 
                    notificacao_sonora = ?, 
                    notificacao_visual = ?, 
                    narrador = ?
                WHERE id_usuario = ?";

        $stmt = $this->conexao->prepare($sql);
        if ($stmt === false) {
            error_log("Erro ao preparar a query de salvar: " . $this->conexao->error);
            return false;
        }

        // Extrai os valores do array de configurações vindo do frontend.
        $idioma = $configuracoes['language'];
        $tema = $configuracoes['appearance']['theme'];
        $tamanho_fonte = $configuracoes['accessibility']['fontSize'];
        $notificacao_sonora = $configuracoes['notifications']['sound'] ? 1 : 0; // Converte boolean para 1 ou 0
        $notificacao_visual = $configuracoes['notifications']['visual'] ? 1 : 0; // Converte boolean para 1 ou 0
        $narrador = $configuracoes['accessibility']['narrator'] ? 1 : 0; // Converte boolean para 1 ou 0

        // Associa os parâmetros à declaração.
        // "sssiii" -> 3 strings, 3 integers, 1 integer (ID)
        $stmt->bind_param(
            'sssiiii',
            $idioma,
            $tema,
            $tamanho_fonte,
            $notificacao_sonora,
            $notificacao_visual,
            $narrador,
            $idUsuario
        );

        $sucesso = $stmt->execute();
        if (!$sucesso) {
            error_log("Erro ao executar a query de salvar: " . $stmt->error);
        }
        $stmt->close();
        return $sucesso;
    }

    /**
     * ATUALIZADO: Busca as configurações diretamente da tabela 'usuario'.
     */
    public function buscarConfiguracoes(int $idUsuario): ?array
    {
        // Query para selecionar as colunas de configuração da tabela 'usuario'.
        $sql = "SELECT idioma, tema, tamanho_fonte, notificacao_sonora, notificacao_visual, narrador 
                FROM usuario 
                WHERE id_usuario = ?";

        $stmt = $this->conexao->prepare($sql);
        if ($stmt === false) {
            error_log("Erro ao preparar a query de busca: " . $this->conexao->error);
            return null;
        }
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $resultadoQuery = $stmt->get_result();
        $db_settings = $resultadoQuery->fetch_assoc();
        $stmt->close();

        if ($db_settings) {
            // Monta o array no mesmo formato que o frontend espera.
            return [
                'language' => $db_settings['idioma'],
                'appearance' => [
                    'theme' => $db_settings['tema']
                ],
                'accessibility' => [
                    'fontSize' => $db_settings['tamanho_fonte'],
                    'narrator' => (bool)$db_settings['narrador']
                ],
                'notifications' => [
                    'sound' => (bool)$db_settings['notificacao_sonora'],
                    'visual' => (bool)$db_settings['notificacao_visual']
                ]
            ];
        }
        return null;
    }
}
