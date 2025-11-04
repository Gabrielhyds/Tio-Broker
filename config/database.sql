CREATE DATABASE IF NOT EXISTS tio_Broker;
-- Cria o banco de dados 'tio_Broker' se não existir
USE tio_broker;
-- Usa o banco de dados 'tio_Broker' para criar as tabelas

-- Tabela de Imobiliária com exclusão lógica (is_deleted)
CREATE TABLE imobiliaria (
    id_imobiliaria INT AUTO_INCREMENT PRIMARY KEY,
    tipo_pessoa CHAR(1) NOT NULL DEFAULT 'J' COMMENT 'F = Física | J = Jurídica',
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) NULL,
    cpf VARCHAR(14) NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0, -- 0 para ativo, 1 para excluído
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
    permissao ENUM(
        'SuperAdmin',
        'Admin',
        'Coordenador',
        'Corretor'
    ) NOT NULL,
    foto TEXT NULL,
    id_imobiliaria INT,
    idioma VARCHAR(10) DEFAULT 'pt-br',
    tema ENUM('light', 'dark') DEFAULT 'light',
    tamanho_fonte VARCHAR(10) DEFAULT 'text-base',
    notificacao_sonora BOOLEAN DEFAULT TRUE,
    notificacao_visual BOOLEAN DEFAULT TRUE,
    narrador BOOLEAN DEFAULT FALSE,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE SET NULL
);

-- Tabela de Clientes com exclusão lógica (is_deleted)
CREATE TABLE cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    empreendimento VARCHAR(255),
    renda DECIMAL(10, 2),
    entrada DECIMAL(10, 2),
    fgts DECIMAL(10, 2),
    subsidio DECIMAL(10, 2),
    foto TEXT,
    tipo_lista ENUM('Não potencial', 'Potencial') NOT NULL,
    id_usuario INT NOT NULL,
    id_imobiliaria INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- 0 para ativo, 1 para excluído
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE SET NULL
);

-- Tabela de Interações
CREATE TABLE interacoes (
    id_interacao INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo_interacao ENUM(
        'mensagem',
        'telefone',
        'reuniao'
    ) NOT NULL,
    descricao TEXT NOT NULL,
    data_interacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

-- Tabela de Notificações
CREATE TABLE notificacoes (
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

CREATE TABLE agenda_eventos (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT NOT NULL,
    id_imovel INT NULL DEFAULT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    tipo_evento ENUM('reuniao', 'visita', 'outro') NOT NULL,
    lembrete BOOLEAN DEFAULT FALSE,
    feedback TEXT NULL DEFAULT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente) ON DELETE CASCADE,
    -- A linha abaixo é a que adicionamos com o comando ALTER TABLE
    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel) ON DELETE SET NULL
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
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

CREATE TABLE tarefas (
    id_tarefa INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT NULL,
    id_imobiliaria INT NULL, -- Coluna para associar a tarefa à imobiliária
    descricao TEXT NOT NULL,
    status ENUM(
        'pendente',
        'em andamento',
        'concluida'
    ) NOT NULL DEFAULT 'pendente',
    prioridade ENUM('baixa', 'média', 'alta') DEFAULT 'média',
    prazo DATE DEFAULT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_conclusao DATETIME DEFAULT NULL,
    -- Chaves estrangeiras
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE CASCADE
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
    FOREIGN KEY (id_conversa) REFERENCES conversas (id_conversa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

-- Tabela de Mensagens
CREATE TABLE mensagens (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    id_conversa INT NOT NULL,
    id_usuario INT NOT NULL,
    mensagem TEXT NOT NULL,
    editada_em TIMESTAMP NULL DEFAULT NULL, -- nova coluna para registrar edições
    apagada TINYINT(1) NOT NULL DEFAULT 0, -- nova coluna para exclusão lógica
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    lida BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_conversa) REFERENCES conversas (id_conversa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
    INDEX ix_msg_conversa_data (id_conversa, data_envio), -- índice para buscas por conversa e data
    INDEX ix_msg_usuario_lida (id_usuario, lida) -- índice para buscas por usuário e status de leitura
);

-- Tabela de Reações
CREATE TABLE reacoes (
    id_reacao INT AUTO_INCREMENT PRIMARY KEY,
    id_mensagem INT NOT NULL,
    id_usuario INT NOT NULL,
    reacao VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mensagem) REFERENCES mensagens (id_mensagem) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE,
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
    FOREIGN KEY (user_id) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

CREATE TABLE imovel (
  id_imovel INT AUTO_INCREMENT PRIMARY KEY,
  id_imobiliaria INT NULL,
  titulo VARCHAR(255) NOT NULL,
  descricao TEXT,
  tipo ENUM('venda', 'locacao', 'temporada', 'lancamento') NOT NULL,
  status ENUM('disponivel', 'reservado', 'vendido', 'indisponivel') DEFAULT 'disponivel',
  preco DECIMAL(15, 2) NOT NULL,

-- Campos de Endereço Refatorados


endereco VARCHAR(255) NULL, -- Logradouro (Rua, Avenida, etc.)
  cep VARCHAR(10) NULL,
  numero VARCHAR(20) NULL,
  complemento VARCHAR(100) NULL,
  bairro VARCHAR(100) NULL,
  cidade VARCHAR(100) NULL,
  estado VARCHAR(2) NULL, -- Armazena a sigla (UF)
  
  data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE SET NULL
);

CREATE TABLE imovel_imagem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel) ON DELETE CASCADE
);

CREATE TABLE imovel_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel) ON DELETE CASCADE
);

CREATE TABLE imovel_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_imovel INT,
    caminho VARCHAR(255),
    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel) ON DELETE CASCADE
);

CREATE TABLE empreendimento (
    id_empreendimento INT AUTO_INCREMENT PRIMARY KEY,
    id_imobiliaria INT NULL, -- Empresa responsável pelo empreendimento
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    categoria ENUM('imobiliario', 'automotivo', 'franquia', 'outro') DEFAULT 'imobiliario',
    status ENUM('planejamento', 'em_andamento', 'concluido', 'disponivel', 'encerrado') DEFAULT 'disponivel',
    responsavel VARCHAR(255) NULL, -- construtora, montadora, franqueadora etc.
    -- Endereço do empreendimento
    endereco VARCHAR(255) NULL,
    cidade VARCHAR(100) NULL,
    estado VARCHAR(2) NULL,
    cep VARCHAR(10) NULL,

    -- Preço mínimo e máximo para referência
    preco_min DECIMAL(15, 2) NULL, preco_max DECIMAL(15, 2) NULL,

    -- Datas do empreendimento
    data_inicio DATE NULL, data_entrega DATE NULL,

    -- Controle de criação e atualização
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Exclusão lógica
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,

    -- Chave estrangeira para imobiliária
    FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE SET NULL
);

CREATE TABLE empreendimento_imovel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empreendimento INT NOT NULL,
    id_imovel INT NOT NULL,
    FOREIGN KEY (id_empreendimento) REFERENCES empreendimento (id_empreendimento) ON DELETE CASCADE,
    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel) ON DELETE CASCADE,
    UNIQUE KEY unq_empreendimento_imovel (id_empreendimento, id_imovel)
);

CREATE TABLE empreendimento_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empreendimento INT NOT NULL,
    id_cliente INT NULL,
    id_lead INT NULL,
    interesse ENUM('alto', 'medio', 'baixo') DEFAULT 'medio',
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empreendimento) REFERENCES empreendimento (id_empreendimento) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente) ON DELETE CASCADE
);

CREATE TABLE empreendimento_imagem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empreendimento INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_empreendimento) REFERENCES empreendimento (id_empreendimento) ON DELETE CASCADE
);

CREATE TABLE empreendimento_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empreendimento INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_empreendimento) REFERENCES empreendimento (id_empreendimento) ON DELETE CASCADE
);

CREATE TABLE empreendimento_documento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empreendimento INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_empreendimento) REFERENCES empreendimento (id_empreendimento) ON DELETE CASCADE
);

CREATE TABLE leads (
  id_lead INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  telefone VARCHAR(20),
  origem VARCHAR(100), -- (RF01)
  interesse VARCHAR(255), -- (RF01)
  status_pipeline ENUM('Novo', 'Contato', 'Negociação', 'Fechado', 'Perdido') NOT NULL DEFAULT 'Novo', -- (RF04)
  id_usuario_responsavel INT NULL, -- (RF06)
  id_imobiliaria INT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0, -- (RF03 - Exclusão lógica)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario_responsavel) REFERENCES usuario (id_usuario) ON DELETE SET NULL,
  FOREIGN KEY (id_imobiliaria) REFERENCES imobiliaria (id_imobiliaria) ON DELETE SET NULL
);

-- Tabela de Interações do Lead (RF08)
CREATE TABLE lead_interacoes (
  id_interacao INT AUTO_INCREMENT PRIMARY KEY,
  id_lead INT NOT NULL,
  id_usuario INT NOT NULL,
  tipo_interacao ENUM('ligacao', 'visita', 'email', 'whatsapp', 'outro') NOT NULL, -- (RF08)
  descricao TEXT NOT NULL, -- (RF08)
  data_interacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_lead) REFERENCES leads (id_lead) ON DELETE CASCADE,
  FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100) NOT NULL,
    contrato_texto TEXT NOT NULL,
    assinatura_simulada TEXT,
    data_assinatura DATETIME,
    ip_assinante VARCHAR(45)
);