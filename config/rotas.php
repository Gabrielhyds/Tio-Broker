<?php

$basePath = '/tio-broker/';

$projectRoot = realpath(__DIR__ . '/../..');
$temApp = is_dir($projectRoot . '/app/views');

// Define o caminho base da URL para o frontend
define('BASE_URL', $basePath . ($temApp ? 'app/' : ''));

// Caminho base absoluto (para servidor)
define('BASE_DIR', $projectRoot . '/');

// Diretório de uploads no servidor
define('UPLOADS_DIR', BASE_DIR . ($temApp ? 'app/uploads/' : 'uploads/'));

// Diretório de uploads acessível via navegador
define('UPLOADS_URL', BASE_URL . 'uploads/');
