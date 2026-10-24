-- ========================================================
-- SCRIPT SQL COMPLETO E ATUALIZADO - SISTEMA DE MARCENARIA
-- ========================================================

-- 1. Tabela de Clientes
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  endereco TEXT DEFAULT NULL,
  data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabela de Serviços (com o nome_servico correto que o PHP exige)
CREATE TABLE servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_servico VARCHAR(100) NOT NULL,
  descricao TEXT DEFAULT NULL,
  preco_base DECIMAL(10,2) NOT NULL,
  categoria VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabela de Orçamentos (com quantidade e data_orcamento)
CREATE TABLE orcamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  servico_id INT NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  valor_total DECIMAL(10,2) NOT NULL,
  data_orcamento DATE NOT NULL,
  status VARCHAR(20) DEFAULT 'Pendente',
  CONSTRAINT fk_cliente FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON DELETE CASCADE,
  CONSTRAINT fk_servico FOREIGN KEY (servico_id) REFERENCES servicos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
