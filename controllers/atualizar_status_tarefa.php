<?php
// Inclui o arquivo de configuração que provavelmente contém a conexão com o DB ($connection)
require_once '../config/config.php';
// Inicia a sessão (embora não esteja sendo usada para verificar o usuário nesta versão)
session_start();

// Pega o ID da tarefa e o novo status vindos de um formulário POST.
// Usa o operador '??' (null coalescing) para definir como null se não existirem.
$id_tarefa = $_POST['id_tarefa'] ?? null;
$novo_status = $_POST['novo_status'] ?? null;

// Define uma lista (whitelist) de status permitidos.
// Isso é uma medida de segurança para evitar que valores inesperados
// sejam inseridos no banco de dados.
$permitidos = ['pendente', 'em andamento', 'concluida'];

// Validação: verifica se o ID da tarefa foi fornecido E se o status recebido
// está dentro da lista de status permitidos.
if (!$id_tarefa || !in_array($novo_status, $permitidos)) {
    // Se os dados forem inválidos, retorna um JSON de erro e para a execução.
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

// Prepara a consulta SQL para atualizar a tarefa.
/*
 * ATENÇÃO: Esta query é INSEGURA em um ambiente multi-usuário,
 * pois permite que QUALQUER usuário mude QUALQUER tarefa, bastando saber o ID.
 * A query correta deveria incluir "AND id_usuario = ?" para garantir
 * que o usuário só possa alterar as próprias tarefas.
 */
$stmt = $connection->prepare("UPDATE tarefas SET status = ? WHERE id_tarefa = ?");

// Associa (bind) as variáveis PHP aos placeholders (?) da query.
// "si" significa que o primeiro parâmetro é uma String ($novo_status)
// e o segundo é um Integer ($id_tarefa).
$stmt->bind_param("si", $novo_status, $id_tarefa);

// Executa a consulta no banco de dados.
// $ok guardará 'true' se a execução foi bem-sucedida, ou 'false' se falhou.
$ok = $stmt->execute();

// Retorna um JSON simples indicando se a execução (execute()) funcionou.
// Nota: $ok = true não significa que uma linha foi afetada,
// apenas que a query rodou sem erro de sintaxe ou conexão.
echo json_encode(['success' => $ok]);
