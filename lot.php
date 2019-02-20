<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';


$layout_content = include_template('404.php', [
    'categories' => all_categories ($link),
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'error' => 'Лот не найден'
]);

if(isset($_GET['id'])) {

    $lot_id = intval($_GET['id']);
    $lot = get_one_lot($link, $lot_id);

    if ($lot !== null) {

        $layout_content = include_template('lot.php', [
            'categories' => all_categories ($link),
            'user_name' => $user_name,
            'is_auth' => $is_auth,
            'lot' => $lot
        ]);
    }
}

print($layout_content);
