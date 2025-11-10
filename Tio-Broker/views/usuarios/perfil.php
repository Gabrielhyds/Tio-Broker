<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? [];
$titulo_pagina = "Meu Perfil";
$conteudo = 'perfil_content.php';
include '../layout/template_base.php';
