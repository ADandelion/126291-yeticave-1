<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $required = ['email', 'password', 'name', 'contacts'];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Заполните обязательное поле';
        }
    }
// Проверяем наличие имейла и что значение из поля «email» действительно является валидным E-mail адресом
    if(!empty($_POST['email'])) {

        $email = mysqli_real_escape_string($link, $_POST['email']);
        $sql = "SELECT id FROM users WHERE `email` = '$email'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors[$key] = 'Пользователь с этим email уже зарегистрирован';
        }
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[$key] = 'Введите  email';
    }

// Проверяем наличие пароля.

    if (empty($_POST['password'])) {
        $errors[$key] = 'Поле пароль обязательное';
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

// Проверяем наличие имени

    if (empty($_POST['name'])) {
        $errors[$key] = 'Поле имя обязательное';
    }

// Проверяем наличие контактов

    if (empty($_POST['contacts'])) {
        $errors[$key] = 'Введите контакты';
    }

// Проверяем является Аватар изображением

    if (!empty($_FILES['image']['name'])) {
        if (!in_array(mime_content_type($_FILES['image']['tmp_name']), ["image/jpg", "image/png", "image/jpeg"])) {
            $errors['image'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
        }
    }
    $tmp_name = $_FILES['image']['tmp_name'];
    $path = 'img/' . uniqid() . $_FILES['image']['name'];
    move_uploaded_file($tmp_name, $path);


 // Если все в порядке. Добавляем пользователя
    if (count($errors) === 0) {

        $newUser = addNewUser($link,
            [
                'email' => $_POST['email'],
                'password' => $password,
                'name' => $_POST['name'],
                'contacts' => $_POST['contacts'],
                'image' => $path
            ]);


    } else {
        var_dump($errors);
    }

}

$layout_content = include_template('sign-up.php', [
    'categories' => all_categories ($link),
    'title' => $title,
    'user_name' => $user_name,
    'errors' => $errors

]);

print($layout_content);