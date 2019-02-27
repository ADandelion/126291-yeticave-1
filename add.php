<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';


$errors = [];

// проверить обязательные поля
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

/*  echo '<pre>' . var_export($_POST , true) . '</pre>';  die;*/
    $required = ['name', 'category', 'description', 'starting_price',
                 'bet_step', 'date_expire'];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Заполните обязательное поле';
        }
    }
    if (intval($_POST['starting_price']) <= 0) {

        $errors['starting_price'] = 'Введите положительное число';
    }
    if (intval($_POST['bet_step']) <= 0) {

        $errors['bet_step'] = 'Введите положительное число';
    }
    $date = strtotime($_POST['date_expire']);
    $now = time();
    if ($date - $now <= 86400) {

        $errors['date_expire'] = 'Дата должна быть больше текущей, хотя бы на один день';
    }



    if (isset($_FILES['image']['name'])) {
        /*        echo '<pre>' . var_export($_FILES, true) . '</pre>';  die;*/
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = 'img/' . $_FILES['image']['name'];

        $mime = mime_content_type($tmp_name);
        var_dump($mime);


        if ($mime !== "image/png" && $mime !== "image/jpg" && $mime !== "image/jpeg") {
            $errors['file'] = '1Загрузите картинку в формате PNG, JPG или JPEG';
        }
        if (empty($mime)) {
            $errors['file'] = 'нет картинки';
        }

        if (count($errors) == 0) {
            move_uploaded_file($tmp_name, $path);

            save_lot($link,
                [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'image' => $path,
                    'starting_price' => $_POST['starting_price'],
                    'date_expire' => $_POST['date_expire'],
                    'bet_step' => $_POST['bet_step'],
                    'category' => $_POST['category']
                ]);

            header("Location: /"); /* Не работает редирикет. Не могу понять почему*/

        } else {
            var_dump($errors);
        }

    }
}

$layout_content = include_template('add.php', [
    'categories' => all_categories ($link),
    'is_auth' => $is_auth,
    'title' => $title,
    'user_name' => $user_name,
    'errors' => $errors

]);

print($layout_content);