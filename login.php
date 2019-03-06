<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $required = ['email', 'password'];
    foreach ($required as $key) {
        if (empty($form[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if(empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите  email';
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);
    $sql = "SELECT id FROM users WHERE `email` = '$email'";
    $res = mysqli_query($link, $sql);
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        }
        else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors) > 0) {
        $page_content = include_template('login.php',
            [
                'categories' => all_categories ($link),
                'title' => $title,
                'user_name' => $user_name,
                'errors' => $errors
            ]);
    }
    else {
        header("Location: /");
    }

} else {
    if (isset($_SESSION['user'])) {
        $page_content = include_template('index.php', ['user_name' => $_SESSION['user']['name']]);
    }
    else {
        $page_content = include_template('enter.php', []);
    }
}



$layout_content = include_template('login.php', [
    'categories' => all_categories ($link),
    'title' => $title,
    'user_name' => $user_name,
    'errors' => $errors

]);

print($layout_content);