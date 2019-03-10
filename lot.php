<?php
session_start();

require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {

    $layout_content = include_template('404.php', [
        'categories' => all_categories ($link),
        'user_name' => $user_name,
        'is_auth' => $is_auth,
        'error' => 'Лот не найден'
    ]);

    print($layout_content);
    exit();
}

$lot_id = intval($_GET['id']);
$lot = get_one_lot($link, $lot_id);



if (empty($lot)) {

    $layout_content = include_template('404.php', [
        'categories' => all_categories ($link),
        'user_name' => $user_name,
        'is_auth' => $is_auth,
        'error' => 'Лот не найден'
    ]);

    print($layout_content);
    exit();
}
$minBet = intval($lot['price']) + intval($lot['bet_step']);
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_auth === 1) {


    if (empty($_POST['cost'])) {
        $error = 'Ввидете минимальную сумму ставки';
    } else if (intval($_POST['cost']) < $minBet) {
        $error = 'Минимум ' . $minBet;

    } else {
        save_bet($link, $_POST['cost'], $user_id, $lot_id);

        header('Location: lot.php?id=' . $lot_id);
    }
}




/// Получение ставок
$bets = get_bets($link, $lot_id);

$layout_content = include_template('lot.php', [
    'categories' => all_categories ($link),
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'lot' => $lot,
    'bets' => $bets,
    'error' => $error
]);



print($layout_content);
