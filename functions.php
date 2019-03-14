<?php
require_once 'db.php';
require_once 'mysql_helper.php';
/**
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

/**
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

/**
 * Вычисляет время до истечения лота
 * @param $date
 * @return string
 */
function lot_expire ($date) {
    $currentDate = date_create();
    $lotDate = date_create($date);
    if ($currentDate->getTimestamp() > $lotDate ->getTimestamp()) {
        return '-- : --';
    }
    $interval= $lotDate->getTimestamp()- $currentDate->getTimestamp();

    $h = floor($interval / 3600);
    $m = floor(($interval - $h * 3600) / 60);

    $h = intval($h) < 10 ? '0' . $h : $h;
    $m = intval($m) < 10 ? '0' . $m : $m;
    return "$h:$m";
};

/**
 *  Заменят числовое описание времени на текстовое
 * @param $time
 * @return false|string
 */
function set_bet_time_phrase ($time){
    $currentTime = date_create();
    $betTime = date_create($time);
    $interval = floor($currentTime->getTimestamp() - $betTime->getTimestamp());

   if ($interval < 60) {
       return 'только что';
    }

   if ($interval < 3600) {
        return floor($interval / 60) . 'м назад';
    }

   if ($interval < 86400) {
        return floor($interval / 3600) . 'ч назад';
    }

       return date("Y-m-d \в H:i:s");

}
/**
 * Самые новые открытые лоты
 * @param $link
 * @return array|null
 */
function all_lots ($link) {

    $sql = '
        SELECT 
              lots.*, 
              IFNULL(max(bets.price), lots.starting_price) AS price, categories.name AS cat_name
        FROM lots
        JOIN categories on lots.category_id = categories.id
        LEFT JOIN bets on lots.id = bets.lot_id
        WHERE lots.winner_id IS NULL
            AND lots.date_expire > NOW()
        GROUP BY lots.id desc, lots.date_create desc
        ORDER BY lots.date_create desc
';


    $res = mysqli_query($link, $sql);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
};


/**
 * Собираем массив всех категорий
 * @param $link
 * @return array|null
 */
function all_categories ($link) {
    $sql = 'SELECT * FROM categories;';

    $cat_result = mysqli_query($link, $sql);

    return mysqli_fetch_all($cat_result, MYSQLI_ASSOC);
};

/**
 * Собираем данные лота по id
 * @param $link
 * @param $id
 * @return array | null
 */
function get_one_lot ($link, $id) {

    $sql = "
      SELECT 
             lots.*, 
             IFNULL(max(bets.price), lots.starting_price) AS price, categories.name AS cat_name
      FROM lots
      JOIN categories on lots.category_id = categories.id
      LEFT JOIN bets on lots.id = bets.lot_id
      WHERE lots.winner_id IS NULL
            AND lots.id = ?
      GROUP BY lots.id
";

    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
};

/**
 * Записываем новый лот в БД
 * @param $link
 * @param array $fields_array
 * @return int|string
 */
function save_lot($link, $fields_array = []) {
    $sql = "
            INSERT INTO `lots`
            (date_create, name, description, image, starting_price, date_expire, bet_step, user_id, category_id )
            VALUES
            (NOW(), ?, ?, ?, ?, ?, ?, ?, ?);

            ";


    $req = ['name', 'category', 'description', 'starting_price',
        'bet_step', 'date_expire', 'image', 'user_id'];

    foreach ($req as $key) {
        if (empty($key)) {
            return 0;
        }
    }

    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $fields_array['name'],
            $fields_array['description'],
            $fields_array['image'],
            $fields_array['starting_price'],
            $fields_array['date_expire'],
            $fields_array['bet_step'],
            $fields_array['user_id'],
            $fields_array['category']
        ]);


    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($link);
};
/**
 * Сохраняем ставку в БД
 * @param $link
 * @param $bet_cost
 * @param $user_id
 * @param $lot_id
 * @return int|string
 */
function save_bet($link, $bet_cost, $user_id, $lot_id ) {
    $sql = "
            INSERT INTO `bets`
            (price, user_id, lot_id)
            VALUES
            (?, ?, ?);

            ";


    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $bet_cost, $user_id, $lot_id
        ]);


    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($link);
};

/**
 * Записываем нового пользователя в БД
 * @param $link
 * @param array $fields_array
 * @return int|string
 */
function addNewUser ($link, $fields_array = []) {

    $sql = "
            INSERT INTO `users`
            (email, password, name, contact, avatar, date_registered)
            VALUES
            ( ?, ?, ?, ?, ?, NOW());

            ";

    $req = ['email', 'password', 'name', 'contacts',
        'image'];

    foreach ($req as $key) {
        if (empty($key)) {
            return 0;
        }
    }

    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $fields_array['email'],
            $fields_array['password'],
            $fields_array['name'],
            $fields_array['contacts'],
            $fields_array['image'],
        ]);

    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($link);
};

/**
 * Получаем все ставки по id лота
 * @param $link
 * @param $lot_id
 * @return array|null
 */
function get_bets ($link, $lot_id) {
    $bets = [];

    $sql = "
    select b.*, u.name 
    from `bets` 
      as b
    join `users` 
      as u 
      on u.id = b.user_id
    where lot_id = ?
    order by b.id desc 
      ";
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получаем ставку пользоваетеля по лоту
 * @param $link
 * @param $user_id
 * @param $lot_id
 * @return array|null
 */
function get_user_bet ($link, $user_id, $lot_id) {
    $sql = "
        SELECT id 
        FROM `bets` 
        WHERE user_id = ?
          and 
        lot_id = ?;
      ";

    $stmt = db_get_prepare_stmt($link, $sql, [$user_id, $lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

/**
 * Проверяем истек лот или нет
 * @param $expireDate
 * @return bool
 */
function bet_for_expire_lot ($expireDate) {
    $currentDate = date_create();
    $lotExpireDate = date_create($expireDate);

    if ($lotExpireDate->getTimestamp() < $currentDate->getTimestamp()) {
        return true;
    }
    return false;
}