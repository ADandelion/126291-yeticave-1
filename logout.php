<?php
session_start();

$_SESSION = [];
header("Location: /");


var_dump($_SESSION); die;