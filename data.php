<?php
$is_auth = isset($_SESSION['user']) ? 1 : 0;
$user_name = $is_auth === 1 ? $_SESSION['user']['name'] : '';
$title = 'Главная страница';

date_default_timezone_set("Europe/Chisinau");