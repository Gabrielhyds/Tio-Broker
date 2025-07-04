CREATE DATABASE IF NOT EXISTS tio_Broker; -- Cria o banco de dados 'tio_Broker' se não existir
USE tio_broker; -- Usa o banco de dados 'tio_Broker' para criar as tabelas

-- Tabela de Imobiliária com exclusão lógica (is_deleted)
CREATE TABLE imobiliaria (
    id_imobiliaria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    -- 0 para ativo, 1 para excluído
    is_deleted TINYINT(1) NOT NULL DEFAULT 0
);

-- Tabela de Usuários com exclusão lógica (is_deleted)
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    creci VARCHAR(20) NULL,
    telefone VARCHAR(20) NOT NULL,
    permissao ENUM('SuperAdmin', 'Admin', 'Coordenador', 'Corretor') NOT NULL,
    foto TEXT NULL,
    id_imobiliaria INT,
    -- 0 para ativo, 1 para excluído
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    -- Chave estrangeira ajustada para não apagar o usuário
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE SET NULL
);

-- Tabela de Clientes com exclusão lógica (is_deleted)
CREATE TABLE cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    empreendimento VARCHAR(255),
    renda DECIMAL(10,2),
    entrada DECIMAL(10,2),
    fgts DECIMAL(10,2),
    subsidio DECIMAL(10,2),
    foto TEXT,
    tipo_lista ENUM('Não potencial', 'Potencial') NOT NULL,
    id_usuario INT NOT NULL,
    id_imobiliaria INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- 0 para ativo, 1 para excluído
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE SET NULL
);

-- Tabela de Interações
CREATE TABLE interacoes (
    id_interacao INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo_interacao ENUM('mensagem', 'telefone', 'reuniao') NOT NULL,
    descricao TEXT NOT NULL,
    data_interacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Notificações
CREATE TABLE notificacoes (
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Agenda de Eventos
CREATE TABLE agenda_eventos (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT NOT NULL,
    id_imovel INT NULL DEFAULT NULL AFTER id_cliente,
    feedback TEXT NULL DEFAULT NULL AFTER lembrete
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    tipo_evento ENUM('reuniao', 'visita', 'outro') NOT NULL,
    lembrete BOOLEAN DEFAULT FALSE,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE
);

-- Tabela de Documentos
CREATE TABLE documentos (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    nome_documento VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(50) NOT NULL,
    caminho_arquivo TEXT NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE tarefas (
    id_tarefa INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT NULL,
    id_imobiliaria INT NULL, -- Coluna para associar a tarefa à imobiliária
    descricao TEXT NOT NULL,
    status ENUM('pendente', 'em andamento', 'concluida') NOT NULL DEFAULT 'pendente',
    prioridade ENUM('baixa', 'média', 'alta') DEFAULT 'média',
    prazo DATE DEFAULT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_conclusao DATETIME DEFAULT NULL,
    -- Chaves estrangeiras
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE CASCADE
);


-- Tabela de Ranking de Desempenho
CREATE TABLE ranking_desempenho (
    id_ranking INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    vendas INT DEFAULT 0,
    contatos INT DEFAULT 0,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Conversas
CREATE TABLE conversas (
    id_conversa INT AUTO_INCREMENT PRIMARY KEY,
    nome_conversa VARCHAR(255) NULL,
    tipo_conversa ENUM('privada', 'grupo') NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Associação entre Usuários e Conversas
CREATE TABLE usuarios_conversa (
    id_conversa INT NOT NULL,
    id_usuario INT NOT NULL,
    PRIMARY KEY (id_conversa, id_usuario),
    FOREIGN KEY (id_conversa) REFERENCES conversas(id_conversa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Mensagens
CREATE TABLE mensagens (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    id_conversa INT NOT NULL,
    id_usuario INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_conversa) REFERENCES conversas(id_conversa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Reações
CREATE TABLE reacoes (
  id_reacao INT AUTO_INCREMENT PRIMARY KEY,
  id_mensagem INT NOT NULL,
  id_usuario INT NOT NULL,
  reacao VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_mensagem) REFERENCES mensagens(id_mensagem) ON DELETE CASCADE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  UNIQUE KEY `reacao_unica_usuario_mensagem` (`id_mensagem`, `id_usuario`)
);

-- Tabela para recuperação de senha com controle de expiração e uso
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Tabela de Imóveis (com id_imobiliaria)
CREATE TABLE imovel (
    id_imovel INT AUTO_INCREMENT PRIMARY KEY,
    id_imobiliaria INT NULL, -- Coluna para associar o imóvel à imobiliária
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    tipo ENUM('venda', 'locacao', 'temporada', 'lancamento') NOT NULL,
    status ENUM('disponivel', 'reservado', 'vendido', 'indisponivel') DEFAULT 'disponivel',
    preco DECIMAL(15,2) NOT NULL,
    endereco VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria(id_imobiliaria) ON DELETE SET NULL
);


CREATE TABLE imovel_imagem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel(id_imovel) ON DELETE CASCADE
);

CREATE TABLE imovel_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel(id_imovel) ON DELETE CASCADE
);

CREATE TABLE imovel_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel(id_imovel) ON DELETE CASCADE
);
