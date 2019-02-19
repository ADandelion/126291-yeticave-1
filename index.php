<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

if(isset($_GET['id'])) {

    $lot_id = intval($_GET['id']);
    $lot = get_one_lot($link, $lot_id);
    $layout_content = include_template('lot.php', [
        'categories' => $categories,
        'user_name' => $user_name,
        'is_auth' => $is_auth,
        'lot' => $lot
    ]);

} else {
    $content = include_template('main.php', ['categories' => all_categories ($link), 'lots' => all_lots($link)]);

    $layout_content = include_template('layout.php', [
        'is_auth' => $is_auth,
        'title' => $title,
        'user_name' => $user_name,
        'content' => $content,
        'categories' => $categories
    ]);
}

print($layout_content);
