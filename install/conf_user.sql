CREATE USER 'matcha'@'localhost' IDENTIFIED BY 'matcha';
GRANT ALL PRIVILEGES ON *.* TO 'matcha'@'localhost';
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS `matcha`;
