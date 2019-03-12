<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

if ($is_auth === 1) {
    header("Location: /");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $required = ['email', 'password', 'name', 'contacts'];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Заполните обязательное поле';
        }
    }
    if(!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите  email';
    }
// Проверяем наличие имейла и что значение из поля «email» действительно является валидным E-mail адресом
    if(empty($errors['email'])) {

        $email = mysqli_real_escape_string($link, $_POST['email']);
        $sql = "SELECT id FROM users WHERE `email` = '$email'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }

// Проверяем является Аватар изображением

    if (!empty($_FILES['image']['name']) && !in_array(mime_content_type($_FILES['image']['tmp_name']),
            ["image/jpg", "image/png", "image/jpeg"]))
    {
            $errors['image'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
    }
 // Если все в порядке. Добавляем пользователя
    if (count($errors) === 0) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        if (!empty($_FILES['image']['name'])) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $path = 'img/' . uniqid() . $_FILES['image']['name'];
            move_uploaded_file($tmp_name, $path);
        } else {
            $path = null;
        }

        $newUser = addNewUser($link,
            [
                'email' => $_POST['email'],
                'password' => $password,
                'name' => $_POST['name'],
                'contacts' => $_POST['contacts'],
                'image' => $path
            ]);

        header("Location: /login.php");
    }

}

$layout_content = include_template('sign-up.php', [
    'categories' => all_categories ($link),
    'title' => $title,
    'user_name' => $user_name,
    'errors' => $errors

]);

print($layout_content);