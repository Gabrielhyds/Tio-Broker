
-- ##################################################################
-- ## 1. TABELAS DE BASE (ENTIDADES PRINCIPAIS)
-- ##################################################################

-- Tabela: imobiliaria (3 registros)
INSERT INTO imobiliaria (id_imobiliaria, tipo_pessoa, nome, cnpj, cpf, is_deleted) VALUES
(1, 'J', 'Imobiliária Central LTDA', '11.111.111/0001-11', NULL, 0),
(2, 'J', 'Sua Casa Imóveis', '22.222.222/0001-22', NULL, 0),
(3, 'F', 'José Silva - Corretor Autônomo', NULL, '111.222.333-44', 0);

-- Tabela: usuario (7 registros)
-- Senhas são 'senha_hash' e devem ser trocadas por hashes reais
INSERT INTO usuario (id_usuario, nome, email, cpf, senha, creci, telefone, permissao, id_imobiliaria, is_deleted) VALUES
(1, 'Super Administrador', 'super@tiobroker.com', '100.100.100-10', 'e10adc3949ba59abbe56e057f20f883e', NULL, '(11) 91111-1111', 'SuperAdmin', NULL, 0),
(2, 'Ana Gerente (Imob 1)', 'ana.gerente@imobcentral.com', '200.200.200-20', 'e10adc3949ba59abbe56e057f20f883e', '12345-J', '(11) 92222-2222', 'Admin', 1, 0),
(3, 'Bruno Coordenador (Imob 1)', 'bruno.coord@imobcentral.com', '300.300.300-30', 'e10adc3949ba59abbe56e057f20f883e', '23456-F', '(11) 93333-3333', 'Coordenador', 1, 0),
(4, 'Carlos Corretor (Imob 2)', 'carlos.corretor@suacasa.com', '400.400.400-40', 'e10adc3949ba59abbe56e057f20f883e', '34567-F', '(19) 94444-4444', 'Corretor', 2, 0),
(5, 'Daniela Corretora (Imob 2)', 'daniela.corretora@suacasa.com', '500.500.500-50', 'e10adc3949ba59abbe56e057f20f883e', '45678-F', '(19) 95555-5555', 'Corretor', 2, 0),
(6, 'José Silva (Corretor F)', 'jose.silva@corretor.com', '111.222.333-44', 'e10adc3949ba59abbe56e057f20f883e', '56789-F', '(11) 96666-6666', 'Admin', 3, 0),
(7, 'Usuário Demitido', 'demitido@imobcentral.com', '700.700.700-70', 'e10adc3949ba59abbe56e057f20f883e', '67890-F', '(11) 97777-7777', 'Corretor', 1, 1);


-- Tabela: cliente (11 registros)
INSERT INTO cliente (id_cliente, nome, numero, cpf, empreendimento, renda, entrada, fgts, subsidio, tipo_lista, id_usuario, id_imobiliaria, is_deleted) VALUES
(1, 'Marcos Oliveira', '(19) 98111-1111', '111.111.111-11', 'Residencial Flores', 4500.00, 20000.00, 15000.00, 5000.00, 'Potencial', 4, 2, 0),
(2, 'Beatriz Costa', '(19) 98222-2222', '222.222.222-22', 'Jardim das Palmeiras', 7000.00, 50000.00, 30000.00, 0.00, 'Potencial', 4, 2, 0),
(3, 'Carla Dias', '(11) 98333-3333', '333.333.333-33', 'Torres de São Paulo', 3200.00, 10000.00, 8000.00, 12000.00, 'Não potencial', 5, 2, 0),
(4, 'Daniel Moreira', '(11) 98444-4444', '444.444.444-44', 'Residencial Flores', 5100.00, 25000.00, 20000.00, 0.00, 'Potencial', 3, 1, 0),
(5, 'Eduarda Lima', '(11) 98555-5555', '555.555.555-55', 'Condomínio Central', 12000.00, 100000.00, 50000.00, 0.00, 'Potencial', 2, 1, 0),
(6, 'Fábio Guedes', '(19) 98666-6666', '666.666.666-66', 'Jardim das Palmeiras', 4000.00, 15000.00, 10000.00, 10000.00, 'Não potencial', 5, 2, 0),
(7, 'Gabriela Pinto', '(11) 98777-7777', '777.777.777-77', 'Residencial Flores', 6000.00, 30000.00, 22000.00, 0.00, 'Potencial', 3, 1, 0),
(8, 'Heitor Alves', '(19) 98888-8888', '888.888.888-88', 'Torres de São Paulo', 3500.00, 10000.00, 5000.00, 15000.00, 'Potencial', 4, 2, 0),
(9, 'Isis Fernandes', '(11) 98999-9999', '999.999.999-99', 'Condomínio Central', 9000.00, 80000.00, 40000.00, 0.00, 'Potencial', 2, 1, 0),
(10, 'João Mendes', '(11) 98000-0000', '000.000.000-00', 'Residencial Flores', 4200.00, 20000.00, 12000.00, 8000.00, 'Potencial', 3, 1, 0),
(11, 'Cliente Deletado', '(11) 98123-4567', '123.456.789-00', 'N/A', 0.00, 0.00, 0.00, 0.00, 'Não potencial', 4, 2, 1);

-- Tabela: imovel (10 registros)
INSERT INTO imovel (id_imovel, id_imobiliaria, titulo, descricao, tipo, status, preco, endereco, cep, numero, bairro, cidade, estado) VALUES
(1, 1, 'Apartamento 2 Quartos - Centro', 'Lindo apartamento no centro da cidade, perto de tudo.', 'venda', 'disponivel', 350000.00, 'Rua Principal', '13010-001', '100', 'Centro', 'Campinas', 'SP'),
(2, 1, 'Casa 3 Quartos com Piscina - Taquaral', 'Casa espaçosa com área de lazer completa.', 'venda', 'reservado', 850000.00, 'Av. Brasil', '13073-001', '500', 'Taquaral', 'Campinas', 'SP'),
(3, 2, 'Apartamento 1 Quarto - Locação', 'Studio mobiliado ideal para estudantes.', 'locacao', 'disponivel', 1500.00, 'Rua dos Estudantes', '13100-000', '25B', 'Barão Geraldo', 'Campinas', 'SP'),
(4, 2, 'Terreno Comercial - Av. Norte-Sul', 'Terreno de esquina em avenida movimentada.', 'venda', 'disponivel', 1200000.00, 'Av. Norte-Sul', '13015-000', '1500', 'Cambuí', 'Campinas', 'SP'),
(5, 1, 'Lançamento - Residencial Flores', 'Apartamentos na planta, 2 e 3 dorms. Oportunidade.', 'lancamento', 'disponivel', 280000.00, 'Rua das Flores', '13050-000', '1000', 'Jd. das Flores', 'Hortolândia', 'SP'),
(6, 3, 'Casa de Campo - Joaquim Egídio', 'Chácara com 3 quartos, piscina e pomar.', 'temporada', 'disponivel', 800.00, 'Estrada da Montanha', '13270-000', 'S/N', 'Zona Rural', 'Joaquim Egídio', 'SP'),
(7, 2, 'Apartamento Vendido - Cambuí', 'Cobertura duplex com 3 suítes.', 'venda', 'vendido', 1500000.00, 'Rua Sampaio Ferraz', '13025-001', '300', 'Cambuí', 'Campinas', 'SP'),
(8, 1, 'Sala Comercial - Centro', 'Sala de 40m² em prédio comercial com portaria.', 'locacao', 'disponivel', 1200.00, 'Rua 13 de Maio', '13013-001', '404', 'Centro', 'Campinas', 'SP'),
(9, 2, 'Casa Antiga - Indisponível', 'Casa para reforma, atualmente indisponível.', 'venda', 'indisponivel', 300000.00, 'Rua Velha', '13020-000', '10', 'Vila Industrial', 'Campinas', 'SP'),
(10, 1, 'Lançamento - Torres de São Paulo', 'Apartamentos de alto padrão na planta.', 'lancamento', 'disponivel', 700000.00, 'Av. Paulista', '01311-000', '2000', 'Bela Vista', 'São Paulo', 'SP');

-- Tabela: empreendimento (3 registros)
INSERT INTO empreendimento (id_empreendimento, id_imobiliaria, nome, descricao, status, responsavel, endereco, cidade, estado, cep, preco_min, preco_max) VALUES
(1, 1, 'Residencial Flores', 'O novo lançamento da Imobiliária Central em Hortolândia. Apartamentos de 2 e 3 dormitórios com lazer completo.', 'disponivel', 'Construtora XYZ', 'Rua das Flores, 1000', 'Hortolândia', 'SP', '13050-000', 280000.00, 350000.00),
(2, 1, 'Torres de São Paulo', 'Alto padrão na capital. Apartamentos de 120m² a 180m².', 'em_andamento', 'Construtora ABC', 'Av. Paulista, 2000', 'São Paulo', 'SP', '01311-000', 700000.00, 1200000.00),
(3, 2, 'Jardim das Palmeiras', 'Loteamento fechado em Campinas. Terrenos a partir de 300m².', 'concluido', 'Loteadora FGH', 'Av. das Palmeiras', 'Campinas', 'SP', '13090-000', 250000.00, 400000.00);

-- Tabela: conversas (5 registros)
INSERT INTO conversas (id_conversa, nome_conversa, tipo_conversa) VALUES
(1, 'Corretores (Carlos e Daniela)', 'privada'),
(2, 'Vendas - Imob 2', 'grupo'),
(3, 'Gestão - Imob 1', 'privada'),
(4, 'Coordenação - Imob 1', 'grupo'),
(5, 'Geral - Tio Broker', 'grupo');


-- ##################################################################
-- ## 2. TABELAS TRANSACIONAIS (ATIVIDADES)
-- ##################################################################

-- Tabela: interacoes (10 registros)
INSERT INTO interacoes (id_cliente, id_usuario, tipo_interacao, descricao, data_interacao) VALUES
(1, 4, 'mensagem', 'Cliente respondeu WhatsApp. Agendou visita para sábado.', NOW() - INTERVAL 1 DAY),
(1, 4, 'telefone', 'Liguei para confirmar a visita. Tudo certo.', NOW() - INTERVAL 4 HOUR),
(2, 4, 'reuniao', 'Reunião no stand de vendas. Cliente demonstrou forte interesse.', NOW() - INTERVAL 2 DAY),
(3, 5, 'mensagem', 'Cliente informou que a renda não foi aprovada. Status: Não Potencial.', NOW() - INTERVAL 3 DAY),
(4, 3, 'telefone', 'Primeiro contato telefônico. Cliente pareceu interessado no Residencial Flores.', NOW() - INTERVAL 1 DAY),
(5, 2, 'reuniao', 'Reunião de apresentação do Torres de SP. Cliente achou o preço elevado.', NOW() - INTERVAL 5 DAY),
(6, 5, 'mensagem', 'Cliente não respondeu às últimas 3 mensagens.', NOW() - INTERVAL 1 WEEK),
(7, 3, 'telefone', 'Cliente ligou pedindo mais fotos do Residencial Flores.', NOW() - INTERVAL 2 HOUR),
(8, 4, 'mensagem', 'Enviada simulação de financiamento pelo WhatsApp.', NOW() - INTERVAL 6 HOUR),
(10, 3, 'mensagem', 'Cliente perguntou sobre o prazo de entrega do Residencial Flores.', NOW() - INTERVAL 1 DAY);

-- Tabela: notificacoes (10 registros)
INSERT INTO notificacoes (id_usuario, mensagem, lida) VALUES
(4, 'Novo lead recebido: Marcos Oliveira', TRUE),
(5, 'Novo lead recebido: Carla Dias', TRUE),
(3, 'Novo lead recebido: Daniel Moreira', TRUE),
(2, 'Novo lead recebido: Eduarda Lima', FALSE),
(4, 'Lembrete: Visita com Marcos Oliveira hoje às 15h.', FALSE),
(1, 'O usuário Bruno Coordenador (Imob 1) cadastrou um novo imóvel.', FALSE),
(3, 'O cliente Daniel Moreira enviou um documento.', TRUE),
(5, 'Tarefa "Ligar para cliente Fábio" está atrasada.', FALSE),
(2, 'O cliente Eduarda Lima favoritou o imóvel "Torres de São Paulo".', FALSE),
(4, 'O cliente Beatriz Costa foi movido para a etapa "Proposta".', TRUE);

-- Tabela: agenda_eventos (10 registros)
INSERT INTO agenda_eventos (id_usuario, id_cliente, id_imovel, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete) VALUES
(4, 1, 5, 'Visita - Marcos Oliveira - Residencial Flores', 'Encontrar no stand de vendas. Levar tabela de preços.', NOW() + INTERVAL 1 DAY + INTERVAL 6 HOUR, NOW() + INTERVAL 1 DAY + INTERVAL 7 HOUR, 'visita', TRUE),
(4, 2, NULL, 'Reunião - Beatriz Costa - Proposta', 'Apresentação da proposta de financiamento no escritório.', NOW() + INTERVAL 2 DAY + INTERVAL 2 HOUR, NOW() + INTERVAL 2 DAY + INTERVAL 3 HOUR, 'reuniao', TRUE),
(3, 4, 5, 'Visita - Daniel Moreira - Residencial Flores', 'Visita ao apartamento decorado.', NOW() + INTERVAL 3 DAY + INTERVAL 1 HOUR, NOW() + INTERVAL 3 DAY + INTERVAL 2 HOUR, 'visita', TRUE),
(2, 5, 10, 'Visita - Eduarda Lima - Torres SP', 'Visita ao terreno e apresentação do projeto.', NOW() + INTERVAL 4 DAY, NOW() + INTERVAL 4 DAY + INTERVAL 1 HOUR, 'visita', FALSE),
(5, 3, NULL, 'Follow-up Carla Dias', 'Ligar para ver se situação da renda mudou.', NOW() + INTERVAL 1 WEEK, NOW() + INTERVAL 1 WEEK + INTERVAL 30 MINUTE, 'outro', TRUE),
(3, 7, 5, 'Visita - Gabriela Pinto', 'Visita ao decorado. Trazer simulação.', NOW() + INTERVAL 1 DAY + INTERVAL 3 HOUR, NOW() + INTERVAL 1 DAY + INTERVAL 4 HOUR, 'visita', TRUE),
(2, 9, 10, 'Reunião - Isis Fernandes', 'Almoço de negócios para discutir proposta.', NOW() + INTERVAL 2 DAY + INTERVAL 15 HOUR, NOW() + INTERVAL 2 DAY + INTERVAL 17 HOUR, 'reuniao', TRUE),
(4, 8, 10, 'Visita - Heitor Alves - Torres SP', 'Cliente de SP, quer conhecer o projeto.', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR, 'visita', FALSE),
(3, 10, 5, 'Visita - João Mendes', 'Cliente quer visitar o empreendimento no fim de semana.', NOW() + INTERVAL 2 DAY + INTERVAL 1 HOUR, NOW() + INTERVAL 2 DAY + INTERVAL 2 HOUR, 'visita', TRUE),
(1, 2, NULL, 'Reunião de Alinhamento Admin', 'Reunião com Ana Gerente (Imob 1).', NOW() + INTERVAL 1 DAY + INTERVAL 1 HOUR, NOW() + INTERVAL 1 DAY + INTERVAL 2 HOUR, 'reuniao', FALSE);

-- Tabela: documentos (10 registros)
-- (Caminhos são fictícios)
INSERT INTO documentos (id_cliente, id_usuario, nome_documento, tipo_documento, caminho_arquivo) VALUES
(1, 4, 'RG - Marcos Oliveira', 'Identidade', '/uploads/docs/cli_1/rg_frente.pdf'),
(1, 4, 'Comprovante Renda - Marcos', 'Financeiro', '/uploads/docs/cli_1/holerite_mes1.pdf'),
(2, 4, 'Declaração IR - Beatriz Costa', 'Financeiro', '/uploads/docs/cli_2/irpf_2024.pdf'),
(4, 3, 'CPF - Daniel Moreira', 'Identidade', '/uploads/docs/cli_4/cpf.jpg'),
(4, 3, 'Comprovante Endereço - Daniel', 'Endereço', '/uploads/docs/cli_4/conta_luz.pdf'),
(5, 2, 'Extrato FGTS - Eduarda Lima', 'Financeiro', '/uploads/docs/cli_5/fgts.pdf'),
(7, 3, 'Certidão Casamento - Gabriela', 'Civil', '/uploads/docs/cli_7/certidao.pdf'),
(8, 4, 'Comprovante Renda - Heitor', 'Financeiro', '/uploads/docs/cli_8/holerite.pdf'),
(9, 2, 'Proposta Assinada - Isis', 'Proposta', '/uploads/docs/cli_9/proposta_torres.pdf'),
(10, 3, 'RG - João Mendes', 'Identidade', '/uploads/docs/cli_10/rg.pdf');

-- Tabela: tarefas (10 registros)
INSERT INTO tarefas (id_usuario, id_cliente, id_imobiliaria, descricao, status, prioridade, prazo) VALUES
(4, 1, 2, 'Confirmar visita de Sábado com Marcos', 'concluida', 'alta', NOW() - INTERVAL 1 DAY),
(4, 2, 2, 'Montar proposta de financiamento para Beatriz Costa', 'em andamento', 'alta', NOW() + INTERVAL 1 DAY),
(5, 6, 2, 'Tentar contato com Fábio Guedes (sem resposta)', 'pendente', 'baixa', NOW() + INTERVAL 3 DAY),
(3, 7, 1, 'Enviar simulação atualizada para Gabriela Pinto', 'pendente', 'média', NOW() + INTERVAL 2 HOUR),
(2, NULL, 1, 'Revisar comissão do imóvel ID 7 (vendido)', 'pendente', 'média', NOW() + INTERVAL 2 DAY),
(1, NULL, NULL, 'Verificar log de erros do servidor', 'pendente', 'alta', NOW() + INTERVAL 1 DAY),
(3, 10, 1, 'Ligar para João Mendes após visita', 'pendente', 'média', NOW() + INTERVAL 2 DAY),
(4, 8, 2, 'Fazer follow-up da simulação enviada para Heitor', 'pendente', 'média', NOW() + INTERVAL 1 DAY),
(2, 5, 1, 'Pesquisar imóveis similares ao Torres SP para Eduarda', 'em andamento', 'baixa', NOW() + INTERVAL 1 WEEK),
(5, 3, 2, 'Arquivar lead Carla Dias (renda insuficiente)', 'concluida', 'baixa', NOW());

-- Tabela: usuarios_conversa (12 registros)
INSERT INTO usuarios_conversa (id_conversa, id_usuario) VALUES
(1, 4), (1, 5), -- Conversa 1 (Privada): Carlos e Daniela
(2, 4), (2, 5), (2, 1), -- Conversa 2 (Grupo): Vendas Imob 2 + SuperAdmin
(3, 2), (3, 3), -- Conversa 3 (Privada): Ana Gerente e Bruno Coord
(4, 2), (4, 3), -- Conversa 4 (Grupo): Gestão Imob 1
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6); -- Conversa 5 (Geral): Todos usuários ativos

-- Tabela: mensagens (13 registros)
INSERT INTO mensagens (id_conversa, id_usuario, mensagem, editada_em, apagada, lida) VALUES
(2, 4, 'Pessoal, novo lead (Marcos Oliveira) agendou visita para o Residencial Flores.', NULL, 0, TRUE),
(2, 5, 'Ótimo! Esse empreendimento está com boa saída.', NULL, 0, TRUE),
(2, 1, 'Equipe Imob 2, por favor, me mantenham informado sobre as visitas do Residencial Flores.', NULL, 0, FALSE),
(3, 3, 'Ana, checou os documentos do cliente Daniel Moreira?', NULL, 0, TRUE),
(3, 2, 'Sim, Bruno. Pedi para ele enviar o comprovante de endereço que faltava.', NULL, 0, TRUE),
(3, 3, 'Ok, me avise quando estiver tudo certo para a análise de crédito.', NULL, 0, FALSE),
(5, 1, 'Bem-vindos ao novo sistema Tio Broker!', NULL, 0, TRUE),
(5, 6, 'Sistema parece muito bom, parabéns!', NULL, 0, TRUE),
(1, 4, 'Dani, você pode fazer o follow-up do cliente Heitor? Ele parece ser bom.', NULL, 0, TRUE),
(1, 5, 'Claro, Carlos. Deixei uma tarefa para ligar para ele amanhã.', NULL, 0, FALSE),
(2, 4, 'A cliente Beatriz Costa aceitou a proposta!', NULL, 0, FALSE),
(2, 4, 'A cliente Beatriz Costa aceitou a proposta!! (editado)', NOW() - INTERVAL 1 HOUR, 0, FALSE),
(5, 3, 'Essa mensagem será apagada.', NULL, 1, TRUE);

-- Tabela: reacoes (5 registros)
INSERT INTO reacoes (id_mensagem, id_usuario, reacao) VALUES
(1, 5, '👍'),
(2, 4, '🚀'),
(11, 5, '🎉'),
(11, 1, '👏'),
(8, 3, '👀');


-- ##################################################################
-- ## 3. TABELAS DE RELACIONAMENTO E MÍDIA
-- ##################################################################

-- Tabela: imovel_imagem (10 registros)
INSERT INTO imovel_imagem (id_imovel, caminho) VALUES
(1, '/uploads/imoveis/1/foto_sala.jpg'),
(1, '/uploads/imoveis/1/foto_cozinha.jpg'),
(1, '/uploads/imoveis/1/foto_quarto1.jpg'),
(2, '/uploads/imoveis/2/foto_frente.jpg'),
(2, '/uploads/imoveis/2/foto_piscina.jpg'),
(3, '/uploads/imoveis/3/foto_studio.jpg'),
(5, '/uploads/imoveis/5/perspectiva_fachada.jpg'),
(5, '/uploads/imoveis/5/planta_2dorms.jpg'),
(7, '/uploads/imoveis/7/cobertura_vendida.jpg'),
(10, '/uploads/imoveis/10/fachada_perspectiva_torres.jpg');

-- Tabela: imovel_video (2 registros)
INSERT INTO imovel_video (id_imovel, caminho) VALUES
(2, '/uploads/imoveis/2/video_drone_piscina.mp4'),
(5, '/uploads/imoveis/5/video_decorado.mp4');

-- Tabela: imovel_documento (2 registros)
INSERT INTO imovel_documento (id_imovel, caminho) VALUES
(1, '/uploads/imoveis/1/matricula_imovel_1.pdf'),
(4, '/uploads/imoveis/4/matricula_terreno_4.pdf');

-- Tabela: empreendimento_imovel (2 registros)
-- (Liga os imóveis do tipo "Lançamento" aos seus Empreendimentos)
INSERT INTO empreendimento_imovel (id_empreendimento, id_imovel) VALUES
(1, 5),  -- Liga 'Lançamento - Residencial Flores' (Imovel 5) ao 'Residencial Flores' (Empreendimento 1)
(2, 10); -- Liga 'Lançamento - Torres de São Paulo' (Imovel 10) ao 'Torres de São Paulo' (Empreendimento 2)

-- Tabela: empreendimento_cliente (10 registros)
-- (Liga clientes que têm interesse em um Empreendimento)
INSERT INTO empreendimento_cliente (id_empreendimento, id_cliente, id_lead, interesse) VALUES
(1, 1, NULL, 'alto'), -- Marcos Oliveira no Residencial Flores
(3, 2, NULL, 'medio'), -- Beatriz Costa no Jardim das Palmeiras
(2, 3, NULL, 'baixo'), -- Carla Dias no Torres de SP (renda baixa)
(1, 4, NULL, 'alto'), -- Daniel Moreira no Residencial Flores
(2, 5, NULL, 'medio'), -- Eduarda Lima no Torres de SP
(3, 6, NULL, 'baixo'), -- Fábio Guedes no Jardim das Palmeiras
(1, 7, NULL, 'alto'), -- Gabriela Pinto no Residencial Flores
(2, 8, NULL, 'medio'), -- Heitor Alves no Torres de SP
(2, 9, NULL, 'alto'), -- Isis Fernandes no Torres de SP
(1, 10, NULL, 'alto'); -- João Mendes no Residencial Flores

-- Tabela: empreendimento_imagem (10 registros)
INSERT INTO empreendimento_imagem (id_empreendimento, caminho) VALUES
(1, '/uploads/empreendimentos/1/fachada.jpg'),
(1, '/uploads/empreendimentos/1/area_lazer.jpg'),
(1, '/uploads/empreendimentos/1/piscina_perspectiva.jpg'),
(1, '/uploads/empreendimentos/1/academia.jpg'),
(1, '/uploads/empreendimentos/1/planta_geral.jpg'),
(2, '/uploads/empreendimentos/2/perspectiva_noturna.jpg'),
(2, '/uploads/empreendimentos/2/lobby_decorado.jpg'),
(2, '/uploads/empreendimentos/2/vista_aerea.jpg'),
(3, '/uploads/empreendimentos/3/vista_aerea_lotes.jpg'),
(3, '/uploads/empreendimentos/3/portaria_3d.jpg');

-- Tabela: empreendimento_video (3 registros)
INSERT INTO empreendimento_video (id_empreendimento, caminho) VALUES
(1, '/uploads/empreendimentos/1/video_institucional_flores.mp4'),
(1, '/uploads/empreendimentos/1/video_drone_obra.mp4'),
(2, '/uploads/empreendimentos/2/video_decorado_torres.mp4');

-- Tabela: empreendimento_documento (3 registros)
INSERT INTO empreendimento_documento (id_empreendimento, caminho) VALUES
(1, '/uploads/empreendimentos/1/memorial_descritivo_flores.pdf'),
(2, '/uploads/empreendimentos/2/tabela_precos_torres_sp.pdf'),
(3, '/uploads/empreendimentos/3/contrato_loteamento_palmeiras.pdf');


-- ##################################################################
-- ## FIM DO SCRIPT
-- ##################################################################