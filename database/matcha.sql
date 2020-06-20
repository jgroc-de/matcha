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

DROP TABLE IF EXISTS `blacklist`;
DROP TABLE IF EXISTS `notification`;
DROP TABLE IF EXISTS `usertags`;
DROP TABLE IF EXISTS `hashtags`;
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `friends`;
DROP TABLE IF EXISTS `friendsReq`;
DROP TABLE IF EXISTS `pictures`;
DROP TABLE IF EXISTS `user`;

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
    `birthdate` SMALLINT NOT NULL DEFAULT (YEAR(CURDATE()) - 18),
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
    `id_user` BIGINT UNSIGNED NOT NULL,
    `cloud_id` VARCHAR(255),
    `url` VARCHAR(255) NOT NULL,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT FK_picture FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `friendsReq`
--

CREATE TABLE `friendsReq` (
    `id_user1` BIGINT UNSIGNED NOT NULL,
    `id_user2` BIGINT UNSIGNED NOT NULL,
    `visible` BOOL DEFAULT TRUE,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_friendsReq UNIQUE (`id_user1`, `id_user2`),
    CONSTRAINT FK_friendsReq1 FOREIGN KEY (id_user1) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_friendsReq2 FOREIGN KEY (id_user2) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
    `id_user1` BIGINT UNSIGNED NOT NULL,
    `id_user2` BIGINT UNSIGNED NOT NULL,
    `suscriber` VARCHAR(255) NOT NULL,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_friends UNIQUE (`id_user1`, `id_user2`),
    CONSTRAINT FK_friends1 FOREIGN KEY (id_user1) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_friends2 FOREIGN KEY (id_user2) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
    `id_user1` BIGINT UNSIGNED NOT NULL,
    `id_user2` BIGINT UNSIGNED NOT NULL,
    `owner` BIGINT UNSIGNED NOT NULL,
    `message` TEXT NOT NULL,
    `date` DATETIME NOT NULL,
    CONSTRAINT FK_message1 FOREIGN KEY (id_user1) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_message2 FOREIGN KEY (id_user2) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
    `id` SERIAL PRIMARY KEY,
    `tag` VARCHAR(255) NOT NULL UNIQUE,
    `tag_count` BIGINT UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `usertags`
--

CREATE TABLE `usertags` (
    `id` SERIAL PRIMARY KEY,
    `id_tag` BIGINT UNSIGNED NOT NULL,
    `id_user` BIGINT UNSIGNED NOT NULL,
    CONSTRAINT PK_usertags UNIQUE (`id_tag`, `id_user`),
    CONSTRAINT FK_usertags FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_hashtags FOREIGN KEY (id_tag) REFERENCES hashtags(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
    `id` SERIAL PRIMARY KEY,
    `exp` BIGINT UNSIGNED NOT NULL,
    `dest` BIGINT UNSIGNED NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `date` DATETIME NOT NULL DEFAULT NOW(),
    `seen` BOOL DEFAULT false,
    CONSTRAINT FK_exp FOREIGN KEY (exp) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_dest FOREIGN KEY (dest) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
    `id` SERIAL PRIMARY KEY,
    `id_user` BIGINT UNSIGNED NOT NULL,
    `id_user_bl` BIGINT UNSIGNED NOT NULL,
    `date` DATETIME DEFAULT NOW(),
    CONSTRAINT PK_blacklist UNIQUE (`id_user`, `id_user_bl`),
    CONSTRAINT FK_blacklist FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_bl FOREIGN KEY (id_user_bl) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TRIGGER `add_tag_count` AFTER INSERT
    ON `usertags` FOR EACH ROW
    UPDATE `hashtags` SET `tag_count` = `tag_count` + 1
    WHERE `hashtags`.`id` = NEW.`id_tag`
;

CREATE TRIGGER `sub_tag_count` AFTER DELETE
ON `usertags` FOR EACH ROW
    UPDATE `hashtags` SET `tag_count` = `tag_count` - 1
    WHERE `hashtags`.`id` = OLD.`id_tag`
;

COMMIT;
