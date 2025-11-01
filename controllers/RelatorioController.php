<?php
// Garante que os caminhos estejam corretos a partir daqui
require_once __DIR__ . '/../models/Relatorio.php';
require_once __DIR__ . '/../config/config.php';

class RelatorioController {

    /**
     * Método principal da página de relatórios.
     * Processa o POST e prepara os dados para a View.
     */
    public function leads() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $connection; // Conexão global definida no config.php

        $relatorioModel = new Relatorio($connection);

        $dados = []; // IMPORTANTE: Inicializa $dados aqui

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filtros = [
                'dataInicio' => $_POST['dataInicio'] ?? null,
                'dataFim'    => $_POST['dataFim'] ?? null,
                'status'     => $_POST['status'] ?? null,
                'responsavel' => $_POST['responsavel'] ?? null,
                'imobiliaria' => $_POST['imobiliaria'] ?? null,
            ];

            $dados = $relatorioModel->gerarRelatorioLeads($filtros);

            // RF09 Fluxo de Erro
            if (empty($dados)) {
                $_SESSION['mensagem'] = "Nenhum dado disponível.";
            }
        }
        
        // Esta é a "mágica" do MVC Clássico:
        // O Controller prepara os dados, e a View (que será incluída)
        // vai magicamente ter acesso à variável $dados.
        return $dados;
    }
}
?>

