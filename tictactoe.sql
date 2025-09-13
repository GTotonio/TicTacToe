-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 15-Maio-2019 às 14:54
-- Versão do servidor: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tictactoe`
--
CREATE DATABASE IF NOT EXISTS `tictactoe` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `tictactoe`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rounds`
--

CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `starter` char(1) NOT NULL,
  `difficulty` char(1) NOT NULL,
  `winner` char(1) NOT NULL,
  `win_option` char(1) NOT NULL,
  `win_key` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Extraindo dados da tabela `rounds`
--

INSERT INTO `rounds` (`id`, `starter`, `difficulty`, `winner`, `win_option`, `win_key`, `timestamp`) VALUES
(1, 'H', 'M', 'H', 'C', 1, '2019-05-15 12:47:48'),
(2, 'H', 'M', 'H', 'D', 1, '2019-05-15 12:47:56'),
(3, 'H', 'M', '', '', 0, '2019-05-15 12:48:08'),
(4, 'H', 'H', '', '', 0, '2019-05-15 12:48:18'),
(5, 'H', 'H', '', '', 0, '2019-05-15 12:48:27'),
(6, 'H', 'H', 'M', 'L', 2, '2019-05-15 12:48:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `turns`
--

CREATE TABLE IF NOT EXISTS `turns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL,
  `char` char(1) NOT NULL,
  `position` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Extraindo dados da tabela `turns`
--

INSERT INTO `turns` (`id`, `round_id`, `char`, `position`, `timestamp`) VALUES
(1, 1, 'O', 6, '2019-05-15 12:47:51'),
(2, 1, 'X', 4, '2019-05-15 12:47:51'),
(3, 1, 'O', 0, '2019-05-15 12:47:53'),
(4, 1, 'X', 1, '2019-05-15 12:47:53'),
(5, 1, 'X', 7, '2019-05-15 12:47:54'),
(6, 2, 'X', 4, '2019-05-15 12:47:58'),
(7, 2, 'O', 1, '2019-05-15 12:47:58'),
(8, 2, 'X', 8, '2019-05-15 12:48:01'),
(9, 2, 'O', 2, '2019-05-15 12:48:01'),
(10, 2, 'X', 3, '2019-05-15 12:48:03'),
(11, 2, 'O', 5, '2019-05-15 12:48:03'),
(12, 2, 'X', 0, '2019-05-15 12:48:06'),
(13, 4, 'O', 3, '2019-05-15 12:48:20'),
(14, 4, 'X', 4, '2019-05-15 12:48:21'),
(15, 4, 'X', 2, '2019-05-15 12:48:22'),
(16, 4, 'O', 6, '2019-05-15 12:48:22'),
(17, 4, 'O', 1, '2019-05-15 12:48:23'),
(18, 4, 'X', 0, '2019-05-15 12:48:23'),
(19, 4, 'X', 7, '2019-05-15 12:48:25'),
(20, 4, 'O', 8, '2019-05-15 12:48:25'),
(21, 4, 'X', 5, '2019-05-15 12:48:26'),
(22, 5, 'X', 4, '2019-05-15 12:48:29'),
(23, 5, 'O', 7, '2019-05-15 12:48:29'),
(24, 5, 'X', 2, '2019-05-15 12:48:31'),
(25, 5, 'O', 6, '2019-05-15 12:48:31'),
(26, 5, 'X', 8, '2019-05-15 12:48:33'),
(27, 5, 'O', 5, '2019-05-15 12:48:33'),
(28, 5, 'X', 1, '2019-05-15 12:48:36'),
(29, 5, 'O', 0, '2019-05-15 12:48:36'),
(30, 5, 'X', 3, '2019-05-15 12:48:37'),
(31, 6, 'X', 4, '2019-05-15 12:48:40'),
(32, 6, 'O', 8, '2019-05-15 12:48:40'),
(33, 6, 'X', 5, '2019-05-15 12:48:41'),
(34, 6, 'O', 3, '2019-05-15 12:48:41'),
(35, 6, 'X', 2, '2019-05-15 12:48:43'),
(36, 6, 'O', 6, '2019-05-15 12:48:44'),
(37, 6, 'X', 1, '2019-05-15 12:48:45'),
(38, 6, 'O', 7, '2019-05-15 12:48:45');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
