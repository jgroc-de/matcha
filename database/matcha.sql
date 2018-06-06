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
    `id` INT(11) AUTO_INCREMENT,
    `pseudo` VARCHAR(40),
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL DEFAULT 'jgroc2s@free.fr',
    `token` VARCHAR(255) NOT NULL DEFAULT 'auie',
    `activ` BOOL NOT NULL DEFAULT true,
    `forname` VARCHAR(255) NOT NULL DEFAULT 'John',
    `name` VARCHAR(255) NOT NULL DEFAULT 'Doe',
    `biography` TEXT,
    `birthdate` YEAR NOT NULL DEFAULT '00',
    `lattitude` FLOAT NOT NULL DEFAULT 48.853,
    `longitude` FLOAT NOT NULL DEFAULT 2.349,
    `gender` ENUM('Rick','Morty','Summer','Beth','Jerry') NOT NULL DEFAULT 'Rick',
    `sexuality` ENUM('bi','hetero','homo') NOT NULL DEFAULT 'bi',
    `popularity` TINYINT NOT NULL DEFAULT 0,
    `img1` TEXT,
    `img2` TEXT,
    `img3` TEXT,
    `img4` TEXT,
    `img5` TEXT,
    CONSTRAINT PK_user PRIMARY KEY (`id`, `pseudo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `friendsReq`
--

CREATE TABLE `friendsReq` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL,
    `owner` TINYINT NOT NULL,
    `message` TEXT NOT NULL,
    `date` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
    `id` INT(11) PRiMARY KEY AUTO_INCREMENT,
    `tag` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `usertags`
--

CREATE TABLE `usertags` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `idtag` INT(11) NOT NULL,
    `iduser` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
