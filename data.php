<?php
session_start();

$is_auth = isset($_SESSION['user']) ? 1 : 0;
$user_name = $is_auth === 1 ? $_SESSION['user']['name'] : '';
$user_id = $is_auth === 1 ? $_SESSION['user']['id'] : '';

$title = 'Главная страница';

date_default_timezone_set("Europe/Chisinau");