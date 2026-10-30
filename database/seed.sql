-- Dados FICTÍCIOS para testar o GESTIQ localmente.
-- Rode depois do schema.sql: mysql -u root -p gestiq < database/seed.sql
-- Todos os usuários abaixo usam a senha de demonstração: gestiq123

USE gestiq;

INSERT INTO departamentos (nome, descricao) VALUES 
('Tecnologia da Informação', 'Desenvolvimento'), 
('Recursos Humanos', 'RH'), 
('Marketing', 'Marketing');

INSERT INTO cargos (nome, descricao, id_departamento) VALUES 
('Desenvolvedor PHP', 'Dev', 1), 
('Analista de Sistemas', 'Analista', 1), 
('Gerente de TI', 'Gestão', 1);

INSERT INTO usuarios (nome, email, senha, perfil, id_departamento, id_cargo) VALUES 
('Administrador Sistema', 'admin@gestiq.com', '$2y$10$GsIv8QEl7eRUZ00f93OrL.fbQbxS0Zh.ns0FQOTBCIS5b6o4p9Zma', 'admin', 1, 3),
('João Silva', 'joao@gestiq.com', '$2y$10$GsIv8QEl7eRUZ00f93OrL.fbQbxS0Zh.ns0FQOTBCIS5b6o4p9Zma', 'gestor', 1, 2),
('Maria Souza', 'maria@gestiq.com', '$2y$10$GsIv8QEl7eRUZ00f93OrL.fbQbxS0Zh.ns0FQOTBCIS5b6o4p9Zma', 'colaborador', 1, 1);

INSERT INTO projetos (titulo, descricao, data_inicio, data_fim, status, id_responsavel, id_departamento) VALUES 
('Novo Site', 'Site corp.', '2025-01-10', '2025-06-30', 'em_andamento', 2, 1);

INSERT INTO tarefas (titulo, descricao, id_projeto, id_responsavel, data_vencimento, status, prioridade) VALUES 
('Wireframes', 'Telas', 1, 3, '2025-02-28', 'concluida', 'alta');
