-- Estrutura do banco do GESTIQ (sem dados)
-- Uso: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS gestiq
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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

CREATE TABLE google_tokens (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id    INT UNSIGNED NOT NULL,
    access_token  TEXT         NOT NULL,
    refresh_token TEXT         NOT NULL,
    atualizado_em TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
