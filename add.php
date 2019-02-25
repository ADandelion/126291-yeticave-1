<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

// проверить обязательные поля
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $addForm = $_POST;

            $required = ['name', 'cat_name', 'description', 'starting_price', 'bet_step', 'date_expire'];
            $errors =[];

        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Заполните обязательное поле';
            }
        }



        if (isset($_FILES['image']['name'])) {
//            var_dump($_FILES); die;
            $tmp_name = $_FILES['image']['tmp_name'];
            $path = $_FILES['image']['name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if ($file_type == "image/png" && $file_type == "image/jpg" && $file_type == "image/jpeg") {
                $errors['file'] = 'Загрузите картинку в формате PNG, JPG или JPEG';
            } else {
                move_uploaded_file($tmp_name, 'uploads/' . $path);
                $addForm['path'] = $path;
            }
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }
        if(count($errors)) {

            $layout_content = include_template('add.php', [
                'categories' => all_categories ($link),
                'is_auth' => $is_auth,
                'title' => $title,
                'user_name' => $user_name,
                'content' => $content
                //массив ошибок
            ]);
        }

}







    // Заполнить массив ошибок ключ = название поля, значение - текст ошибки
    // Проверека. Если массив ошибок пустой, сохраняем файл и сохраняем форму. Редирект на главную страницу.





$layout_content = include_template('add.php', [
    'categories' => all_categories ($link),
    'is_auth' => $is_auth,
    'title' => $title,
    'user_name' => $user_name,
    'content' => $content
    //массив ошибок
]);


print($layout_content);