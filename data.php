<?php
session_start();
$is_auth = isset($_SESSION['user']) ? 1 : 0;

$user_name = $is_auth === 1 && isset($_SESSION['user']['name'])
    ? $_SESSION['user']['name']
    : '';

$user_id = $is_auth === 1 && intval(isset($_SESSION['user']['id']))
    ? intval($_SESSION['user']['id'])
    : '';

$user_avatar = $is_auth === 1 && isset($_SESSION['user']['avatar']) !== null
    ? $_SESSION['user']['avatar']
    : false;

$title = 'Главная страница';


date_default_timezone_set("Europe/Chisinau");