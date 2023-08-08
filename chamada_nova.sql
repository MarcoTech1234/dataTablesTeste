-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26-Jul-2023 às 02:51
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tcc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `chamada`
--

CREATE TABLE `chamada` (
  `mensagem` varchar(255) NOT NULL,
  `id_CH_equipamento` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `chamada`
--

INSERT INTO `chamada` (`mensagem`, `id_CH_equipamento`, `date`, `id`) VALUES
('muito burro', 0, '2023-07-24 23:35:31', 1),
('muito burro', 0, '2023-07-24 23:35:31', 2),
('paulo: ', 0, '2023-07-24 18:36:20', 3),
('paulo: ', 0, '2023-07-24 18:38:14', 4),
('paulo: ', 0, '2023-07-24 18:39:43', 5),
('paulo: Serio ?', 0, '2023-07-24 18:39:43', 6),
('paulo: alo', 0, '2023-07-24 20:45:02', 7),
('paulo: ', 0, '2023-07-24 21:27:54', 8),
('paulo: ', 0, '2023-07-24 21:27:54', 9),
('paulo: ', 0, '2023-07-24 21:27:54', 10),
('paulo: Se funcionar isso so precisa de testar com mais bancos', 0, '2023-07-24 21:32:38', 11),
('hello: as', 0, '2023-07-24 21:39:58', 12),
('Senhor Jesus: ta dando', 3, '2023-07-24 22:05:24', 13),
('Senhor Jesus: qwqwqwqw', 1, '2023-07-24 22:09:04', 14),
('Senhor Jesus: fds futebol feminino', 1, '2023-07-24 22:09:39', 15),
('Senhor Jesus: alo', 2, '2023-07-24 22:14:02', 16),
('equipamento numero 1', 0, '2023-07-26 02:10:42', 17),
('equipamento numero 1', 0, '2023-07-26 02:10:42', 18),
('asas: asd', 1, '2023-07-25 21:38:45', 19);

-- --------------------------------------------------------

--
-- Estrutura da tabela `equipamento`
--

CREATE TABLE `equipamento` (
  `nome` varchar(255) NOT NULL,
  `status` varchar(80) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `equipamento`
--

INSERT INTO `equipamento` (`nome`, `id`) VALUES
('asasas', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `chamada`
--
ALTER TABLE `chamada`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `equipamento`
--
ALTER TABLE `equipamento`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `chamada`
--
ALTER TABLE `chamada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `equipamento`
--
ALTER TABLE `equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
