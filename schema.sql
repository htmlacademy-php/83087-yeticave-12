CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lot_date DATETIME,
  user_id INT,
  name CHAR,
  description TEXT,
  category_id INT,
  image_url CHAR,
  price DECIMAL,
  lot_date_end DATE,
  price_step INT,
  users_win INT
);

CREATE TABLE rates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rate_date DATETIME,
  sum DECIMAL,
  user_id INT,
  lot_id INT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_date DATETIME,
  email  VARCHAR(128),
  name CHAR,
  password CHAR(64),
  contact TEXT,
  lots_id INT,
  rates_id INT
);
