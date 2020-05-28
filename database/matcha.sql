SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databatse: `matcha`
--

DROP DATABASE IF EXISTS matcha;
CREATE DATABASE matcha;
USE matcha;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
    `id` SERIAL,
    `pseudo` VARCHAR(40),
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL DEFAULT 'lol@matcha.fr',
    `token` TEXT NOT NULL,
    `publicToken` VARCHAR(255) NOT NULL DEFAULT 'private',
    `oauth` BOOL NOT NULL DEFAULT false,
    `activ` BOOL NOT NULL DEFAULT true,
    `name` VARCHAR(255) NOT NULL DEFAULT 'John',
    `surname` VARCHAR(255) NOT NULL DEFAULT 'Doe',
    `biography` TEXT,
    `birthdate` YEAR NOT NULL DEFAULT '00',
    `lattitude` FLOAT NOT NULL DEFAULT 48.853,
    `longitude` FLOAT NOT NULL DEFAULT 2.349,
    `gender` ENUM('Rick','Morty','Summer','Beth','Jerry') NOT NULL DEFAULT 'Rick',
    `sexuality` ENUM('bi','hetero','homo') NOT NULL DEFAULT 'bi',
    `popularity` TINYINT NOT NULL DEFAULT 0,
    `bot` BOOL DEFAULT false,
    `lastlog` INT(11) DEFAULT 0,
    `img1` VARCHAR(255),
    `img2` VARCHAR(255),
    `img3` VARCHAR(255),
    `img4` VARCHAR(255),
    `img5` VARCHAR(255),
    `cloud_id1` VARCHAR(255),
    `cloud_id2` VARCHAR(255),
    `cloud_id3` VARCHAR(255),
    `cloud_id4` VARCHAR(255),
    `cloud_id5` VARCHAR(255),
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_user PRIMARY KEY (`id`, `pseudo`),
    CONSTRAINT PK_user2 UNIQUE (`pseudo`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `pictures`
--

CREATE TABLE `pictures` (
    `id` SERIAL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `cloud_id` VARCHAR(255),
    `url` VARCHAR(255) NOT NULL,
    `date` DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE pictures
ADD FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE;

--
-- Table structure for table `friendsReq`
--

CREATE TABLE `friendsReq` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL,
    `visible` BOOL DEFAULT TRUE,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_friendsReq UNIQUE (`id_user1`, `id_user2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL,
    `suscriber` VARCHAR(255) NOT NULL,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_friends UNIQUE (`id_user1`, `id_user2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
    `id_user1` INT(11) NOT NULL,
    `id_user2` INT(11) NOT NULL,
    `owner` INT(11) NOT NULL,
    `message` TEXT NOT NULL,
    `date` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
    `id` SERIAL PRIMARY KEY,
    `tag` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `usertags`
--

CREATE TABLE `usertags` (
    `id` SERIAL PRIMARY KEY,
    `idtag` INT(11) NOT NULL,
    `iduser` INT(11) NOT NULL,
    CONSTRAINT PK_usertags UNIQUE (`idtag`, `iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
    `id` SERIAL PRIMARY KEY,
    `exp` INT(11) NOT NULL,
    `dest` INT(11) NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `date` DATETIME NOT NULL DEFAULT NOW(),
    `seen` BOOL DEFAULT false
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
    `id` SERIAL PRIMARY KEY,
    `iduser` INT(11) NOT NULL,
    `iduser_bl` INT(11) NOT NULL,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_blacklist UNIQUE (`iduser`, `iduser_bl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;
