<?php


$is_auth = rand(0, 1);
$user_name = 'A. Благодетелев';
$title = 'Главная страница';
date_default_timezone_set("Europe/Chisinau");
$categories = [];
$sql_get_categories = 'SELECT * FROM categories;';
$cat_result = mysqli_query($link, $sql_get_categories);

if($cat_result) {
    $categories = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);
}

$lots = [];
$sql_get_lots = 'SELECT  lots.name, lots.date_expire, lots.starting_price, lots.image, IFNULL(max(bets.price), lots.starting_price) AS price, categories.name AS cat_name
FROM lots
       JOIN categories on lots.category_id = categories.id
       LEFT JOIN bets on lots.id = bets.lot_id
WHERE lots.winner_id IS NULL
  AND lots.date_expire > NOW()
GROUP BY lots.id
ORDER BY bets.add_date desc,
         lots.date_create desc;';

$lots_result = mysqli_query($link, $sql_get_lots);
if($lots_result) {
    $lots = mysqli_fetch_all($lots_result, MYSQLI_ASSOC);
}

