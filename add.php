<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';


if (!$is_auth) {
    header("Location: login.php");
    exit();
}

$errors = [];

// проверить обязательные поля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required = ['name', 'category', 'description', 'starting_price',
                 'bet_step', 'date_expire'];

    foreach ($required as $key) {
        if (empty($_POST[$key])|| trim($_POST[$key]) === '') {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (empty($errors['starting_price']) && intval($_POST['starting_price']) <= 0) {
        $errors['starting_price'] = 'Введите число больше нуля';
    }
    if (empty($errors['bet_step']) && intval($_POST['bet_step']) <= 0) {
        $errors['bet_step'] = 'Введите число больше нуля';
    }


    $date = strtotime($_POST['date_expire']);
    $now = strtotime('today midnight');
    if ($date - $now < 86400) {
        $errors['date_expire'] = 'Дата должна быть больше текущей, хотя бы на один день';
    }


    if (empty($_FILES['image']) || empty($_FILES['image']['name'])) {
        $errors['image'] = 'нет картинки';
    } elseif (!in_array( mime_content_type($_FILES['image']['tmp_name']), ["image/jpg", "image/png", "image/jpeg"])) {
        $errors['image'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
    }

        if (count($errors) === 0) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $path = 'img/' . uniqid() . $_FILES['image']['name'];
            move_uploaded_file($tmp_name, $path);

           $lot_id = save_lot($link,
                [
                    'user_id' => $user_id,
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'image' => $path,
                    'starting_price' => $_POST['starting_price'],
                    'date_expire' => $_POST['date_expire'],
                    'bet_step' => $_POST['bet_step'],
                    'category' => $_POST['category']
                ]);
        if ($lot_id > 0) {
            header("Location: /lot.php?id=" . $lot_id);
        }
    }
}

$layout_content = include_template('add.php', [
    'categories' => all_categories ($link),
    'is_auth' => $is_auth,
    'title' => $title,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'errors' => $errors
]);


print($layout_content);