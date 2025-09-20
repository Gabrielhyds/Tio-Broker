<?php
// controllers/EmpreendimentoController.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Empreendimento.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$mysqli = $connection; // Pega a conexão do config.php

class EmpreendimentoController {
    private $model;

    public function __construct($mysqli) {
        $this->model = new Empreendimento($mysqli);
    }

    /**
     * Orquestra o cadastro de um novo empreendimento e seus arquivos.
     */
    public function criar($dados, $arquivos) {
        // Inicia a transação pedindo ao model, de forma segura.
        $this->model->beginTransaction();

        try {
            // 1. Salva os arquivos no servidor usando a função global do config.php
            $imagens = salvarUploads('imagens', 'empreendimentos');
            $videos = salvarUploads('videos', 'empreendimentos');
            $documentos = salvarUploads('documentos', 'empreendimentos');

            // 2. Chama o model para salvar os dados do empreendimento e os caminhos dos arquivos no banco
            $this->model->criar($dados, $imagens, $videos, $documentos);

            // 3. Se tudo deu certo, confirma a transação
            $this->model->commit();
            $_SESSION['sucesso'] = "Empreendimento cadastrado com sucesso.";

        } catch (Exception $e) {
            // 4. Se qualquer passo falhou, desfaz todas as operações no banco
            $this->model->rollback();
            $_SESSION['erro'] = "Erro ao cadastrar empreendimento: " . $e->getMessage();
        }

        // Redireciona de volta para a página de listagem
        header('Location: ../views/empreendimento/listar_empreendimento.php');
        exit;
    }

    // Futuros métodos como editar, deletar, etc.
}


// --- LÓGICA DE EXECUÇÃO ---
// Verifica se o formulário foi enviado com a ação de 'cadastrar'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $controller = new EmpreendimentoController($mysqli);
    // Chama o método criar, passando os dados do formulário e os arquivos
    $controller->criar($_POST, $_FILES);
}

