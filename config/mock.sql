-- Imobiliárias
INSERT INTO
    imobiliaria (nome, cnpj)
VALUES (
        'Imobiliária Alpha',
        '12.345.678/0001-90'
    ),
    (
        'Imobiliária Beta',
        '98.765.432/0001-00'
    );

-- Usuários
INSERT INTO
    usuario (
        nome,
        email,
        cpf,
        senha,
        telefone,
        permissao,
        id_imobiliaria
    )
VALUES (
        'João Corretor',
        'joao@alpha.com',
        '111.111.111-11',
        MD5('123456'),
        '(11)99999-1111',
        'Corretor',
        1
    ),
    (
        'Maria Coordenadora',
        'maria@beta.com',
        '222.222.222-22',
        MD5('123456'),
        '(11)99999-2222',
        'Coordenador',
        2
    ),
    (
        'Carlos Admin',
        'admin@alpha.com',
        '333.333.333-33',
        MD5('123456'),
        '(11)99999-3333',
        'Admin',
        1
    );

-- Clientes
INSERT INTO
    cliente (
        nome,
        numero,
        cpf,
        empreendimento,
        renda,
        entrada,
        fgts,
        subsidio,
        tipo_lista,
        id_usuario,
        id_imobiliaria
    )
VALUES (
        'José Cliente',
        '11988887777',
        '444.444.444-44',
        'Residencial Aurora',
        4000.00,
        10000.00,
        5000.00,
        2000.00,
        'Potencial',
        1,
        1
    ),
    (
        'Ana Compradora',
        '11977776666',
        '555.555.555-55',
        'Villa das Flores',
        5500.00,
        20000.00,
        3000.00,
        1500.00,
        'Potencial',
        2,
        2
    );

-- Imóveis
INSERT INTO
    imovel (
        id_imobiliaria,
        titulo,
        descricao,
        tipo,
        preco,
        endereco,
        latitude,
        longitude
    )
VALUES (
        1,
        'Apartamento 2Q no Centro',
        'Ótimo apto reformado',
        'venda',
        250000.00,
        'Rua Central, 123',
        -23.5505,
        -46.6333
    ),
    (
        2,
        'Casa com Piscina',
        'Casa ampla com área gourmet',
        'venda',
        450000.00,
        'Rua das Rosas, 789',
        -23.5599,
        -46.6299
    );

-- Tarefas
INSERT INTO
    tarefas (
        id_usuario,
        id_cliente,
        id_imobiliaria,
        descricao,
        status,
        prioridade,
        prazo
    )
VALUES (
        1,
        1,
        1,
        'Ligar para cliente José sobre proposta',
        'pendente',
        'alta',
        '2025-07-10'
    ),
    (
        2,
        2,
        2,
        'Agendar visita com Ana para Villa das Flores',
        'em andamento',
        'média',
        '2025-07-08'
    );

-- Conversas (privada)
INSERT INTO
    conversas (nome_conversa, tipo_conversa)
VALUES (
        'Chat entre João e Maria',
        'privada'
    );

-- Associação usuários-conversa
INSERT INTO
    usuarios_conversa (id_conversa, id_usuario)
VALUES (1, 1),
    (1, 2);

-- Mensagens
INSERT INTO
    mensagens (
        id_conversa,
        id_usuario,
        mensagem
    )
VALUES (
        1,
        1,
        'Oi Maria, já falou com o cliente?'
    ),
    (
        1,
        2,
        'Falei sim, está animado com a proposta!'
    );