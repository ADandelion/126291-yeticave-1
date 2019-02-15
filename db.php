<?php

$db = [
    'host' => 'localhost1',
    'user' => 'root',
    'password' => '',
    'database' => '126291-yeticave-1'
];

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if(!$link) {
    die ('Ошибка подключения');
}

mysqli_set_charset($link, "utf8");