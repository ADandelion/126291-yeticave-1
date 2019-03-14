<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {

    $layout_content = include_template('404.php', [
        'categories' => all_categories ($link),
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'is_auth' => $is_auth,
        'error' => 'Лот не найден'
    ]);

    print($layout_content);
    exit();
}

$lot_id = intval($_GET['id']);
$lot =  get_one_lot($link, $lot_id);
if (empty($lot)) {

    $layout_content = include_template('404.php', [
        'categories' => all_categories ($link),
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'is_auth' => $is_auth,
        'error' => 'Лот не найден'
    ]);

    print($layout_content);
    exit();
}
$minBet = intval($lot['starting_price']) + intval($lot['bet_step']);
$error = '';

$show_bet_form = $is_auth === 1
    && !bet_for_expire_lot($lot['date_expire'])
    && $user_id !== intval($lot['user_id'])
    && empty(get_user_bet($link, $user_id, $lot_id));



if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_auth === 1) {

    if (empty($_POST['cost'])) {
        $error = 'Введите минимальную ставку' ;
    }elseif (empty(intval($_POST['cost'])) || intval($_POST['cost']) < $minBet) {
        $error = 'Введите минимальную ставку';
    } else {
        save_bet($link, $_POST['cost'], $user_id, $lot_id);
        header('Location: lot.php?id=' . $lot_id);
    }
}

$bets = get_bets($link, $lot_id);

$layout_content = include_template('lot.php', [
    'categories' => all_categories ($link),
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'is_auth' => $is_auth,
    'lot' => $lot,
    'bets' => $bets,
    'error' => $error,
    'show_bet_form' => $show_bet_form,

]);



print($layout_content);