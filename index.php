<?php
require_once 'db.php';
require_once 'data.php';
require_once 'functions.php';

$content = include_template('main.php', ['categories' => all_categories ($link), 'lots' => all_lots($link)]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'title' => $title,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'content' => $content,
    'categories' => all_categories ($link)
]);

print($layout_content);
