SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databatse: `matcha`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
    `id` int(11) AUTO_INCREMENT,
    `pseudo` VARCHAR(40),
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL DEFAULT 'jgroc2s@free.fr',
    `token` VARCHAR(255) NOT NULL DEFAULT 'auie',
    `activ` BOOL NOT NULL DEFAULT true,
    `forname` VARCHAR(255) NOT NULL DEFAULT 'John',
    `name` VARCHAR(255) NOT NULL DEFAULT 'Doe',
    `biography` TEXT,
    `birthdate` YEAR NOT NULL DEFAULT '00',
    `geolocalisation` tinyint NOT NULL DEFAULT 1,
    `gender` ENUM('Rick','Morty','Summer','Beth','Jerry') NOT NULL DEFAULT 'Rick',
    `sexuality` ENUM('bi','hetero','homo') NOT NULL DEFAULT 'bi',
    `popularity` TINYINT NOT NULL DEFAULT 50,
    CONSTRAINT PK_user PRIMARY KEY (`id`, `pseudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
