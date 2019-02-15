USE `126291-yeticave-1`;

-- Добавляем список категорий;

INSERT INTO `categories`
(name)
VALUES
('Доски и лыжи'),
('Крепления'),
('Ботинки'),
('Одежда'),
('Инструменты'),
('Разное');

-- Добавляем пользователей

INSERT INTO `users`
(email, password, name, contact, avatar)
VALUES
('alexblag@gmail.com', 'qwerty123', 'Alex', '022520330', 'img/ava.png'),
('alexblag3@ya.ru', 'qweqwe123', 'Masha1', '079123456', 'img/ava1.png'),
('alexblag2@ya.ru', 'qweqwe123', 'Masha2', '079123456', 'img/ava1.png'),
('alexblag1@ya.ru', 'qweqwe123', 'Masha3', '079123456', 'img/ava1.png');


-- Добавляем лоты

INSERT INTO `lots`
(name, description, image, starting_price, date_expire, bet_step, user_id, winner_id, category_id)
VALUES
('2014 Rossignol District Snowboard', 'Доска мега крутая', 'img/lot-1.jpg', 10999, '2018-12-11 00-00-00', 1000, 1, NULL, 1),
('DC Ply Mens 2016/2017 Snowboard', 'Еще одна крутая доска', 'img/lot-2.jpg', 15999, '2018-12-11 00-00-00', 1000, 2, NULL, 1),
('Крепления Union Contact Pro 2015 года размер L/XL', 'Крепления', 'img/lot-3.jpg', 8000, '2018-12-11 00-00-00', 1, 2, NULL, 2),
('Ботинки для сноуборда DC Mutiny Charcoal', 'Ботинки', 'img/lot-4.jpg', 10999, '2018-12-11 00-00-00', 1000, 2, NULL, 3),
('Куртка для сноуборда DC Mutiny Charcoal', 'Куртка боевая', 'img/lot-5.jpg', 7500, '2018-12-11 00-00-00', 1000, 2, NULL, 4),
('Маска Oakley Canopy', 'Куртка боевая', 'img/lot-6.jpg', 5000, '2018-12-11 00-00-00', 1000, 2, NULL, 6);

-- Добавляем ставки

INSERT INTO `bets`
(price, user_id, lot_id)
VALUES
(17700, 1, 2),
(15000, 2, 1),
(25000, 3, 2),
(30000, 4, 2);



-- Запросы

-- 1. Получаем все категории
SELECT * FROM categories;

-- 2. получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену,
-- ссылку на изображение, цену, название категории;

SELECT  lots.name, lots.starting_price, lots.image, IFNULL(max(bets.price), lots.starting_price) AS price, categories.name AS cat_name
FROM lots
       JOIN categories on lots.category_id = categories.id
       LEFT JOIN bets on lots.id = bets.lot_id
WHERE lots.winner_id IS NULL
  AND lots.date_expire > NOW()
GROUP BY lots.id
ORDER BY bets.add_date desc,
         lots.date_create desc;


-- 3. показать лот по его id. Получите также название категории, к которой принадлежит лот

SELECT lots.*, categories.name FROM lots
                                      JOIN categories
                                           ON lots.category_id = categories.id
WHERE lots.id = 2;

-- 4. обновить название лота по его идентификатору;

UPDATE lots
SET name = 'Другое'
WHERE id = 1;

-- 5. получить список самых свежих ставок для лота по его идентификатору;

SELECT * FROM bets
WHERE lot_id = 1
ORDER BY add_date DESC;


select * from lots;


