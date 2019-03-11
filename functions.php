<?php
require_once 'mysql_helper.php';
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


/***
 * Самые новые открытые лоты
 * @param $link
 * @return array|null
 */
function all_lots ($link) {
    $lots = [];
    $sql = '
        SELECT 
              lots.*, 
              IFNULL(max(bets.price), lots.starting_price) AS price, categories.name AS cat_name
        FROM lots
        JOIN categories on lots.category_id = categories.id
        LEFT JOIN bets on lots.id = bets.lot_id
        WHERE lots.winner_id IS NULL
            AND lots.date_expire > NOW()
        GROUP BY lots.id, bets.add_date desc, lots.date_create desc
        ORDER BY bets.add_date desc, lots.date_create desc
';

    $res = mysqli_query($link, $sql);
    if($res !== false) {
        $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $lots;
};


/***
 * Собираем массив всех категорий
 * @param $link
 * @return array|null
 */

function all_categories ($link) {
    $categories = [];
    $sql = 'SELECT * FROM categories;';

    $cat_result = mysqli_query($link, $sql);
    if($cat_result !== false) {
        $categories = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);
    }
    return $categories;
};

/***
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
            AND lots.date_expire > NOW()
            AND lots.id = ?
      GROUP BY lots.id
";

    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
};

/***
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

    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $fields_array['name'],$fields_array['description'],
            $fields_array['image'], $fields_array['starting_price'],
            $fields_array['date_expire'], $fields_array['bet_step'],
            $fields_array['user_id'],$fields_array['category'],
        ]);


    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($link);
};
/**
 *
 *
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

    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $fields_array['email'],$fields_array['password'],
            $fields_array['name'], $fields_array['contacts'],
            $fields_array['image']
        ]);

    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($link);
};

/**
 *
 *`
 * @param $link
 * @param $lot_id
 * @return array|null
 */
function get_bets ($link, $lot_id) {
    $bets = [];

    $sql = "
    select b.*, u.name from `bets` as b
    join `users` as u on u.id = b.user_id
    where lot_id = ?
      ";
    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);


    if($result !== false) {
        $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $bets;
}


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

function bet_for_expire_lot ($expireDate) {
    $currentDate = date_create();
    $lotExpireDate = date_create($expireDate);

    if ($lotExpireDate->getTimestamp() < $currentDate->getTimestamp()) {
        return true;
    }
    return false;
}