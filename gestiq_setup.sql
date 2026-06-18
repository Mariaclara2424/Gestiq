DROP DATABASE IF EXISTS gestiq;
CREATE DATABASE gestiq;
USE gestiq;

CREATE TABLE departamentos (
    id_departamento INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_departamento),
    UNIQUE KEY uq_dept_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cargos (
    id_cargo INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    id_departamento INT UNSIGNED,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_cargo),
    CONSTRAINT fk_cargo_dept FOREIGN KEY (id_departamento) REFERENCES departamentos (id_departamento) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin','gestor','colaborador') NOT NULL DEFAULT 'colaborador',
    id_departamento INT UNSIGNED,
    id_cargo INT UNSIGNED,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_usuario_email (email),
    CONSTRAINT fk_usuario_dept FOREIGN KEY (id_departamento) REFERENCES departamentos (id_departamento) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_usuario_cargo FOREIGN KEY (id_cargo) REFERENCES cargos (id_cargo) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projetos (
    id_projeto INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    data_inicio DATE NOT NULL,
    data_fim DATE,
    status ENUM('planejamento','em_andamento','concluido','cancelado') NOT NULL DEFAULT 'planejamento',
    id_responsavel INT UNSIGNED NOT NULL,
    id_departamento INT UNSIGNED,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_projeto),
    CONSTRAINT fk_projeto_resp FOREIGN KEY (id_responsavel) REFERENCES usuarios (id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_projeto_dept FOREIGN KEY (id_departamento) REFERENCES departamentos (id_departamento) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tarefas (
    id_tarefa INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    id_projeto INT UNSIGNED NOT NULL,
    id_responsavel INT UNSIGNED,
    data_vencimento DATE,
    status ENUM('pendente','em_andamento','concluida','cancelada') NOT NULL DEFAULT 'pendente',
    prioridade ENUM('baixa','media','alta','critica') NOT NULL DEFAULT 'media',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_tarefa),
    CONSTRAINT fk_tarefa_proj FOREIGN KEY (id_projeto) REFERENCES projetos (id_projeto) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tarefa_resp FOREIGN KEY (id_responsavel) REFERENCES usuarios (id_usuario) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comentarios (
    id_comentario INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_tarefa INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    texto TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_comentario),
    CONSTRAINT fk_coment_tarefa FOREIGN KEY (id_tarefa) REFERENCES tarefas (id_tarefa) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_coment_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO departamentos (nome, descricao) VALUES 
('Tecnologia da Informação', 'Desenvolvimento'), 
('Recursos Humanos', 'RH'), 
('Marketing', 'Marketing');

INSERT INTO cargos (nome, descricao, id_departamento) VALUES 
('Desenvolvedor PHP', 'Dev', 1), 
('Analista de Sistemas', 'Analista', 1), 
('Gerente de TI', 'Gestão', 1);

INSERT INTO usuarios (nome, email, senha, perfil, id_departamento, id_cargo) VALUES 
('Administrador Sistema', 'admin@gestiq.com', '$2y$10$VhLTXOSDwUzV0K0zUmPQC.RUmfSN.G4XPp6ehyukArwzyAGKLjmm2', 'admin', 1, 3),
('João Silva', 'joao@gestiq.com', '$2y$10$VhLTXOSDwUzV0K0zUmPQC.RUmfSN.G4XPp6ehyukArwzyAGKLjmm2', 'gestor', 1, 2),
('Maria Souza', 'maria@gestiq.com', '$2y$10$VhLTXOSDwUzV0K0zUmPQC.RUmfSN.G4XPp6ehyukArwzyAGKLjmm2', 'colaborador', 1, 1);

INSERT INTO projetos (titulo, descricao, data_inicio, data_fim, status, id_responsavel, id_departamento) VALUES 
('Novo Site', 'Site corp.', '2025-01-10', '2025-06-30', 'em_andamento', 2, 1);

INSERT INTO tarefas (titulo, descricao, id_projeto, id_responsavel, data_vencimento, status, prioridade) VALUES 
('Wireframes', 'Telas', 1, 3, '2025-02-28', 'concluida', 'alta');