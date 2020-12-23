-- добавление существующего списка категорий
INSERT INTO categories SET id = 1, name = 'Доски и лыжи';
INSERT INTO categories SET id = 2, name = 'Крепления';
INSERT INTO categories SET id = 3, name = 'Ботинки';
INSERT INTO categories SET id = 4, name = 'Одежда';
INSERT INTO categories SET id = 5, name = 'Инструменты';
INSERT INTO categories SET id = 6, name = 'Разное';

-- добавление пары пользователей
INSERT INTO users SET id = 1, reg_date = '2020-12-19', email = 'papan41k@gmail.com', name = 'Павел', password = 'Ms\,6Baq]:z4Cw7U';
INSERT INTO users SET id = 2, reg_date = '2020-12-22', email = 'aleks.palyan@gmail.com', name = 'Александр', password = ';UKQ&x.<3p+\vM(u';

-- добавление существующего списка объявлений
INSERT INTO lots SET id = 1, create_date = '2020-12-17', user_id = 1, name = '2014 Rossignol District Snowboard', description = '', category_id = 1, image_url = 'img/lot-1.jpg', price = 10999, end_date = '2020-12-19';

INSERT INTO lots SET id = 2, create_date = '2020-12-18', user_id = 2, name = 'DC Ply Mens 2016/2017 Snowboard', description = '', category_id = 1, image_url = 'img/lot-2.jpg', price = 159999, end_date = '2021-01-14';

INSERT INTO lots SET id = 3, create_date = '2020-12-19', user_id = 2, name = 'Крепления Union Contact Pro 2015 года размер L/XL', description = '', category_id = 2, image_url = 'img/lot-3.jpg', price = '8000', end_date = '2021-01-15';

INSERT INTO lots SET id = 4, create_date = '2020-12-20', user_id = 1, name = 'Ботинки для сноуборда DC Mutiny Charocal', description = '', category_id = 3, image_url = 'img/lot-4.jpg', price = '10999', end_date = '2021-01-16';

INSERT INTO lots SET id = 5, create_date = '2020-12-21', user_id = 1, name = 'Куртка для сноуборда DC Mutiny Charocal', description = '', category_id = 4, image_url = 'img/lot-5.jpg', price = '7500', end_date = '2021-01-17';

INSERT INTO lots SET id = 6, create_date = '2020-12-22', user_id = 1, name = 'Маска Oakley Canopy', description = '', category_id = 6, image_url = 'img/lot-6.jpg', price = '5400', end_date = '2021-01-18';

-- добавляем пару ставок для двух объявлений
INSERT INTO rates SET sum = 11999, rate_date = '2020-12-22', lot_id = 1, user_id = 1;
INSERT INTO rates SET sum = 12999, rate_date = '2020-12-23', lot_id = 1, user_id = 2;

-- получаем все категории
SELECT * FROM categories;

-- получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, текущую цену, название категории;
SELECT name, price, image_url, sum, category_id FROM lots
LEFT JOIN rates
ON lots.id = rates.lot_id
WHERE lots.end_date > '2020-12-23'
ORDER BY create_date DESC;

-- показать лот по его id. Получите также название категории, к которой принадлежит лот;
SELECT lots.id, categories.name FROM lots
JOIN categories
ON lots.category_id = categories.id;

-- обновляем название лота по его идентификатору
UPDATE lots SET name = 'Куртка для сноуборда Rip Curl' WHERE id = 5;

--получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM rates r
JOIN lots l
  ON l.id = r.lot_id
  ORDER BY rate_date;
