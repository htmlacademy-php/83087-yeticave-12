CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128),
  code VARCHAR(128)
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  create_date DATETIME,
  user_id INT,
  name VARCHAR(128),
  description TEXT,
  category_id INT,
  image_url VARCHAR(128),
  price INT,
  end_date DATE,
  price_step MEDIUMINT,
  winner_id INT
);

CREATE TABLE rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rate_date DATETIME,
  sum INT,
  user_id INT,
  lot_id INT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_date DATETIME,
  email  VARCHAR(128),
  name VARCHAR(128),
  password VARCHAR(128),
  contact TEXT
);

CREATE FULLTEXT INDEX lot_search ON lots(name, description);
