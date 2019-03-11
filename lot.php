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



// Проверям является авторизованный пользовтаель автором лота? Возвращает false если является
$user_lot = intval($_SESSION['user']['id']) === intval($lot['user_id']) ? false : true;

// Проверям, добавлял авторизованный пользоваель ставку по текущему лоту ? . Возвращает false добавлял
$user_bet_amount = get_user_bet($link, intval($_SESSION['user']['id']), $lot_id) !== null ? false : true;
// Проверям, истек лот или нет ? . Возвращает false если истек

$expire_lot_bet = bet_for_expire_lot($lot['date_expire']) ;


var_dump($expire_lot_bet);





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
    'error' => $error,
    'show_bet_form' => false
]);



print($layout_content);


// 3 пер.
// 1пер - автор лота, если ложь
// 2пер - твоя ставка если ложь
// 3пер - истек лот если ложь
// is_auth - есть если истина