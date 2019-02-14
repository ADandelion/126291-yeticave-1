<?php
/***
 * форматирования суммы и добавления к ней знака рубля
 * @param $price
 * @return string
 */

function formatPrice($price){
    $rubleStyle = " " . "<b class=\"rub\">р</b>";
    $totalPrice = ceil($price);
    if ($totalPrice  < 1000) {
        return $totalPrice . $rubleStyle;
    }
    return number_format($totalPrice , 0, '.', ' ') . $rubleStyle;
};

/***
 * Шаблонизатор
 * @param $name
 * @param $data
 * @return false|string
 */
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
};

/**
 * Экранирование
 * @param $scr
 * @return string/
 */

function screening_txt($scr) {
    $text = strip_tags($scr);
    return $text;
};

/***
 * Вычисляет время до истечения лота
 * @param $date
 * @return string
 */
function lot_expire ($date) {
    $currentDate = date_create();
    $lotDate = date_create($date);
    $interval= $lotDate->getTimestamp()- $currentDate->getTimestamp();
    $h = floor($interval / 3600);
    $m = floor(($interval - $h * 3600) / 60);
    return "$h:$m";
};





