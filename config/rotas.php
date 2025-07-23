<?php

$basePath = '/tio-broker/';
$projectRoot = realpath(__DIR__ . '/../..');

// Se o diretório "app/views" existir, assumimos estrutura com /app
$usandoEstruturaComApp = is_dir($projectRoot . '/app/views');

// BASE_URL: para usar em <img>, <a>, etc.
define('BASE_URL', $basePath . ($usandoEstruturaComApp ? 'app/' : ''));

// Caminho físico absoluto até a raiz do projeto
define('BASE_DIR', $projectRoot . ($usandoEstruturaComApp ? '/app/' : '/'));

// Caminho físico para salvar arquivos
define('UPLOADS_DIR', $projectRoot . '/uploads/');

// Caminho público para acessar os uploads
define('UPLOADS_URL', $basePath . 'uploads/');