<?php

// Conexão com banco de dados
// Faz a conexão com o banco de dados.

// Nesse bloco de codigo vamos definir as variaveis que serão usadas para fazer a conexão com o banco de dados

$host = "localhost";
$databasename = "tio_Broker";
$username = "root";
$password = "";

// Nesse proximo bloco ele cria uma conexão com o banco de dados 
// aqui usamos uma função do PHP para conectar o banco chamada "mysqli"

$connection = new mysqli($host,$username,$password,$databasename);

// Nesse utimo passso verifica se ocorreu algum erro na conexão com o PHP e o Banco de dados
//usaremos um if 
 //caso entre no if ele exibe uma mensagem do erro que causou
if($connection->connect_error){
    //exibe a mensagem de erro e encerra o script
    die("Erro de conexão:" . $connection->connect_error);
}

?>