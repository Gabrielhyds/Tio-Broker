<?php
$basePath = '/tio-broker/';
$temApp = is_dir(__DIR__ . '/../views'); // Corrigido: verifica onde REALMENTE está a pasta views
define('BASE_URL', $basePath . ($temApp ? 'app/' : ''));
