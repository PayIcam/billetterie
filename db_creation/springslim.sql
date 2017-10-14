-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 15 Avril 2017 à 11:32
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `Shotgunslim`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE IF NOT EXISTS `administrateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(55) NOT NULL,
  `prenom` varchar(55) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` int(2) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `email`, `password`, `nom`, `prenom`, `online`, `role_id`) VALUES
(1, 'antoine.giraud@xxx', 'xxx', 'Giraud', 'Antoine', 1, 1),
(2, 'thibaut.de-gouberville@2018.icam.fr', '5a62f05f47bb9f8b767e198c6ed6044f', 'De Gouberville', 'Thibaut', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `configs`
--

INSERT INTO `configs` (`name`, `value`) VALUES
('authentification', '1'),
('maintenance', ''),
('websitename', 'Shotgun Festival');

-- --------------------------------------------------------

--
-- Structure de la table `entrees`
--

CREATE TABLE IF NOT EXISTS `entrees` (
  `guest_id` int(11) NOT NULL,
  `arrive` int(11) NOT NULL,
  `heure_arrive` datetime NOT NULL,
  PRIMARY KEY (`guest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `guests`
--

CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `is_icam` int(11) NOT NULL,
  `promo` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `inscription` datetime NOT NULL,
  `sexe` int(11) NOT NULL,
  `bracelet_id` int(11) NOT NULL,
  `paiement` varchar(50) NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Contenu de la table `guests`
--

INSERT INTO `guests` (`id`, `nom`, `prenom`, `is_icam`, `promo`, `email`, `telephone`, `inscription`, `sexe`, `bracelet_id`, `paiement`, `price`) VALUES
(47, 'Bon', 'Jean', 1, '118', 'jean.bon@2018.icam.fr', '', '2017-04-15 10:28:18', 1, 0, 'espece', 15),
(48, 'Tille', 'Jean', 0, '', '', '', '2017-04-15 10:28:18', 1, 0, 'espece', 15),
(49, 'Pide', 'Stu', 0, '', '', '', '2017-04-15 10:28:18', 1, 0, 'espece', 15),
(50, 'Nard', 'Ca', 1, '117', 'ca.nard@2017.icam.fr', '', '2017-04-15 10:41:31', 1, 0, 'espece', 15);

-- --------------------------------------------------------

--
-- Structure de la table `icam_has_guest`
--

CREATE TABLE IF NOT EXISTS `icam_has_guest` (
  `icam_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  PRIMARY KEY (`icam_id`,`guest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `icam_has_guest`
--

INSERT INTO `icam_has_guest` (`icam_id`, `guest_id`) VALUES
(47, 48),
(47, 49);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `level` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `level`) VALUES
(1, 'Administrateur', 'admin', 2),
(2, 'Membre', 'member', 1),
(3, 'Non inscrit', 'non-inscrit', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
