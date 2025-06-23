<?php
$basePath = '/tio-broker/';

// Caminho absoluto do projeto (do servidor de arquivos)
$projectRoot = realpath(__DIR__ . '/../..');

// Verifica se a pasta "app/views" existe dentro da estrutura
$temApp = is_dir($projectRoot . '/app/views');

define('BASE_URL', $basePath . ($temApp ? 'app/' : ''));
