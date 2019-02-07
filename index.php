<?php
require_once 'data.php';
require_once 'functions.php';

$main_content = include_template('main.php', ['lots' => $lots, 'categories' => $categories ]);

$layout_content = include_template('layout.php', [
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => $title,
    'categories' => $categories,
    'main_content' => $main_content
]);

print ($layout_content);