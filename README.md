Sistema de Gestão Imobiliária

Um sistema web completo para gestão de imobiliárias, corretores, clientes e empreendimentos. Desenvolvido em PHP com MySQL, utilizando padrão MVC e interface responsiva com Bootstrap.

🌐 Tecnologias Utilizadas

PHP 8+

MySQL (via MySQL Workbench)

Bootstrap 5

HTML5, CSS3, JavaScript

XAMPP (ou ambiente compatível com Apache + MySQL)

⚖️ Funcionalidades

SuperAdmin

Login com controle de sessão

Cadastro de imobiliárias

Cadastro e gestão de usuários (Admin, Coordenador, Corretor)

Vínculo de usuários com suas respectivas imobiliárias

Acesso à dashboard exclusiva com controle geral

Usuários (Admin, Coordenador, Corretor)

Login com redirecionamento conforme permissão

Visualização de clientes e empreendimentos

Registro de interações com clientes

Agenda e tarefas (em desenvolvimento)

🔧 Instalação Local

1. Clonar o repositório

git clone https://github.com/seu-usuario/seu-repo.git

2. Configurar ambiente

Instalar XAMPP e MySQL Workbench

Colocar o projeto dentro de C:/xampp/htdocs/

3. Criar banco de dados

Nome: tio_Broker

Importar o arquivo database.sql (estruturas de tabelas)

4. Configurar conexão no app/config/config.php

$host = "localhost";
$databasename = "tio_Broker";
$username = "root";
$password = "root"; // ou vazio se seu XAMPP não tem senha

5. Executar o projeto

Acesse no navegador:

http://localhost/nome-do-projeto/

