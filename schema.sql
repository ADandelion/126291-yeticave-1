  CREATE DATABASE  `126291-yeticave-1`
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

  USE `126291-yeticave-1`;

  CREATE TABLE `lots` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date_create` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `name` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `starting_price` INT DEFAULT NULL,
    `date_expire` DATETIME NOT NULL,
    `bet_step` INT NOT NULL,
    `user_id` INT NOT NULL,
    `winner_id` INT DEFAULT  NULL,
    `category_id` INT  NULL
  );

  CREATE TABLE `bets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `add_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `price` INT NOT NULL,
    `user_id` INT NOT NULL,
    `lot_id` INT NOT NULL
  );

  CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR (255)
  );

  CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR (255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `contact` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `date_registered` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );