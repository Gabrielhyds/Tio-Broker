CREATE DATABASE tio_Broker; -- Cria o banco de dados 'tio_Broker'
USE tio_broker; -- Usa o banco de dados 'tio_Broker' para criar as tabelas

CREATE TABLE imobiliaria ( -- Tabela de Imobiliária
    id_imobiliaria INT AUTO_INCREMENT PRIMARY KEY, -- ID único da imobiliária
    nome VARCHAR(255) NOT NULL, -- Nome da imobiliária
    cnpj VARCHAR(18) UNIQUE NOT NULL, -- CNPJ da imobiliária
    quantidade_usuarios INT DEFAULT 0 -- Quantidade de usuários associados
);

CREATE TABLE usuario ( -- Tabela de Usuários
    id_usuario INT AUTO_INCREMENT PRIMARY KEY, -- ID único do usuário
    nome VARCHAR(100) NOT NULL, -- Nome do usuário
    email VARCHAR(100) UNIQUE NOT NULL, -- Email do usuário
    cpf VARCHAR(14) UNIQUE NOT NULL, -- CPF do usuário
    senha VARCHAR(255) NOT NULL, -- Senha do usuário
    creci VARCHAR(20) NULL, -- CRECI do corretor (opcional)
    telefone VARCHAR(20) NOT NULL, -- Telefone do usuário
    permissao ENUM('SuperAdmin', 'Admin', 'Coordenador', 'Corretor') NOT NULL, -- Permissões do usuário
    foto TEXT NULL, -- Foto do usuário (opcional)
    id_imobiliaria INT, -- Chave estrangeira para a imobiliária
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE CASCADE -- Relaciona com a tabela 'imobiliaria'
);

CREATE TABLE cliente ( -- Tabela de Clientes
    id_cliente INT AUTO_INCREMENT PRIMARY KEY, -- ID único do cliente
    nome VARCHAR(255) NOT NULL, -- Nome do cliente
    numero VARCHAR(20) NOT NULL UNIQUE, -- Número do cliente
    cpf VARCHAR(14) NOT NULL UNIQUE, -- CPF do cliente
    empreendimento VARCHAR(255), -- Nome do empreendimento (opcional)
    renda DECIMAL(10,2), -- Renda do cliente
    entrada DECIMAL(10,2), -- Valor da entrada do cliente
    fgts DECIMAL(10,2), -- Valor do FGTS do cliente
    subsidio DECIMAL(10,2), -- Valor do subsídio do cliente
    foto TEXT, -- Foto do cliente (opcional)
    tipo_lista ENUM('Não potencial', 'Potencial') NOT NULL, -- Tipo de cliente (potencial ou não)
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    id_imobiliaria INT, -- Chave estrangeira para a imobiliária
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE, -- Relaciona com a tabela 'usuario'
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE SET NULL -- Relaciona com a tabela 'imobiliaria'
);

CREATE TABLE interacoes ( -- Tabela de Interações
    id_interacao INT AUTO_INCREMENT PRIMARY KEY, -- ID único da interação
    id_cliente INT NOT NULL, -- Chave estrangeira para o cliente
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    tipo_interacao ENUM('mensagem', 'telefone', 'reuniao') NOT NULL, -- Tipo de interação
    descricao TEXT NOT NULL, -- Descrição da interação
    data_interacao DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data da interação
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE, -- Relaciona com a tabela 'cliente'
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);

CREATE TABLE notificacoes ( -- Tabela de Notificações
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY, -- ID único da notificação
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    mensagem TEXT NOT NULL, -- Mensagem da notificação
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data de envio da notificação
    lida BOOLEAN NOT NULL DEFAULT FALSE, -- Status de leitura da notificação
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);

CREATE TABLE agenda_eventos ( -- Tabela de Agenda de Eventos
    id_evento INT AUTO_INCREMENT PRIMARY KEY, -- ID único do evento
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    id_cliente INT NOT NULL, -- Chave estrangeira para o cliente
    titulo VARCHAR(255) NOT NULL, -- Título do evento
    descricao TEXT, -- Descrição do evento
    data_inicio DATETIME NOT NULL, -- Data de início do evento
    data_fim DATETIME NOT NULL, -- Data de fim do evento
    tipo_evento ENUM('reuniao', 'visita', 'outro') NOT NULL, -- Tipo de evento
    lembrete BOOLEAN DEFAULT FALSE, -- Se haverá lembrete para o evento
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data de criação do evento
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE, -- Relaciona com a tabela 'usuario'
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE -- Relaciona com a tabela 'cliente'
);

CREATE TABLE documentos ( -- Tabela de Documentos
    id_documento INT AUTO_INCREMENT PRIMARY KEY, -- ID único do documento
    id_cliente INT NOT NULL, -- Chave estrangeira para o cliente
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    nome_documento VARCHAR(255) NOT NULL, -- Nome do documento
    tipo_documento VARCHAR(50) NOT NULL, -- Tipo do documento
    caminho_arquivo TEXT NOT NULL, -- Caminho do arquivo
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data de upload do documento
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE, -- Relaciona com a tabela 'cliente'
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);

CREATE TABLE tarefas ( -- Tabela de Tarefas
    id_tarefa INT AUTO_INCREMENT PRIMARY KEY, -- ID único da tarefa
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    id_cliente INT NOT NULL, -- Chave estrangeira para o cliente
    descricao TEXT NOT NULL, -- Descrição da tarefa
    status ENUM('pendente', 'em andamento', 'concluida') NOT NULL DEFAULT 'pendente', -- Status da tarefa
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data de criação da tarefa
    data_conclusao DATETIME, -- Data de conclusão da tarefa
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE, -- Relaciona com a tabela 'usuario'
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE -- Relaciona com a tabela 'cliente'
);

CREATE TABLE ranking_desempenho ( -- Tabela de Ranking de Desempenho
    id_ranking INT AUTO_INCREMENT PRIMARY KEY, -- ID único do ranking
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    vendas INT DEFAULT 0, -- Quantidade de vendas
    contatos INT DEFAULT 0, -- Quantidade de contatos
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data da última atualização do ranking
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);

CREATE TABLE conversas ( -- Tabela de Conversas
    id_conversa INT AUTO_INCREMENT PRIMARY KEY, -- ID único da conversa
    nome_conversa VARCHAR(255) NULL, -- Nome da conversa (opcional)
    tipo_conversa ENUM('privada', 'grupo') NOT NULL, -- Tipo de conversa (privada ou grupo)
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP -- Data de criação da conversa
);

CREATE TABLE usuarios_conversa ( -- Tabela de Associação entre Usuários e Conversas
    id_conversa INT NOT NULL, -- Chave estrangeira para a conversa
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    PRIMARY KEY (id_conversa, id_usuario), -- Chave primária composta (conversa e usuário)
    FOREIGN KEY (id_conversa) REFERENCES conversas(id_conversa) ON DELETE CASCADE, -- Relaciona com a tabela 'conversas'
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);

CREATE TABLE mensagens ( -- Tabela de Mensagens
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY, -- ID único da mensagem
    id_conversa INT NOT NULL, -- Chave estrangeira para a conversa
    id_usuario INT NOT NULL, -- Chave estrangeira para o usuário
    mensagem TEXT NOT NULL, -- Conteúdo da mensagem
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP, -- Data de envio da mensagem
    lida BOOLEAN NOT NULL DEFAULT FALSE, -- Status de leitura da mensagem
    FOREIGN KEY (id_conversa) REFERENCES conversas(id_conversa) ON DELETE CASCADE, -- Relaciona com a tabela 'conversas'
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE -- Relaciona com a tabela 'usuario'
);
