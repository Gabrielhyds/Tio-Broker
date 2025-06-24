<?php

// Define o caminho base da URL do sistema (usado para montar links no frontend)
$basePath = '/tio-broker/';

// Obtém o caminho absoluto da raiz do projeto
// __DIR__ representa o diretório atual (normalmente /app/config), então voltamos dois níveis para chegar à raiz
$projectRoot = realpath(__DIR__ . '/../..');

// Verifica se a pasta "app/views" existe dentro da estrutura do projeto
// Isso serve para identificar se estamos em um ambiente local (com estrutura completa) ou produção
$temApp = is_dir($projectRoot . '/app/views');

// Define a constante BASE_URL, que será usada para montar URLs em todo o sistema
// Se a pasta app/views existir, adiciona "app/" ao final do path
// Caso contrário, assume que estamos rodando direto da raiz
define('BASE_URL', $basePath . ($temApp ? 'app/' : ''));
