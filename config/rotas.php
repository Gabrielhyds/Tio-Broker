<?php
$basePath = '/tio-broker/';

// Verifica se a pasta 'app' e 'views' existem
$temApp = is_dir(__DIR__ . '/../views'); // Caminho relativo a 'app/config/'

define('BASE_URL', $basePath . ($temApp ? 'app/' : ''));
