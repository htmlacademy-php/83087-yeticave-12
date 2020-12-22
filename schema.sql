CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
);

CREATE TABLE lots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  create_date DATETIME,
  user_id INT,
  name VARCHAR,
  description TEXT,
  category_id INT,
  image_url VARCHAR,
  price DECIMAL,
  end_date DATE,
  price_step INT,
  winner_id INT
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
  email  VARCHAR,
  name VARCHAR,
  password VARCHAR,
  contact TEXT
);
