-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 11 mars 2024 à 17:53
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `rettiwt`
--

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `author`, `title`, `text`, `date`) VALUES
(1, 1, 'Je suis trop beau', 'Je suis grand blond j\'ai des magnifiques yeux marrons je suis vraiment trop beau', '2024-03-11 00:00:00'),
(2, 1, 'Je suis pas si beau enfaite', 'Je complexe on m\'a quitter', '2024-03-11 00:00:00'),
(3, 2, 'J\'ai fait top 1', '40 kills les mecs', '2024-03-11 00:00:00'),
(4, 3, 'Je suis le numéro 3', 'Le 3ieme gars a avoir était crée', '2024-03-11 00:00:00'),
(5, 4, 'Soirée ce soir les mecs', 'Ramener des tichobelo', '2024-03-11 00:00:00'),
(6, 5, 'Paris < Marseille', 'C\'est vrai les copaing', '2024-03-11 00:00:00'),
(7, 6, 'Je suis le numéro 6', 'Le 6ieme gars a avoir était crée', '2024-03-11 00:00:00'),
(8, 7, 'Je suis le numéro 7', 'Le 7ieme gars a avoir était crée', '2024-03-11 00:00:00'),
(9, 8, 'Je suis le numéro 8', 'Le 8ieme gars a avoir était crée', '2024-03-11 00:00:00'),
(10, 9, 'Je suis le numéro 9', 'Le 9ieme gars a avoir était crée', '2024-03-11 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profil`
--

INSERT INTO `profil` (`id`, `username`, `email`, `password`) VALUES
(1, 'tiger123', 'tiger123@example.com', 'tiger123pwd'),
(2, 'lion456', 'lion456@example.com', 'lion456pwd'),
(3, 'elephant789', 'elephant789@example.com', 'elephant789pwd'),
(4, 'monkey012', 'monkey012@example.com', 'monkey012pwd'),
(5, 'giraffe345', 'giraffe345@example.com', 'giraffe345pwd'),
(6, 'zebra678', 'zebra678@example.com', 'zebra678pwd'),
(7, 'panda901', 'panda901@example.com', 'panda901pwd'),
(8, 'koala234', 'koala234@example.com', 'koala234pwd'),
(9, 'cheetah567', 'cheetah567@example.com', 'cheetah567pwd'),
(10, 'rhino890', 'rhino890@example.com', 'rhino890pwd'),
(11, 'Charles', 'didi831313@gmail.com', '12345678'),
(12, 'Pierrot2000', 'LeP@gmail.com', '12345678');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author`);

--
-- Index pour la table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `author_id` FOREIGN KEY (`author`) REFERENCES `profil` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
