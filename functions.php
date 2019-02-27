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
 *
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


function save_lot($link, $fields_array = []) {
    $sql = "
            INSERT INTO `lots`
            (date_create, name, description, image, starting_price, date_expire, bet_step, user_id, category_id )
            VALUES
            (NOW(), ?, ?, ?, ?, ?, ?, 1, ?);

            ";

    $stmt = db_get_prepare_stmt($link, $sql,
        [
            $fields_array['name' ],$fields_array['description'],
            $fields_array['image'], $fields_array['starting_price'],
            $fields_array['date_expire'], $fields_array['bet_step'],
            $fields_array['category' ]
        ]);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
};
