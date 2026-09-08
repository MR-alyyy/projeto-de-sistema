CREATE DATABASE IF NOT EXISTS trabalho1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE trabalho1;

-- Tabela de usuários cadastrados (login/cadastro/recuperação de senha)
CREATE TABLE cadastro (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    email  VARCHAR(255) NOT NULL UNIQUE,
    senha  VARCHAR(255) NOT NULL
);

-- Registro de cada login realizado
CREATE TABLE logins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255) NOT NULL,
    data_login  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cadastro de nomes feito na tela de menu
CREATE TABLE menu (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(255) NOT NULL
);

-- Registro de e-mails de recuperação de senha enviados
CREATE TABLE emails_enviados (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    email_destino  VARCHAR(255) NOT NULL,
    data_envio     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
