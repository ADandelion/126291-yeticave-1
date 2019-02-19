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
