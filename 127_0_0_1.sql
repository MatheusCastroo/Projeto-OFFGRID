-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/05/2026 às 02:00
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `arenas_mvp`
--
CREATE DATABASE IF NOT EXISTS `arenas_mvp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `arenas_mvp`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena`
--

CREATE TABLE `arena` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `endereco` varchar(150) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `plano_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `arena`
--

INSERT INTO `arena` (`id`, `nome`, `endereco`, `usuario_id`, `plano_id`) VALUES
(1, 'Matheus', 'avenida tste', NULL, NULL),
(2, 'Matheus', 'avenida tste', NULL, NULL),
(3, 'Matheus', 'avenida tste', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_imagens`
--

CREATE TABLE `arena_imagens` (
  `id` int(11) NOT NULL,
  `arena_id` int(11) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `bo_principal` tinyint(1) DEFAULT 0,
  `ordem` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `quadra_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `disponivel` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quadras`
--

CREATE TABLE `quadras` (
  `id` int(11) NOT NULL,
  `arena_id` int(11) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `modalidades_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arena`
--
ALTER TABLE `arena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `plano_id` (`plano_id`);

--
-- Índices de tabela `arena_imagens`
--
ALTER TABLE `arena_imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arena_id` (`arena_id`);

--
-- Índices de tabela `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quadra_id` (`quadra_id`);

--
-- Índices de tabela `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `quadras`
--
ALTER TABLE `quadras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arena_id` (`arena_id`),
  ADD KEY `modalidades_id` (`modalidades_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arena`
--
ALTER TABLE `arena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `arena_imagens`
--
ALTER TABLE `arena_imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `quadras`
--
ALTER TABLE `quadras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `arena`
--
ALTER TABLE `arena`
  ADD CONSTRAINT `arena_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `arena_ibfk_2` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

--
-- Restrições para tabelas `arena_imagens`
--
ALTER TABLE `arena_imagens`
  ADD CONSTRAINT `arena_imagens_ibfk_1` FOREIGN KEY (`arena_id`) REFERENCES `arena` (`id`);

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`quadra_id`) REFERENCES `quadras` (`id`);

--
-- Restrições para tabelas `quadras`
--
ALTER TABLE `quadras`
  ADD CONSTRAINT `quadras_ibfk_1` FOREIGN KEY (`arena_id`) REFERENCES `arena` (`id`),
  ADD CONSTRAINT `quadras_ibfk_2` FOREIGN KEY (`modalidades_id`) REFERENCES `modalidades` (`id`);
--
-- Banco de dados: `calculadora_offgrid`
--
CREATE DATABASE IF NOT EXISTS `calculadora_offgrid` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `calculadora_offgrid`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bateria`
--

CREATE TABLE `bateria` (
  `sku` int(11) NOT NULL,
  `bateria_desc` varchar(255) NOT NULL,
  `tensao_nominal_vdc` decimal(5,2) NOT NULL,
  `capacidade_bateria` decimal(10,2) NOT NULL,
  `descarregar_percentual` decimal(5,2) NOT NULL,
  `rendimento_bateria` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `bateria`
--

INSERT INTO `bateria` (`sku`, `bateria_desc`, `tensao_nominal_vdc`, `capacidade_bateria`, `descarregar_percentual`, `rendimento_bateria`) VALUES
(13917, 'BATERIA SELADA 12V 9,0AH UP1290 F187 UNICOBA', 12.00, 9.00, 30.00, 90.00),
(14121, 'BATERIA SELADA 12V 7,0AH P/ NOBREAK UP1270E F187 UNICOBA', 12.00, 7.00, 30.00, 90.00),
(14372, 'BATERIA SELADA 12V 5,0AH UNICOBA', 12.00, 5.00, 30.00, 90.00),
(27548, 'BATERIA ESTACIONARIA 12V 60AH INTELBRAS', 12.00, 60.00, 30.00, 90.00),
(27612, 'BATERIA ESTACIONARIA 12V 45AH INTELBRAS', 12.00, 45.00, 30.00, 90.00),
(27919, 'BATERIA SELADA SOLAR 12V 220AH 2,64KW 12MS234 MOURA (B2)', 12.00, 220.00, 30.00, 90.00),
(28887, 'BATERIA ESTACIONARIA 12V 45AH MOURA', 12.00, 45.00, 30.00, 90.00),
(31457, 'BATERIA LITIO 48V 100AH UPLFP48100 3U RACK UNICOBA', 48.00, 100.00, 30.00, 90.00),
(31570, 'BATERIA SELADA 12V 45AH ESTACIONARIA UPMF1245 UNICOBA', 12.00, 45.00, 30.00, 90.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `controlador_carga`
--

CREATE TABLE `controlador_carga` (
  `sku` int(11) NOT NULL,
  `controlador` varchar(255) NOT NULL,
  `corrente_nominal` decimal(10,2) NOT NULL,
  `tensao_circuito_aberto` decimal(10,2) NOT NULL,
  `tensao_1_vdc` decimal(10,2) DEFAULT NULL,
  `tensao_2_vdc` decimal(10,2) DEFAULT NULL,
  `tensao_3_vdc` decimal(10,2) DEFAULT NULL,
  `tipo_controle` varchar(50) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `condicao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `controlador_carga`
--

INSERT INTO `controlador_carga` (`sku`, `controlador`, `corrente_nominal`, `tensao_circuito_aberto`, `tensao_1_vdc`, `tensao_2_vdc`, `tensao_3_vdc`, `tipo_controle`, `quantidade`, `condicao`) VALUES
(22776, 'CONTROLADOR DE CARGA MPPT 20A 12/24V DC XTRA2210N-XDS2 EPEVER', 20.00, 92.00, 12.00, 24.00, NULL, 'MPPT', 1, 0),
(22776, 'CONTROLADOR DE CARGA MPPT 20A 12/24V DC XTRA2210N-XDS2 EPEVER', 40.00, 184.00, 12.00, 24.00, NULL, 'MPPT', 2, 0),
(22776, 'CONTROLADOR DE CARGA MPPT 20A 12/24V DC XTRA2210N-XDS2 EPEVER', 60.00, 276.00, 12.00, 24.00, NULL, 'MPPT', 3, 0),
(22776, 'CONTROLADOR DE CARGA MPPT 20A 12/24V DC XTRA2210N-XDS2 EPEVER', 80.00, 368.00, 12.00, 24.00, NULL, 'MPPT', 4, 0),
(22777, 'CONTROLADOR DE CARGA MPPT 30A 12/24V DC XTRA3210N-XDS2 EPEVER', 30.00, 92.00, 12.00, 24.00, NULL, 'MPPT', 1, 0),
(22777, 'CONTROLADOR DE CARGA MPPT 30A 12/24V DC XTRA3210N-XDS2 EPEVER', 60.00, 184.00, 12.00, 24.00, NULL, 'MPPT', 2, 0),
(22777, 'CONTROLADOR DE CARGA MPPT 30A 12/24V DC XTRA3210N-XDS2 EPEVER', 90.00, 276.00, 12.00, 24.00, NULL, 'MPPT', 3, 0),
(22777, 'CONTROLADOR DE CARGA MPPT 30A 12/24V DC XTRA3210N-XDS2 EPEVER', 120.00, 368.00, 12.00, 24.00, NULL, 'MPPT', 4, 0),
(22778, 'CONTROLADOR DE CARGA MPPT 40A 12/24V DC XTRA4210N-XDS2 EPEVER', 40.00, 92.00, 12.00, 24.00, NULL, 'MPPT', 1, 0),
(22778, 'CONTROLADOR DE CARGA MPPT 40A 12/24V DC XTRA4210N-XDS2 EPEVER', 80.00, 184.00, 12.00, 24.00, NULL, 'MPPT', 2, 0),
(22778, 'CONTROLADOR DE CARGA MPPT 40A 12/24V DC XTRA4210N-XDS2 EPEVER', 120.00, 276.00, 12.00, 24.00, NULL, 'MPPT', 3, 0),
(22778, 'CONTROLADOR DE CARGA MPPT 40A 12/24V DC XTRA4210N-XDS2 EPEVER', 160.00, 368.00, 12.00, 24.00, NULL, 'MPPT', 4, 0),
(22779, 'CONTROLADOR DE CARGA MPPT 40A 12/24/36/48V DC XTRA4415N-XDS2 EPEVER', 40.00, 138.00, 12.00, 24.00, 48.00, 'MPPT', 1, 0),
(22779, 'CONTROLADOR DE CARGA MPPT 40A 12/24/36/48V DC XTRA4415N-XDS2 EPEVER', 80.00, 276.00, 12.00, 24.00, 48.00, 'MPPT', 2, 0),
(22779, 'CONTROLADOR DE CARGA MPPT 40A 12/24/36/48V DC XTRA4415N-XDS2 EPEVER', 120.00, 414.00, 12.00, 24.00, 48.00, 'MPPT', 3, 0),
(22779, 'CONTROLADOR DE CARGA MPPT 40A 12/24/36/48V DC XTRA4415N-XDS2 EPEVER', 160.00, 552.00, 12.00, 24.00, 48.00, 'MPPT', 4, 0),
(22780, 'CONTROLADOR DE CARGA MPPT 50A 12/24/36/48V DC TRACER 5415AN EPEVER', 50.00, 138.00, 12.00, 24.00, 48.00, 'MPPT', 1, 0),
(22780, 'CONTROLADOR DE CARGA MPPT 50A 12/24/36/48V DC TRACER 5415AN EPEVER', 100.00, 276.00, 12.00, 24.00, 48.00, 'MPPT', 2, 0),
(22780, 'CONTROLADOR DE CARGA MPPT 50A 12/24/36/48V DC TRACER 5415AN EPEVER', 150.00, 414.00, 12.00, 24.00, 48.00, 'MPPT', 3, 0),
(22780, 'CONTROLADOR DE CARGA MPPT 50A 12/24/36/48V DC TRACER 5415AN EPEVER', 200.00, 552.00, 12.00, 24.00, 48.00, 'MPPT', 4, 0),
(22781, 'CONTROLADOR DE CAGA MPPT 60A 12/24/36/48V DC TRACER 6415AN EPEVER', 60.00, 138.00, 12.00, 24.00, 48.00, 'MPPT', 1, 0),
(22781, 'CONTROLADOR DE CAGA MPPT 60A 12/24/36/48V DC TRACER 6415AN EPEVER', 120.00, 276.00, 12.00, 24.00, 48.00, 'MPPT', 2, 0),
(22781, 'CONTROLADOR DE CAGA MPPT 60A 12/24/36/48V DC TRACER 6415AN EPEVER', 180.00, 414.00, 12.00, 24.00, 48.00, 'MPPT', 3, 0),
(22781, 'CONTROLADOR DE CAGA MPPT 60A 12/24/36/48V DC TRACER 6415AN EPEVER', 240.00, 552.00, 12.00, 24.00, 48.00, 'MPPT', 4, 0),
(22782, 'CONTROLADOR DE CARGA MPPT 60A 12/24/36/48V DC TRACER 6420AN EPEVER', 60.00, 180.00, 12.00, 24.00, 48.00, 'MPPT', 1, 0),
(22782, 'CONTROLADOR DE CARGA MPPT 60A 12/24/36/48V DC TRACER 6420AN EPEVER', 120.00, 360.00, 12.00, 24.00, 48.00, 'MPPT', 2, 0),
(22782, 'CONTROLADOR DE CARGA MPPT 60A 12/24/36/48V DC TRACER 6420AN EPEVER', 180.00, 540.00, 12.00, 24.00, 48.00, 'MPPT', 3, 0),
(22782, 'CONTROLADOR DE CARGA MPPT 60A 12/24/36/48V DC TRACER 6420AN EPEVER', 240.00, 720.00, 12.00, 24.00, 48.00, 'MPPT', 4, 0),
(22783, 'CONTROLADOR DE CARGA MPPT 100A 12/24/36/48V DC TRACER 10415AN EPEVER', 100.00, 138.00, 12.00, 24.00, 48.00, 'MPPT', 1, 0),
(22783, 'CONTROLADOR DE CARGA MPPT 100A 12/24/36/48V DC TRACER 10415AN EPEVER', 200.00, 276.00, 12.00, 24.00, 48.00, 'MPPT', 2, 0),
(22783, 'CONTROLADOR DE CARGA MPPT 100A 12/24/36/48V DC TRACER 10415AN EPEVER', 300.00, 414.00, 12.00, 24.00, 48.00, 'MPPT', 3, 0),
(22783, 'CONTROLADOR DE CARGA MPPT 100A 12/24/36/48V DC TRACER 10415AN EPEVER', 400.00, 552.00, 12.00, 24.00, 48.00, 'MPPT', 4, 0),
(22785, 'CONTROLADOR DE CARGA 5A 12V LS0512E EPEVER', 5.00, 30.00, 12.00, NULL, NULL, 'PWM', 1, 0),
(22785, 'CONTROLADOR DE CARGA 5A 12V LS0512E EPEVER', 10.00, 60.00, 12.00, NULL, NULL, 'PWM', 2, 0),
(22785, 'CONTROLADOR DE CARGA 5A 12V LS0512E EPEVER', 15.00, 90.00, 12.00, NULL, NULL, 'PWM', 3, 0),
(22785, 'CONTROLADOR DE CARGA 5A 12V LS0512E EPEVER', 20.00, 120.00, 12.00, NULL, NULL, 'PWM', 4, 0),
(22786, 'CONTROLADOR DE CARGA 10A 12/24V LS1024EU EPEVER', 10.00, 50.00, 12.00, 24.00, NULL, 'PWM', 1, 0),
(22786, 'CONTROLADOR DE CARGA 10A 12/24V LS1024EU EPEVER', 20.00, 100.00, 12.00, 24.00, NULL, 'PWM', 2, 0),
(22786, 'CONTROLADOR DE CARGA 10A 12/24V LS1024EU EPEVER', 30.00, 150.00, 12.00, 24.00, NULL, 'PWM', 3, 0),
(22786, 'CONTROLADOR DE CARGA 10A 12/24V LS1024EU EPEVER', 40.00, 200.00, 12.00, 24.00, NULL, 'PWM', 4, 0),
(22790, 'CONTROLADOR DE CARGA PWM 20A 12/24V VS2024AU EPEVER', 20.00, 50.00, 12.00, 24.00, NULL, 'PWM', 1, 0),
(22790, 'CONTROLADOR DE CARGA PWM 20A 12/24V VS2024AU EPEVER', 40.00, 150.00, 12.00, 24.00, NULL, 'PWM', 2, 0),
(22790, 'CONTROLADOR DE CARGA PWM 20A 12/24V VS2024AU EPEVER', 60.00, 150.00, 12.00, 24.00, NULL, 'PWM', 3, 0),
(22790, 'CONTROLADOR DE CARGA PWM 20A 12/24V VS2024AU EPEVER', 80.00, 200.00, 12.00, 24.00, NULL, 'PWM', 4, 0),
(22791, 'CONTROLADOR DE CARGA PWM 30A 12/24V VS3024AU EPEVER', 30.00, 50.00, 12.00, 24.00, NULL, 'PWM', 1, 0),
(22791, 'CONTROLADOR DE CARGA PWM 30A 12/24V VS3024AU EPEVER', 60.00, 100.00, 12.00, 24.00, NULL, 'PWM', 2, 0),
(22791, 'CONTROLADOR DE CARGA PWM 30A 12/24V VS3024AU EPEVER', 90.00, 150.00, 12.00, 24.00, NULL, 'PWM', 3, 0),
(22791, 'CONTROLADOR DE CARGA PWM 30A 12/24V VS3024AU EPEVER', 120.00, 200.00, 12.00, 24.00, NULL, 'PWM', 4, 0),
(22792, 'CONTROLADOR DE CARGA PWM 45A 12/24V VS4524AU EPEVER', 45.00, 50.00, 12.00, 24.00, NULL, 'PWM', 1, 0),
(22792, 'CONTROLADOR DE CARGA PWM 45A 12/24V VS4524AU EPEVER', 90.00, 100.00, 12.00, 24.00, NULL, 'PWM', 2, 0),
(22792, 'CONTROLADOR DE CARGA PWM 45A 12/24V VS4524AU EPEVER', 135.00, 150.00, 12.00, 24.00, NULL, 'PWM', 3, 0),
(22792, 'CONTROLADOR DE CARGA PWM 45A 12/24V VS4524AU EPEVER', 180.00, 200.00, 12.00, 24.00, NULL, 'PWM', 4, 0),
(22793, 'CONTROLADOR DE CARGA PWM 60A 12/24V VS6024AU EPEVER', 60.00, 50.00, 12.00, 24.00, NULL, 'PWM', 1, 0),
(22793, 'CONTROLADOR DE CARGA PWM 60A 12/24V VS6024AU EPEVER', 120.00, 100.00, 12.00, 24.00, NULL, 'PWM', 2, 0),
(22793, 'CONTROLADOR DE CARGA PWM 60A 12/24V VS6024AU EPEVER', 180.00, 150.00, 12.00, 24.00, NULL, 'PWM', 3, 0),
(22793, 'CONTROLADOR DE CARGA PWM 60A 12/24V VS6024AU EPEVER', 240.00, 200.00, 12.00, 24.00, NULL, 'PWM', 4, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `disjuntor`
--

CREATE TABLE `disjuntor` (
  `sku` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` enum('AC','DC') DEFAULT NULL,
  `corrente_nominal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disjuntor`
--

INSERT INTO `disjuntor` (`sku`, `descricao`, `tipo`, `corrente_nominal`) VALUES
(21341, 'DISJUNTOR 2 POLOS 10A CURVA C G NXB-63 2P C10(R) CHINT (DJ210)', 'AC', 10.00),
(21342, 'DISJUNTOR 2 POLOS 16A CURVA C G NXB-63 2P C16(R) CHINT (DJ216)', 'AC', 16.00),
(21343, 'DISJUNTOR 2 POLOS 20A CURVA C G NXB-63 2P C20(R) CHINT (DJ220)', 'AC', 20.00),
(21344, 'DISJUNTOR 2 POLOS 25A CURVA C G NXB-63 2P C25(R) CHINT (DJ225)', 'AC', 25.00),
(21345, 'DISJUNTOR 2 POLOS 32A CURVA C G NXB-63 2P C32(R) CHINT (DJ232)', 'AC', 32.00),
(21346, 'DISJUNTOR 2 POLOS 40A CURVA C G NXB-63 2P C40(R) CHINT (DJ240)', 'AC', 40.00),
(21347, 'DISJUNTOR 2 POLOS 50A CURVA C G NXB-63 2P C50(R) CHINT (DJ250)', 'AC', 50.00),
(21348, 'DISJUNTOR 2 POLOS 63A CURVA C G NXB-63 2P C63(R) CHINT (DJ263)', 'AC', 63.00),
(26935, 'DISJUNTOR DC 6KA 2 POLOS 10A 500V G NB1-63DC 2P C10A DC500V 6KA(R) CHINT', 'DC', 10.00),
(26936, 'DISJUNTOR DC 6KA 2 POLOS 16A 500V G NB1-63DC 2P C16A DC500V 6KA(R) CHINT', 'DC', 16.00),
(26937, 'DISJUNTOR DC 6KA 2 POLOS 20A 500V G NB1-63DC 2P C20A DC500V 6KA(R) CHINT', 'DC', 20.00),
(26938, 'DISJUNTOR DC 6KA 2 POLOS 25A 500V G NB1-63DC 2P C25A DC500V 6KA(R) CHINT', 'DC', 25.00),
(26939, 'DISJUNTOR DC 6KA 2 POLOS 32A 500V G NB1-63DC 2P C32A DC500V 6KA(R) CHINT', 'DC', 32.00),
(26940, 'DISJUNTOR DC 6KA 2 POLOS 40A 500V G NB1-63DC 2P C40A DC500V 6KA(R) CHINT', 'DC', 40.00),
(26941, 'DISJUNTOR DC 6KA 2 POLOS 50A 500V G NB1-63DC 2P C50A DC500V 6KA(R) CHINT', 'DC', 50.00),
(26942, 'DISJUNTOR DC 6KA 2 POLOS 63A 500V G NB1-63DC 2P C63A DC500V 6KA(R) CHINT', 'DC', 63.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estrutura_solar`
--

CREATE TABLE `estrutura_solar` (
  `sku` int(11) NOT NULL,
  `estrutura_desc` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estrutura_solar`
--

INSERT INTO `estrutura_solar` (`sku`, `estrutura_desc`, `quantidade`) VALUES
(0, 'SEM ESTRUTURA SOLAR', 0),
(21749, 'ESTRUTURA 1 PLACA POSTE', 1),
(28521, 'ESTRUTURA 2 PLACA POSTE', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `inversor`
--

CREATE TABLE `inversor` (
  `sku` int(11) NOT NULL,
  `inversor` varchar(255) NOT NULL,
  `potencia_nominal` decimal(10,2) NOT NULL,
  `potencia_trabalho` decimal(10,2) NOT NULL,
  `tensao_entrada` decimal(10,2) NOT NULL,
  `tensao_saida` decimal(10,2) NOT NULL,
  `condicao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inversor`
--

INSERT INTO `inversor` (`sku`, `inversor`, `potencia_nominal`, `potencia_trabalho`, `tensao_entrada`, `tensao_saida`, `condicao`) VALUES
(22768, 'INVERSOR SENOIDAL 350W 12V/110V IP350-11 EPEVER', 350.00, 280.00, 12.00, 127.00, 0),
(22769, 'INVERSOR SENOIDAL 500W 12V/110V IP500-11 EPEVER', 500.00, 400.00, 12.00, 127.00, 0),
(22770, 'INVERSOR SENOIDAL 500W 12V/220V IP500-12 EPEVER', 500.00, 400.00, 12.00, 220.00, 0),
(22771, 'INVERSOR SENOIDAL 1500W 12V/110V IP1500-11 EPEVER', 1000.00, 1200.00, 12.00, 127.00, 0),
(25378, 'INVERSOR SENOIDAL 1000W 24V/110V IP1000-21-PLUS(T) EPEVER', 1000.00, 1000.00, 24.00, 127.00, 0),
(25379, 'INVERSOR SENOIDAL 2000W 24V/220V IP2000-22-PLUS(T) EPEVER', 2000.00, 2000.00, 24.00, 220.00, 0),
(25380, 'INVERSOR SENOIDAL 2000W 24V/110V IP2000-21-PLUS(T) EPEVER', 2000.00, 2000.00, 24.00, 127.00, 0),
(25381, 'INVERSOR SENOIDAL 4000W 48V/220V IP4000-42-PLUS(T) EPEVER', 4000.00, 4000.00, 48.00, 220.00, 0),
(25382, 'INVERSOR SENOIDAL 4000W 48V/110V IP4000-41-PLUS(T) EPEVER', 4000.00, 4000.00, 48.00, 127.00, 0),
(25413, 'INVERSOR SENOIDAL 1500W 12V/220V IP1500-12 EPEVER', 1500.00, 1200.00, 12.00, 220.00, 0),
(25414, 'INVERSOR SENOIDAL 2000W 24V/110V IP2000-21 EPEVER', 2000.00, 1600.00, 24.00, 127.00, 0),
(25415, 'INVERSOR SENOIDAL 2000W 24V/220V IP2000-22 EPEVER', 2000.00, 1600.00, 24.00, 220.00, 0),
(29763, 'INVERSOR SENOIDAL 2000W 48V/220V IP2000-42-PLUS(T) EPEVER', 2000.00, 2000.00, 48.00, 220.00, 0),
(31377, 'INVERSOR SENOIDAL 5000W 48V/220V IP5000-42-PLUS(T) EPEVER', 5000.00, 5000.00, 48.00, 220.00, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `operacao_sistema`
--

CREATE TABLE `operacao_sistema` (
  `id` int(11) NOT NULL,
  `valor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `operacao_sistema`
--

INSERT INTO `operacao_sistema` (`id`, `valor`) VALUES
(1, 12),
(2, 24),
(3, 48),
(4, 127),
(5, 220);

-- --------------------------------------------------------

--
-- Estrutura para tabela `placa_solar`
--

CREATE TABLE `placa_solar` (
  `sku` int(11) NOT NULL,
  `painel` varchar(255) NOT NULL,
  `potencia_max` int(11) NOT NULL,
  `tensao_circuito` decimal(10,2) NOT NULL,
  `corrente_curto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `placa_solar`
--

INSERT INTO `placa_solar` (`sku`, `painel`, `potencia_max`, `tensao_circuito`, `corrente_curto`) VALUES
(18450, 'MODULO FOTOVOLTAICO INTELBRAS 160W', 160, 21.60, 9.25),
(20745, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN 440W', 440, 48.70, 11.48),
(20746, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS3W-440MS', 440, 48.70, 11.48),
(22444, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SUNOVA 450W', 450, 50.40, 11.47),
(22473, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SINE ENERGY 500W', 500, 45.54, 13.72),
(22479, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SUNOVA SS-505-66MDH', 505, 45.52, 13.92),
(23069, 'PAINEL FOTOVOLTAICO MONOCRISTALINO AMERISOLAR AS-8M120-HC-605W', 605, 41.80, 18.50),
(23236, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS3W-455MS', 455, 46.90, 9.41),
(23237, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS6W-545MS', 545, 49.40, 13.95),
(23904, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SUNOVA SS-605-60MDH-G12', 605, 41.72, 18.57),
(24248, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS6W-550MS', 550, 49.60, 14.00),
(24533, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-SH144-540/M', 540, 49.70, 13.78),
(24534, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-SP144-550/M', 550, 50.10, 13.90),
(25220, 'PAINEL FOTOVOLTAICO BIFACIAL MONOCRISTALINO CANADIAN CS7N-655MB-AG', 655, 45.20, 22.12),
(25896, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS6W-545MS', 545, 49.40, 13.95),
(26035, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN 550W', 550, 49.60, 14.00),
(26036, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS6W-550MS', 550, 49.60, 14.00),
(26386, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE N-TYPE ZXM7-UHLD144-575/N', 575, 51.20, 14.25),
(26387, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE 575W', 575, 51.30, 14.29),
(26863, 'PAINEL FOTOVOLTAICO MONOCRISTALINO JA SOLAR 550W', 550, 49.90, 14.00),
(26890, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS7N-665MS', 665, 45.60, 18.51),
(27193, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-SHLD144-555/M', 555, 50.30, 13.96),
(27233, 'PAINEL FOTOVOLTAICO BIFACIAL MONOCRISTALINO CANADIAN CS7N-655MB-AG', 655, 45.20, 22.12),
(27254, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SINE ENERGY SN555-144M', 555, 49.98, 13.87),
(28138, 'PAINEL FOTOVOLTAICO MONOCRISTALINOSENGI SV1S72-550', 550, 49.90, 14.00),
(28233, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE N-TYPE ZXMR-UHLD132-560W', 560, 50.60, 14.06),
(28824, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SINE ENERGY 555W', 555, 49.90, 14.00),
(28825, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SINE ENERGY SN555-144M', 555, 49.98, 13.87),
(28864, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE 555W', 555, 50.30, 13.96),
(28865, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-NPLD144-555/M', 555, 50.30, 13.96),
(29546, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SINE ENERGY N-TYPE SN575-144MT', 575, 50.88, 14.36),
(29547, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-UHLD144-555/M', 555, 50.30, 13.96),
(30214, 'PAINEL FOTOVOLTAICO MONOCRISTALINO HANERSUN HN21-66H670W', 670, 45.80, 18.55),
(30448, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE ZXM7-NPLD144-555/M', 555, 50.30, 13.96),
(30510, 'PAINEL FOTOVOLTAICO MONOCRISTALINO SUNOVA SS-610-66MDHG10', 610, 49.00, 15.86),
(30520, 'PAINEL FOTOVOLTAICO BIFACIAL MONOCRISTALINO ZNSHINE ZXM8-TPLDD132-660/M', 660, 45.60, 22.96),
(30606, 'PAINEL FOTOVOLTAICO MONOCRISTALINO HT-SAAE HT66-18X 505W', 505, 45.70, 13.99),
(30720, 'PAINEL FOTOVOLTAICO BIFACIAL MONOCRISTALINO CANADIAN PERC 7N-MB-AG 660W', 660, 45.40, 22.16),
(31169, 'PAINEL FOTOVOLTAICO MONOCRISTALINO ZNSHINE N-TYPE ZXMR-UHLD132-570/', 570, 46.80, 15.49),
(31228, 'PAINEL FOTOVOLTAICO MONOCRISTALINO CANADIAN CS6W-555MS-1.5K M/6WMS', 555, 49.80, 14.05);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tensoes`
--

CREATE TABLE `tensoes` (
  `id` int(11) NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tensoes`
--

INSERT INTO `tensoes` (`id`, `valor`) VALUES
(4, '127_sistema'),
(1, '12_sistema'),
(5, '220_sistema'),
(2, '24_sistema'),
(3, '48_sistema');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bateria`
--
ALTER TABLE `bateria`
  ADD PRIMARY KEY (`sku`);

--
-- Índices de tabela `controlador_carga`
--
ALTER TABLE `controlador_carga`
  ADD PRIMARY KEY (`sku`,`quantidade`);

--
-- Índices de tabela `disjuntor`
--
ALTER TABLE `disjuntor`
  ADD PRIMARY KEY (`sku`);

--
-- Índices de tabela `estrutura_solar`
--
ALTER TABLE `estrutura_solar`
  ADD PRIMARY KEY (`sku`);

--
-- Índices de tabela `inversor`
--
ALTER TABLE `inversor`
  ADD PRIMARY KEY (`sku`);

--
-- Índices de tabela `operacao_sistema`
--
ALTER TABLE `operacao_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `placa_solar`
--
ALTER TABLE `placa_solar`
  ADD PRIMARY KEY (`sku`);

--
-- Índices de tabela `tensoes`
--
ALTER TABLE `tensoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `valor` (`valor`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `operacao_sistema`
--
ALTER TABLE `operacao_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tensoes`
--
ALTER TABLE `tensoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Banco de dados: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Despejando dados para a tabela `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"calculadora_offgrid\",\"table\":\"operacao_sistema\"},{\"db\":\"calculadora_offgrid\",\"table\":\"bateria\"},{\"db\":\"calculadora_offgrid\",\"table\":\"disjuntor\"},{\"db\":\"calculadora_offgrid\",\"table\":\"controlador_carga\"},{\"db\":\"calculadora_offgrid\",\"table\":\"inversor\"},{\"db\":\"calculadora_offgrid\",\"table\":\"placa_solar\"},{\"db\":\"calculadora_offgrid\",\"table\":\"estrutura_solar\"},{\"db\":\"calculadora_offgrid\",\"table\":\"placar_solar\"},{\"db\":\"calculadora_offgrid\",\"table\":\"controlador_tensoes\"},{\"db\":\"projeto_resend\",\"table\":\"emails\"}]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Despejando dados para a tabela `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-05-26 00:00:21', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"pt_BR\",\"NavigationWidth\":338}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Estrutura para tabela `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Índices de tabela `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Índices de tabela `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Índices de tabela `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Índices de tabela `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Índices de tabela `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Índices de tabela `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Índices de tabela `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Índices de tabela `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Índices de tabela `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Índices de tabela `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Índices de tabela `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Índices de tabela `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Índices de tabela `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Índices de tabela `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Banco de dados: `projeto_resend`
--
CREATE DATABASE IF NOT EXISTS `projeto_resend` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `projeto_resend`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `applications`
--

CREATE TABLE `applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `api_key` varchar(128) NOT NULL,
  `resend_api_key` varchar(512) NOT NULL,
  `resend_from` varchar(255) NOT NULL DEFAULT 'onboarding@resend.dev',
  `logo_url` varchar(2048) DEFAULT NULL,
  `cor_primaria` varchar(32) NOT NULL DEFAULT '#6366f1',
  `cor_secundaria` varchar(32) NOT NULL DEFAULT '#111827',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `applications`
--

INSERT INTO `applications` (`id`, `nome`, `api_key`, `resend_api_key`, `resend_from`, `logo_url`, `cor_primaria`, `cor_secundaria`, `created_at`) VALUES
(1, 'MatheusApp', 'a4ff278f1ea9efbb968095416553941074500b2ebd7f6898', 're_eiw9YxJ5_5ScVEvrd54SoDvzAsAQVBPdx', 'onboarding@resend.dev', 'https://cloud.caw.agency/api/rcontent/bWF0aGV1cy9iV0YwYUdWMWN5OHhOemMxTURnMk9EWTJOalk1TFdSbGMybG5iaTFrWlMxc2IyZHZkR2x3YnkxMFpXTnViMnh2WjJsamIxOHhOREkwTFRNNS1kZXNpZ24tZGUtbG9nb3RpcG8tdGVjbm9sb2dpY29fMTQyNC0zOS5hdmlm?id=bWF0aGV1cy8xNzc1MDg2ODY2NjY5LWRlc2lnbi1kZS1sb2dvdGlwby10ZWNub2xvZ2ljb18xNDI0LTM5', '#6366f1', '#111827', '2026-04-01 23:41:09'),
(5, 'Ray', 'be8aea66dff893bd5899ba01e4109a7d53571c0863a0ac90', 're_bztnUSiG_9kJBY5zjEh2SXtseCZ9Xk9iP', 'ray@caw.agency', NULL, '#6366f1', '#111827', '2026-04-11 00:54:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `application_templates`
--

CREATE TABLE `application_templates` (
  `application_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `application_templates`
--

INSERT INTO `application_templates` (`application_id`, `template_id`, `created_at`) VALUES
(1, 1, '2026-04-11 00:51:33'),
(1, 6, '2026-04-11 00:57:29'),
(5, 6, '2026-04-11 00:57:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `logo_url` varchar(2048) DEFAULT NULL,
  `cor_primaria` varchar(32) NOT NULL DEFAULT '#2563eb',
  `cor_secundaria` varchar(32) NOT NULL DEFAULT '#1e293b',
  `layout_padrao` mediumtext DEFAULT NULL COMMENT 'HTML opcional com {{conteudo}}',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `emails`
--

CREATE TABLE `emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `destinatario` varchar(500) NOT NULL,
  `assunto` varchar(500) NOT NULL,
  `conteudo` mediumtext NOT NULL,
  `status` enum('PENDENTE','ENVIADO','ERRO') NOT NULL DEFAULT 'ENVIADO',
  `resposta_api` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emails`
--

INSERT INTO `emails` (`id`, `application_id`, `template_id`, `destinatario`, `assunto`, `conteudo`, `status`, `resposta_api`, `created_at`) VALUES
(1, 1, 1, 'matheusesteves1160@gmail.com', 'Bem vindo ao site', 'Seja muito bem vindo ao nosso site.', 'ENVIADO', '{\"http\":200,\"body\":\"{\\\"id\\\":\\\"d3cc590e-0988-4866-a437-f07b14972c34\\\"}\",\"decoded\":{\"id\":\"d3cc590e-0988-4866-a437-f07b14972c34\"}}', '2026-04-01 23:42:47'),
(2, 1, 1, 'matheusesteves1160@gmail.com', 'Bem vindo ao site', 'Seja muito bem vindo ao nosso site.', 'ENVIADO', '{\"http\":200,\"body\":\"{\\\"id\\\":\\\"c17b32fc-3822-425b-89b6-b789ab92f68b\\\"}\",\"decoded\":{\"id\":\"c17b32fc-3822-425b-89b6-b789ab92f68b\"}}', '2026-04-01 23:42:48'),
(3, 1, 1, 'rayarruda9876@gmail.com', 'Bem vindo ao site', 'Seja muito bem vindo ao nosso site.', 'ERRO', '{\"http\":403,\"body\":\"{\\\"statusCode\\\":403,\\\"name\\\":\\\"validation_error\\\",\\\"message\\\":\\\"You can only send testing emails to your own email address (matheusesteves1160@gmail.com). To send emails to other recipients, please verify a domain at resend.com\\/domains, and change the `from` address to an email using this domain.\\\"}\",\"decoded\":{\"statusCode\":403,\"name\":\"validation_error\",\"message\":\"You can only send testing emails to your own email address (matheusesteves1160@gmail.com). To send emails to other recipients, please verify a domain at resend.com\\/domains, and change the `from` address to an email using this domain.\"}}', '2026-04-11 00:45:14'),
(4, 5, 6, 'rayarruda9876@gmail.com', 'Bem vindo ao site', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html dir=\"ltr\" lang=\"en\">\r\n  <head>\r\n    <meta content=\"width=device-width\" name=\"viewport\" />\r\n    <meta content=\"text/html; charset=UTF-8\" http-equiv=\"Content-Type\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta content=\"IE=edge\" http-equiv=\"X-UA-Compatible\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta\r\n      content=\"telephone=no,address=no,email=no,date=no,url=no\"\r\n      name=\"format-detection\" />\r\n  </head>\r\n  <body>\r\n    <!--$--><!--html--><!--head--><!--body-->\r\n    <table\r\n      border=\"0\"\r\n      width=\"100%\"\r\n      cellpadding=\"0\"\r\n      cellspacing=\"0\"\r\n      role=\"presentation\"\r\n      align=\"center\">\r\n      <tbody>\r\n        <tr>\r\n          <td>\r\n            <table\r\n              align=\"center\"\r\n              width=\"100%\"\r\n              border=\"0\"\r\n              cellpadding=\"0\"\r\n              cellspacing=\"0\"\r\n              role=\"presentation\">\r\n              <tbody>\r\n                <tr>\r\n                  <td>\r\n                    <table\r\n                      width=\"100%\"\r\n                      border=\"0\"\r\n                      cellpadding=\"0\"\r\n                      cellspacing=\"0\"\r\n                      role=\"presentation\"\r\n                      style=\"width:100%\">\r\n                      <tbody>\r\n                        <tr>\r\n                          <td>\r\n                            <div\r\n                              style=\'margin:0;padding:0;background-color:rgb(255,255,255);margin-top:3rem;margin-bottom:3rem;margin-left:auto;margin-right:auto;font-family:ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"\'>\r\n                              <div\r\n                                style=\"margin:0;padding:0;display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0\">\r\n                                <p style=\"margin:0;padding:0\">\r\n                                  🎉 Bem-vindo! Suas credenciais de acesso estão\r\n                                  aqui\r\n                                </p>\r\n                                <div style=\"margin:0;padding:0\">\r\n                                  <p style=\"margin:0;padding:0\">\r\n                                     ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿\r\n                                  </p>\r\n                                </div>\r\n                              </div>\r\n                              <table\r\n                                align=\"center\"\r\n                                width=\"100%\"\r\n                                border=\"0\"\r\n                                cellpadding=\"0\"\r\n                                cellspacing=\"0\"\r\n                                role=\"presentation\"\r\n                                style=\"margin-top:40px;margin-right:auto;margin-bottom:40px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem;width:465px;max-width:37.5em\">\r\n                                <tbody>\r\n                                  <tr>\r\n                                    <td>\r\n                                      <tr style=\"margin:0;padding:0;width:100%\">\r\n                                        <td\r\n                                          data-id=\"__react-email-column\"\r\n                                          style=\"margin:0;padding:0\">\r\n                                          <h1\r\n                                            style=\"margin:0;padding:0px;color:rgb(0,0,0);font-size:24px;font-weight:700;text-align:center;margin-top:30px;margin-bottom:30px;margin-left:0px;margin-right:0px\">\r\n                                            Bem-vindo(a) 🚀\r\n                                          </h1>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                            Olá,\r\n                                            <!-- -->Ray arruda<!-- -->\r\n                                          </p>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Estamos felizes por ter você\r\n                                            conosco! Seu pagamento foi\r\n                                            confirmado com sucesso. Aqui estão\r\n                                            suas credenciais de acesso:\r\n                                          </p>\r\n                                          <table\r\n                                            align=\"center\"\r\n                                            width=\"100%\"\r\n                                            border=\"0\"\r\n                                            cellpadding=\"0\"\r\n                                            cellspacing=\"0\"\r\n                                            role=\"presentation\"\r\n                                            style=\"margin-top:16px;margin-right:auto;margin-bottom:16px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;background-color:rgb(249,249,249);border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem\">\r\n                                            <tbody>\r\n                                              <tr>\r\n                                                <td>\r\n                                                  <tr\r\n                                                    style=\"margin:0;padding:0\">\r\n                                                    <td\r\n                                                      data-id=\"__react-email-column\"\r\n                                                      style=\"margin:0;padding:0\">\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                                        <strong>Email:</strong>\r\n                                                        <!-- -->rayarruda9876@gmail.com<!-- -->\r\n                                                      </p>\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                                        <strong>Senha:</strong>\r\n                                                        <!-- -->123<!-- -->\r\n                                                      </p>\r\n                                                    </td>\r\n                                                  </tr>\r\n                                                </td>\r\n                                              </tr>\r\n                                            </tbody>\r\n                                          </table>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Por favor, mantenha essas\r\n                                            informações em um lugar seguro e\r\n                                            altere sua senha no primeiro acesso.\r\n                                          </p>\r\n                                          <p style=\"margin:0;padding:0\">\r\n                                            <span\r\n                                              ><a\r\n                                                href=\"https://valorhora.jungnapratica.com.br//sign-in\"\r\n                                                rel=\"noopener noreferrer nofollow\"\r\n                                                style=\"color:rgb(255,255,255);text-decoration-line:none;background-color:rgb(0,0,0);border-radius:0.25rem;font-size:14px;font-weight:600;text-align:center;padding-top:12px;padding-bottom:12px;padding-left:20px;padding-right:20px;margin-top:16px;margin-bottom:16px;margin-left:auto;margin-right:auto;display:block;line-height:100%;text-decoration:none;max-width:100%;padding:12px 20px 12px 20px\"\r\n                                                target=\"_blank\"\r\n                                                >Acessar minha conta</a\r\n                                              ></span\r\n                                            >\r\n                                          </p>\r\n                                          <hr\r\n                                            style=\"width:100%;border:none;border-top:1px solid #eaeaea;border-width:1px;border-style:solid;border-color:rgb(234,234,234);margin-top:26px;margin-bottom:26px;margin-left:0px;margin-right:0px\" />\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(102,102,102);font-size:12px;line-height:24px\">\r\n                                            Se você não solicitou esta conta,\r\n                                            por favor ignore este e-mail ou\r\n                                            entre em contato com nosso suporte.\r\n                                          </p>\r\n                                        </td>\r\n                                      </tr>\r\n                                    </td>\r\n                                  </tr>\r\n                                </tbody>\r\n                              </table>\r\n                            </div>\r\n                            <p style=\"margin:0;padding:0\"><br /></p>\r\n                          </td>\r\n                        </tr>\r\n                      </tbody>\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </tbody>\r\n            </table>\r\n          </td>\r\n        </tr>\r\n      </tbody>\r\n    </table>\r\n    <!--/$-->\r\n  </body>\r\n</html>', 'ENVIADO', '{\"http\":200,\"body\":\"{\\\"id\\\":\\\"1995252d-d37a-4a24-af3c-81e90be7e571\\\"}\",\"decoded\":{\"id\":\"1995252d-d37a-4a24-af3c-81e90be7e571\"}}', '2026-04-11 00:58:12'),
(5, 5, 6, 'rayarruda9876@gmail.com', 'Bem vindo ao site', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html dir=\"ltr\" lang=\"en\">\r\n  <head>\r\n    <meta content=\"width=device-width\" name=\"viewport\" />\r\n    <meta content=\"text/html; charset=UTF-8\" http-equiv=\"Content-Type\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta content=\"IE=edge\" http-equiv=\"X-UA-Compatible\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta\r\n      content=\"telephone=no,address=no,email=no,date=no,url=no\"\r\n      name=\"format-detection\" />\r\n  </head>\r\n  <body>\r\n    <!--$--><!--html--><!--head--><!--body-->\r\n    <table\r\n      border=\"0\"\r\n      width=\"100%\"\r\n      cellpadding=\"0\"\r\n      cellspacing=\"0\"\r\n      role=\"presentation\"\r\n      align=\"center\">\r\n      <tbody>\r\n        <tr>\r\n          <td>\r\n            <table\r\n              align=\"center\"\r\n              width=\"100%\"\r\n              border=\"0\"\r\n              cellpadding=\"0\"\r\n              cellspacing=\"0\"\r\n              role=\"presentation\">\r\n              <tbody>\r\n                <tr>\r\n                  <td>\r\n                    <table\r\n                      width=\"100%\"\r\n                      border=\"0\"\r\n                      cellpadding=\"0\"\r\n                      cellspacing=\"0\"\r\n                      role=\"presentation\"\r\n                      style=\"width:100%\">\r\n                      <tbody>\r\n                        <tr>\r\n                          <td>\r\n                            <div\r\n                              style=\'margin:0;padding:0;background-color:rgb(255,255,255);margin-top:3rem;margin-bottom:3rem;margin-left:auto;margin-right:auto;font-family:ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"\'>\r\n                              <div\r\n                                style=\"margin:0;padding:0;display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0\">\r\n                                <p style=\"margin:0;padding:0\">\r\n                                  🎉 Bem-vindo! Suas credenciais de acesso estão\r\n                                  aqui\r\n                                </p>\r\n                                <div style=\"margin:0;padding:0\">\r\n                                  <p style=\"margin:0;padding:0\">\r\n                                     ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿\r\n                                  </p>\r\n                                </div>\r\n                              </div>\r\n                              <table\r\n                                align=\"center\"\r\n                                width=\"100%\"\r\n                                border=\"0\"\r\n                                cellpadding=\"0\"\r\n                                cellspacing=\"0\"\r\n                                role=\"presentation\"\r\n                                style=\"margin-top:40px;margin-right:auto;margin-bottom:40px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem;width:465px;max-width:37.5em\">\r\n                                <tbody>\r\n                                  <tr>\r\n                                    <td>\r\n                                      <tr style=\"margin:0;padding:0;width:100%\">\r\n                                        <td\r\n                                          data-id=\"__react-email-column\"\r\n                                          style=\"margin:0;padding:0\">\r\n                                          <h1\r\n                                            style=\"margin:0;padding:0px;color:rgb(0,0,0);font-size:24px;font-weight:700;text-align:center;margin-top:30px;margin-bottom:30px;margin-left:0px;margin-right:0px\">\r\n                                            Bem-vindo(a) 🚀\r\n                                          </h1>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                            Olá,\r\n                                            <!-- -->Ray Arruda<!-- -->\r\n                                          </p>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Estamos felizes por ter você\r\n                                            conosco! Seu pagamento foi\r\n                                            confirmado com sucesso. Aqui estão\r\n                                            suas credenciais de acesso:\r\n                                          </p>\r\n                                          <table\r\n                                            align=\"center\"\r\n                                            width=\"100%\"\r\n                                            border=\"0\"\r\n                                            cellpadding=\"0\"\r\n                                            cellspacing=\"0\"\r\n                                            role=\"presentation\"\r\n                                            style=\"margin-top:16px;margin-right:auto;margin-bottom:16px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;background-color:rgb(249,249,249);border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem\">\r\n                                            <tbody>\r\n                                              <tr>\r\n                                                <td>\r\n                                                  <tr\r\n                                                    style=\"margin:0;padding:0\">\r\n                                                    <td\r\n                                                      data-id=\"__react-email-column\"\r\n                                                      style=\"margin:0;padding:0\">\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                                        <strong>Email:</strong>\r\n                                                        <!-- -->rayarruda9876@gmail.com<!-- -->\r\n                                                      </p>\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                                        <strong>Senha:</strong>\r\n                                                        <!-- -->123<!-- -->\r\n                                                      </p>\r\n                                                    </td>\r\n                                                  </tr>\r\n                                                </td>\r\n                                              </tr>\r\n                                            </tbody>\r\n                                          </table>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Por favor, mantenha essas\r\n                                            informações em um lugar seguro e\r\n                                            altere sua senha no primeiro acesso.\r\n                                          </p>\r\n                                          <p style=\"margin:0;padding:0\">\r\n                                            <span\r\n                                              ><a\r\n                                                href=\"https://valorhora.jungnapratica.com.br//sign-in\"\r\n                                                rel=\"noopener noreferrer nofollow\"\r\n                                                style=\"color:rgb(255,255,255);text-decoration-line:none;background-color:rgb(0,0,0);border-radius:0.25rem;font-size:14px;font-weight:600;text-align:center;padding-top:12px;padding-bottom:12px;padding-left:20px;padding-right:20px;margin-top:16px;margin-bottom:16px;margin-left:auto;margin-right:auto;display:block;line-height:100%;text-decoration:none;max-width:100%;padding:12px 20px 12px 20px\"\r\n                                                target=\"_blank\"\r\n                                                >Acessar minha conta</a\r\n                                              ></span\r\n                                            >\r\n                                          </p>\r\n                                          <hr\r\n                                            style=\"width:100%;border:none;border-top:1px solid #eaeaea;border-width:1px;border-style:solid;border-color:rgb(234,234,234);margin-top:26px;margin-bottom:26px;margin-left:0px;margin-right:0px\" />\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(102,102,102);font-size:12px;line-height:24px\">\r\n                                            Se você não solicitou esta conta,\r\n                                            por favor ignore este e-mail ou\r\n                                            entre em contato com nosso suporte.\r\n                                          </p>\r\n                                        </td>\r\n                                      </tr>\r\n                                    </td>\r\n                                  </tr>\r\n                                </tbody>\r\n                              </table>\r\n                            </div>\r\n                            <p style=\"margin:0;padding:0\"><br /></p>\r\n                          </td>\r\n                        </tr>\r\n                      </tbody>\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </tbody>\r\n            </table>\r\n          </td>\r\n        </tr>\r\n      </tbody>\r\n    </table>\r\n    <!--/$-->\r\n  </body>\r\n</html>', 'ENVIADO', '{\"http\":200,\"body\":\"{\\\"id\\\":\\\"4ed0a153-d22c-4c8b-aa0d-d9a86cb60281\\\"}\",\"decoded\":{\"id\":\"4ed0a153-d22c-4c8b-aa0d-d9a86cb60281\"}}', '2026-04-11 01:00:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `provider` varchar(64) NOT NULL DEFAULT 'cloudinary',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `midias`
--

CREATE TABLE `midias` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `provider` varchar(64) NOT NULL DEFAULT 'cloudinary',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `templates`
--

CREATE TABLE `templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `assunto` varchar(500) NOT NULL,
  `html` mediumtext NOT NULL,
  `variaveis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variaveis`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `templates`
--

INSERT INTO `templates` (`id`, `nome`, `assunto`, `html`, `variaveis`, `created_at`) VALUES
(1, 'boas_vindas', 'Bem vindo ao site', 'Seja muito bem vindo ao nosso site.', NULL, '2026-04-01 23:42:11'),
(6, 'Bem vindo ao site', 'Bem vindo ao site', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html dir=\"ltr\" lang=\"en\">\r\n  <head>\r\n    <meta content=\"width=device-width\" name=\"viewport\" />\r\n    <meta content=\"text/html; charset=UTF-8\" http-equiv=\"Content-Type\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta content=\"IE=edge\" http-equiv=\"X-UA-Compatible\" />\r\n    <meta name=\"x-apple-disable-message-reformatting\" />\r\n    <meta\r\n      content=\"telephone=no,address=no,email=no,date=no,url=no\"\r\n      name=\"format-detection\" />\r\n  </head>\r\n  <body>\r\n    <!--$--><!--html--><!--head--><!--body-->\r\n    <table\r\n      border=\"0\"\r\n      width=\"100%\"\r\n      cellpadding=\"0\"\r\n      cellspacing=\"0\"\r\n      role=\"presentation\"\r\n      align=\"center\">\r\n      <tbody>\r\n        <tr>\r\n          <td>\r\n            <table\r\n              align=\"center\"\r\n              width=\"100%\"\r\n              border=\"0\"\r\n              cellpadding=\"0\"\r\n              cellspacing=\"0\"\r\n              role=\"presentation\">\r\n              <tbody>\r\n                <tr>\r\n                  <td>\r\n                    <table\r\n                      width=\"100%\"\r\n                      border=\"0\"\r\n                      cellpadding=\"0\"\r\n                      cellspacing=\"0\"\r\n                      role=\"presentation\"\r\n                      style=\"width:100%\">\r\n                      <tbody>\r\n                        <tr>\r\n                          <td>\r\n                            <div\r\n                              style=\'margin:0;padding:0;background-color:rgb(255,255,255);margin-top:3rem;margin-bottom:3rem;margin-left:auto;margin-right:auto;font-family:ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"\'>\r\n                              <div\r\n                                style=\"margin:0;padding:0;display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0\">\r\n                                <p style=\"margin:0;padding:0\">\r\n                                  🎉 Bem-vindo! Suas credenciais de acesso estão\r\n                                  aqui\r\n                                </p>\r\n                                <div style=\"margin:0;padding:0\">\r\n                                  <p style=\"margin:0;padding:0\">\r\n                                     ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿ ‌​‍‎‏﻿\r\n                                  </p>\r\n                                </div>\r\n                              </div>\r\n                              <table\r\n                                align=\"center\"\r\n                                width=\"100%\"\r\n                                border=\"0\"\r\n                                cellpadding=\"0\"\r\n                                cellspacing=\"0\"\r\n                                role=\"presentation\"\r\n                                style=\"margin-top:40px;margin-right:auto;margin-bottom:40px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem;width:465px;max-width:37.5em\">\r\n                                <tbody>\r\n                                  <tr>\r\n                                    <td>\r\n                                      <tr style=\"margin:0;padding:0;width:100%\">\r\n                                        <td\r\n                                          data-id=\"__react-email-column\"\r\n                                          style=\"margin:0;padding:0\">\r\n                                          <h1\r\n                                            style=\"margin:0;padding:0px;color:rgb(0,0,0);font-size:24px;font-weight:700;text-align:center;margin-top:30px;margin-bottom:30px;margin-left:0px;margin-right:0px\">\r\n                                            Bem-vindo(a) 🚀\r\n                                          </h1>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                            Olá,\r\n                                            <!-- -->{{userName}}<!-- -->\r\n                                          </p>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Estamos felizes por ter você\r\n                                            conosco! Seu pagamento foi\r\n                                            confirmado com sucesso. Aqui estão\r\n                                            suas credenciais de acesso:\r\n                                          </p>\r\n                                          <table\r\n                                            align=\"center\"\r\n                                            width=\"100%\"\r\n                                            border=\"0\"\r\n                                            cellpadding=\"0\"\r\n                                            cellspacing=\"0\"\r\n                                            role=\"presentation\"\r\n                                            style=\"margin-top:16px;margin-right:auto;margin-bottom:16px;margin-left:auto;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;background-color:rgb(249,249,249);border-width:1px;border-style:solid;border-color:rgb(234,234,234);border-radius:0.25rem\">\r\n                                            <tbody>\r\n                                              <tr>\r\n                                                <td>\r\n                                                  <tr\r\n                                                    style=\"margin:0;padding:0\">\r\n                                                    <td\r\n                                                      data-id=\"__react-email-column\"\r\n                                                      style=\"margin:0;padding:0\">\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px\">\r\n                                                        <strong>Email:</strong>\r\n                                                        <!-- -->{{userEmail}}<!-- -->\r\n                                                      </p>\r\n                                                      <p\r\n                                                        style=\"margin:0px;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                                        <strong>Senha:</strong>\r\n                                                        <!-- -->{{password}}<!-- -->\r\n                                                      </p>\r\n                                                    </td>\r\n                                                  </tr>\r\n                                                </td>\r\n                                              </tr>\r\n                                            </tbody>\r\n                                          </table>\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(0,0,0);font-size:14px;line-height:24px;margin-top:0.5rem\">\r\n                                            Por favor, mantenha essas\r\n                                            informações em um lugar seguro e\r\n                                            altere sua senha no primeiro acesso.\r\n                                          </p>\r\n                                          <p style=\"margin:0;padding:0\">\r\n                                            <span\r\n                                              ><a\r\n                                                href=\"https://valorhora.jungnapratica.com.br//sign-in\"\r\n                                                rel=\"noopener noreferrer nofollow\"\r\n                                                style=\"color:rgb(255,255,255);text-decoration-line:none;background-color:rgb(0,0,0);border-radius:0.25rem;font-size:14px;font-weight:600;text-align:center;padding-top:12px;padding-bottom:12px;padding-left:20px;padding-right:20px;margin-top:16px;margin-bottom:16px;margin-left:auto;margin-right:auto;display:block;line-height:100%;text-decoration:none;max-width:100%;padding:12px 20px 12px 20px\"\r\n                                                target=\"_blank\"\r\n                                                >Acessar minha conta</a\r\n                                              ></span\r\n                                            >\r\n                                          </p>\r\n                                          <hr\r\n                                            style=\"width:100%;border:none;border-top:1px solid #eaeaea;border-width:1px;border-style:solid;border-color:rgb(234,234,234);margin-top:26px;margin-bottom:26px;margin-left:0px;margin-right:0px\" />\r\n                                          <p\r\n                                            style=\"margin:16px 0;padding:0;color:rgb(102,102,102);font-size:12px;line-height:24px\">\r\n                                            Se você não solicitou esta conta,\r\n                                            por favor ignore este e-mail ou\r\n                                            entre em contato com nosso suporte.\r\n                                          </p>\r\n                                        </td>\r\n                                      </tr>\r\n                                    </td>\r\n                                  </tr>\r\n                                </tbody>\r\n                              </table>\r\n                            </div>\r\n                            <p style=\"margin:0;padding:0\"><br /></p>\r\n                          </td>\r\n                        </tr>\r\n                      </tbody>\r\n                    </table>\r\n                  </td>\r\n                </tr>\r\n              </tbody>\r\n            </table>\r\n          </td>\r\n        </tr>\r\n      </tbody>\r\n    </table>\r\n    <!--/$-->\r\n  </body>\r\n</html>', '[\"password\",\"userEmail\",\"userName\"]', '2026-04-11 00:57:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `template_bases`
--

CREATE TABLE `template_bases` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `html` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_applications_api_key` (`api_key`),
  ADD KEY `idx_applications_nome` (`nome`);

--
-- Índices de tabela `application_templates`
--
ALTER TABLE `application_templates`
  ADD PRIMARY KEY (`application_id`,`template_id`),
  ADD KEY `idx_at_template` (`template_id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_emails_tpl` (`template_id`),
  ADD KEY `idx_emails_app` (`application_id`),
  ADD KEY `idx_emails_created` (`created_at`);

--
-- Índices de tabela `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_media_app` (`application_id`);

--
-- Índices de tabela `midias`
--
ALTER TABLE `midias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mid_cliente` (`cliente_id`);

--
-- Índices de tabela `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `template_bases`
--
ALTER TABLE `template_bases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tb_cliente` (`cliente_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `midias`
--
ALTER TABLE `midias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `template_bases`
--
ALTER TABLE `template_bases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `application_templates`
--
ALTER TABLE `application_templates`
  ADD CONSTRAINT `fk_at_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_at_tpl` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `emails`
--
ALTER TABLE `emails`
  ADD CONSTRAINT `fk_emails_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_emails_tpl` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `midias`
--
ALTER TABLE `midias`
  ADD CONSTRAINT `fk_mid_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `template_bases`
--
ALTER TABLE `template_bases`
  ADD CONSTRAINT `fk_tb_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
--
-- Banco de dados: `ranksports`
--
CREATE DATABASE IF NOT EXISTS `ranksports` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ranksports`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arenas`
--

CREATE TABLE `arenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `cidade` varchar(120) NOT NULL,
  `estado` char(2) NOT NULL,
  `telefone` varchar(25) NOT NULL DEFAULT '',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'inativo',
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `plano_id` int(10) UNSIGNED DEFAULT NULL,
  `atualizado_em` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arenas`
--

INSERT INTO `arenas` (`id`, `user_id`, `nome`, `endereco`, `cidade`, `estado`, `telefone`, `latitude`, `longitude`, `status`, `usuario_id`, `plano_id`, `atualizado_em`) VALUES
(1, NULL, 'Arena Central', 'Rua das Quadras, 100', 'São Paulo', 'SP', '11999999999', NULL, NULL, 'ativo', 2, 2, '2026-04-23 20:46:28'),
(8, 12, 'Matheus Castro', '1521', 'Maringá - PR', 'PR', '', NULL, NULL, 'inativo', 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_images`
--

CREATE TABLE `arena_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `file_id` varchar(64) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `size_bytes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_images`
--

INSERT INTO `arena_images` (`id`, `arena_id`, `file_id`, `url`, `size_bytes`, `sort_order`, `created_at`) VALUES
(1, 8, '95bda1ce58d5fa660acfe06bc77717a7', 'http://localhost/RankkSports/uploads/arenas/95bda1ce58d5fa660acfe06bc77717a7.png', 55401, 10, '2026-04-18 10:49:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `arena_modalidade`
--

CREATE TABLE `arena_modalidade` (
  `id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `modalidade_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `arena_modalidade`
--

INSERT INTO `arena_modalidade` (`id`, `arena_id`, `modalidade_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 8, 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `assinaturas`
--

CREATE TABLE `assinaturas` (
  `id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `plano_id` int(10) UNSIGNED NOT NULL,
  `status` enum('ativa','cancelada','expirada') NOT NULL DEFAULT 'ativa',
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `assinaturas`
--

INSERT INTO `assinaturas` (`id`, `arena_id`, `plano_id`, `status`, `data_inicio`, `data_fim`) VALUES
(1, 1, 2, 'ativa', '2026-04-01', '2027-04-01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos`
--

CREATE TABLE `fotos` (
  `id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `caminho` varchar(500) NOT NULL,
  `bo_principal` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `fotos`
--

INSERT INTO `fotos` (`id`, `arena_id`, `caminho`, `bo_principal`) VALUES
(1, 1, 'uploads/arenas/1/demo.svg', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `quadra_id` int(10) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `categoria` enum('iniciante','intermediario','avancado','livre') NOT NULL DEFAULT 'livre',
  `vagas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `horarios`
--

INSERT INTO `horarios` (`id`, `quadra_id`, `data`, `hora_inicio`, `hora_fim`, `categoria`, `vagas`, `disponivel`) VALUES
(1, 1, '2026-04-02', '08:00:00', '10:00:00', 'intermediario', 10, 1),
(2, 1, '2026-04-02', '14:00:00', '16:00:00', 'livre', 8, 1),
(3, 2, '2026-04-03', '19:00:00', '21:00:00', 'avancado', 6, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `modalidades`
--

INSERT INTO `modalidades` (`id`, `nome`) VALUES
(4, 'Basquete'),
(6, 'Beach Tennis'),
(1, 'Futebol'),
(2, 'Futsal'),
(5, 'Tênis'),
(3, 'Vôlei');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `destaque` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = plano popular em destaque'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `descricao`, `valor`, `destaque`) VALUES
(1, 'Básico', 'Listagem e até 5 fotos.', 49.90, 0),
(2, 'Popular', 'Destaque na busca e fotos ilimitadas.', 99.90, 1),
(3, 'Premium', 'Suporte prioritário e analytics.', 199.90, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(64) NOT NULL,
  `name` varchar(120) NOT NULL,
  `price_cents` int(11) NOT NULL DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `plans`
--

INSERT INTO `plans` (`id`, `slug`, `name`, `price_cents`, `features`, `created_at`) VALUES
(1, 'free', 'Gratuito', 0, '{\"max_arenas\": 1, \"max_images\": 5, \"max_schedule_slots\": 10, \"search_listing\": true, \"highlight\": false, \"external_link\": false}', '2026-04-16 19:21:57'),
(2, 'plus', 'Plus', 9990, '{\"max_arenas\": 1, \"max_images\": null, \"max_schedule_slots\": null, \"search_listing\": true, \"highlight\": true, \"external_link\": true, \"future_online_booking\": true}', '2026-04-16 19:21:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `quadras`
--

CREATE TABLE `quadras` (
  `id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `modalidade_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `quadras`
--

INSERT INTO `quadras` (`id`, `arena_id`, `nome`, `modalidade_id`) VALUES
(1, 1, 'Quadra 1', 1),
(2, 1, 'Quadra Futsal', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `arena_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `status` enum('active','canceled','expired') NOT NULL DEFAULT 'active',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `arena_id`, `plan_id`, `status`, `expires_at`, `created_at`) VALUES
(1, 8, 1, 'active', NULL, '2026-04-18 10:49:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('athlete','arena_owner','admin') NOT NULL DEFAULT 'athlete',
  `city` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `type`, `city`, `created_at`) VALUES
(12, 'matheuscastro1160@gmail.com', '$2y$10$JIHCPaC8agvaZVKFVFFkiuGfosSdA254AVBX7Yq9Gu5MxR3Mwxsgy', 'Matheus Castro', 'arena_owner', 'Maringá - PR', '2026-04-18 10:49:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `role` enum('admin','arena') NOT NULL DEFAULT 'arena',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `cidade` varchar(120) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `role`, `criado_em`, `cidade`, `estado`) VALUES
(1, 'Administrador', 'admin@ranksports.local', '$2y$10$I6xkx9FO67lW2T2SYdvbw.V.b50TIPuhXnSQRI2KKGoaZirlakh7u', 'admin', '2026-04-01 21:13:18', NULL, NULL),
(2, 'Arena Demo', 'arena@ranksports.local', '$2y$10$adhY/cByoGx77kTWuRCGVecl40kTaScrCFc7Qn4T68zcGL8Ni4twG', 'arena', '2026-04-01 21:13:18', NULL, NULL),
(6, 'Matheus Castro', 'matheuscastro1160@gmail.com', '$2y$10$JIHCPaC8agvaZVKFVFFkiuGfosSdA254AVBX7Yq9Gu5MxR3Mwxsgy', 'arena', '2026-04-18 10:49:46', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arenas`
--
ALTER TABLE `arenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arenas_usuario_id` (`usuario_id`),
  ADD KEY `arenas_plano_id` (`plano_id`),
  ADD KEY `arenas_status_cidade` (`status`,`cidade`),
  ADD KEY `idx_arenas_user` (`user_id`);

--
-- Índices de tabela `arena_images`
--
ALTER TABLE `arena_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arena_images_arena_file` (`arena_id`,`file_id`),
  ADD KEY `idx_arena_images_arena_sort` (`arena_id`,`sort_order`);

--
-- Índices de tabela `arena_modalidade`
--
ALTER TABLE `arena_modalidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `arena_modal_uk` (`arena_id`,`modalidade_id`),
  ADD KEY `am_modalidade` (`modalidade_id`);

--
-- Índices de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assinaturas_arena` (`arena_id`),
  ADD KEY `assinaturas_plano` (`plano_id`),
  ADD KEY `assinaturas_status` (`status`);

--
-- Índices de tabela `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fotos_arena` (`arena_id`),
  ADD KEY `fotos_principal` (`arena_id`,`bo_principal`);

--
-- Índices de tabela `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `horarios_quadra_data` (`quadra_id`,`data`),
  ADD KEY `horarios_disponivel` (`disponivel`);

--
-- Índices de tabela `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modalidades_nome` (`nome`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_plans_slug` (`slug`);

--
-- Índices de tabela `quadras`
--
ALTER TABLE `quadras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quadras_arena` (`arena_id`),
  ADD KEY `quadras_modalidade` (`modalidade_id`);

--
-- Índices de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sub_arena` (`arena_id`),
  ADD KEY `idx_sub_plan` (`plan_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_type` (`type`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arenas`
--
ALTER TABLE `arenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `arena_images`
--
ALTER TABLE `arena_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `arena_modalidade`
--
ALTER TABLE `arena_modalidade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `quadras`
--
ALTER TABLE `quadras`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `arenas`
--
ALTER TABLE `arenas`
  ADD CONSTRAINT `arenas_plano_fk` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `arenas_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_arenas_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `arena_images`
--
ALTER TABLE `arena_images`
  ADD CONSTRAINT `fk_arena_images_arena` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `arena_modalidade`
--
ALTER TABLE `arena_modalidade`
  ADD CONSTRAINT `am_arena_fk` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `am_modal_fk` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD CONSTRAINT `assinaturas_arena_fk` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assinaturas_plano_fk` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

--
-- Restrições para tabelas `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fotos_arena_fk` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_quadra_fk` FOREIGN KEY (`quadra_id`) REFERENCES `quadras` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `quadras`
--
ALTER TABLE `quadras`
  ADD CONSTRAINT `quadras_arena_fk` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quadras_modal_fk` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`);

--
-- Restrições para tabelas `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_sub_arena` FOREIGN KEY (`arena_id`) REFERENCES `arenas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`);
--
-- Banco de dados: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
