<?php

// Caminho base da URL para uso em links e assets
$basePath = '/tio-broker/';

// Caminho abs oluto até a raiz do projeto
$projectRoot = realpath(__DIR__ . '/../');

// BASE_URL: para uso em <img>, <a>, etc.
define('BASE_URL', $basePath);

// Caminho físico absoluto do projeto (ex: para includes, uploads, etc.)
define('BASE_DIR', $projectRoot . '/');

// Caminho físico onde os arquivos serão salvos
define('UPLOADS_DIR', BASE_DIR . 'uploads/');

// Caminho acessível via navegador para os arquivos
define('UPLOADS_URL', BASE_URL . 'uploads/');
