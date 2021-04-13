<?php

/**
 * Функция для форматирования суммы и добавления к ней знака рубля
 * @param int $numberRate сумма лота
 * @return string Отформатированная сумма вместе со знаком рубля
 */
function formatPrice($numberRate)
{
    $numberRate = ceil($numberRate);

    if ($numberRate >= 1000) {
        $numberRate = number_format($numberRate, 0, ".", " ");
    }

    return $numberRate . ' ₽';
}

/**
 * Функция для вывода текста, которая удаляет теги HTML и PHP из строки
 * @param string $str текст
 * @return string Отформатированный текст
 */
function stripTags($str)
{
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lotDate дата вида - ГГГГ-ММ-ДД
 * @param bool $showSeconds - вывести секунды
 */
function getDifferenceTime(string $lotDate, bool $showSeconds = null)
{
    $currentDate = time();
    $lotDateUnix = strtotime($lotDate);

    $dateDiff = $lotDateUnix - $currentDate;
    $allMinutes = floor($dateDiff / 60);
    $hours = floor($allMinutes / 60);
    $minute = $allMinutes - $hours * 60;
    $seconds =  $dateDiff - $allMinutes * 60;

    if ($showSeconds === true) {
        $res = [
            $hours, $minute, $seconds
        ];
    } else {
        $res = [
            $hours, $minute
        ];
    }

    return $res;
}

function getConnection($config)
{
    $connection = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
    mysqli_set_charset($connection, "utf8");

    if ($connection == false) {
        print("Ошибка подключения: " . mysqli_connect_error());
    }

    return $connection;
}

function getCategories($connection)
{
    $requestCategories = "SELECT id, name, code FROM categories";

    $resultCategories = mysqli_query($connection, $requestCategories);

    if (!$resultCategories) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

    return $categories;
}

function getCategoryName($connection, $name)
{
    $requestCategory = "SELECT name FROM categories
    WHERE categories.id = '$name'";

    $resultCategory = mysqli_query($connection, $requestCategory);

    if (!$resultCategory) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $categoryName = mysqli_fetch_all($resultCategory, MYSQLI_ASSOC);

    return $categoryName[0]['name'];
}

function getLots($connection, $category = null)
{
    $requestLots = "SELECT lots.name, lots.id, categories.name as category, image_url, price, end_date
    FROM lots JOIN categories
    WHERE lots.category_id = categories.id";

    if (!empty($category)) {
        $requestLots .= " AND categories.code = '$category'";
    }

    $requestLots .= " ORDER BY create_date DESC
    LIMIT 6";

    $resultLot = mysqli_query($connection, $requestLots);

    if (!$resultLot) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($resultLot, MYSQLI_ASSOC);

    return $lots;
}

function getLot($connection, $lotId)
{
    $requestLot = "SELECT lots.name, description, lots.id, categories.name as category, image_url, price, end_date
    FROM lots JOIN categories
    WHERE lots.category_id = categories.id AND lots.id = $lotId
    ORDER BY create_date DESC";
    $resultLot = mysqli_query($connection, $requestLot);

    if (!$resultLot) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($resultLot, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция сохранения значения полей формы после валидации
 * @param $name - поле ввода
 */
function getPostVal($name)
{
    return $_POST[$name] ?? "";
}

function validate($field, &$errors, $errorText, $filter)
{
    if (empty($_POST[$field])) {
        $errors[$field] = $errorText;

        return false;
    }

    $fieldValue = filter_input(INPUT_POST, $field, $filter);
    return $fieldValue;
}

function validateFloatNumber($field, &$errors, $errorText, $errorValidateText, $minRate = null, $errorMinRateValidateText = null)
{
    if (!isset($_POST[$field]) || $_POST[$field] === "") {
        $errors[$field] = $errorText;

        return false;
    }

    $fieldValue = filter_input(INPUT_POST, $field, FILTER_VALIDATE_FLOAT);

    if ($fieldValue < 1) {
        $errors[$field] = $errorValidateText;

        return false;
    }

    if ($fieldValue < $minRate) {
        $errors[$field] = $errorMinRateValidateText;

        return false;
    }

    return $fieldValue;
}

function validateIntNumber($field, &$errors, $errorText, $errorValidateText)
{
    if (!isset($_POST[$field]) || $_POST[$field] === "") {
        $errors[$field] = $errorText;
        return false;
    }

    $fieldValue = filter_input(INPUT_POST, $field, FILTER_VALIDATE_INT);

    if ($fieldValue <= 0) {
        $errors[$field] = $errorValidateText;

        return false;
    }

    return $fieldValue;
}

function validateDate($date, &$errors, $errorText, $errorValidateText)
{
    $dateField = $_POST[$date];

    if (empty($dateField)) {
        $errors[$date] = $errorText;

        return false;
    }

    $currentDate = time();
    $lotDateUnix = strtotime("$dateField +1 day");

    $dateDiff = $lotDateUnix - $currentDate;

    if ($dateDiff <= 86400) {
        $errors[$date] = $errorValidateText;

        return false;
    }

    return $dateField;
}

function checkSession()
{
    if (!isset($_SESSION['userName'])) {
        return 0;
    }

    return 1;
}

function getLotsQtyBySearch($connection, $searchText)
{
    $lotsSql = "SELECT COUNT(*) AS cnt FROM lots WHERE MATCH(name,description) AGAINST('$searchText')";

    $lotsResult = mysqli_query($connection, $lotsSql);

    if (!$lotsResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);

    return $allLots[0]['cnt'];
}

function searchLot($connection, $searchText, $page)
{
    $searchText = mysqli_real_escape_string($connection, $searchText);
    $requestSearch =  "SELECT * FROM lots WHERE MATCH(name,description) AGAINST('$searchText') LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);
    $resultSearch = mysqli_query($connection, $requestSearch);

    if (!$resultSearch) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($resultSearch, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция sql запроса на создание нового лота
 * @param $lotName - имя лота
 * @param $lotCategory - категория лота
 * @param $lotDescription - описание лота
 * @param $fileUrl - url адрес изображения лота
 * @param $lotRate - начальная ставка лота
 * @param $lotStep - шаг ставки лота
 * @param $lotDate - дата окончания лота
 */
function addLot($imageUrl)
{
    return "INSERT INTO lots (create_date, user_id, name, category_id, description, price, price_step, end_date, image_url) VALUES (NOW(), 1, ?, ?, ?, ?, ?, ?, '$imageUrl')";
}

function redirect($id)
{
    header("Location: " . $id);

    exit;
}

function getLotsQtyByCategory($connection, $category)
{
    $lotsSql = "SELECT COUNT(*) AS cnt FROM lots WHERE lots.category_id = '$category'";

    $lotsResult = mysqli_query($connection, $lotsSql);

    if (!$lotsResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);

    return $allLots[0]['cnt'];
}

function getLotsByCategory($connection, $category, $page)
{
    $lotsSqlLimit = "SELECT * FROM lots WHERE lots.category_id = '$category' LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);

    $resultLot = mysqli_query($connection, $lotsSqlLimit);

    if (!$resultLot) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($resultLot, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция вывода ставок по лоту
 * @param $connection - подключение
 * @param $lotId - id лота
 */
function lotRates($connection, $lotId)
{
    $lotRatesSql = "SELECT rates.rate_date, rates.sum, users.name
    FROM rates
    JOIN users
    ON users.id = rates.user_id
    WHERE rates.lot_id = '$lotId'
    ORDER BY rates.sum DESC
    LIMIT 10";

    $lotRatesResult = mysqli_query($connection, $lotRatesSql);

    if (!$lotRatesResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotRates = mysqli_fetch_all($lotRatesResult, MYSQLI_ASSOC);

    return $lotRates;
}

/**
 * Функция возврата значения текущей ставки и минимальной ставки
 * @param int $lotId - id лота
 * @param int $step - шаг ставки лота
 */
function lotMinRate($connection, $lotId, $step = null)
{
    $check = "SELECT * FROM rates WHERE lot_id = '$lotId'";
    $checkResult = mysqli_query($connection, $check);

    if (mysqli_num_rows($checkResult) === 0) {
        if (true !== $step) {
            $lotMinRateSql = "SELECT price as min_rate";
        } else {
            $lotMinRateSql = "SELECT price + price_step as min_rate";
        }

        $lotMinRateSql .= " FROM lots
        WHERE id = '$lotId'";
    } else {
        if (true !== $step) {
            $lotMinRateSql = "SELECT rates.sum as min_rate";
        } else {
            $lotMinRateSql = "SELECT rates.sum  + lots.price_step as min_rate";
        }

        $lotMinRateSql .= " FROM rates
        JOIN lots
        ON rates.lot_id = lots.id
        WHERE rates.lot_id = '$lotId'
        AND rates.sum IS NOT NULL
        ORDER BY sum DESC
        LIMIT 1";
    }

    $lotMinRateResult = mysqli_query($connection, $lotMinRateSql);

    if (!$lotMinRateResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotMinRate = mysqli_fetch_all($lotMinRateResult, MYSQLI_ASSOC);

    return $lotMinRate;
}

/**
 * Функция вывода ставок пользователя
 * @param $userId - id пользователя
 */

function getLotsRates($connection, $userId)
{
    $lotsRatesSql = "SELECT lots.image_url, lots.name, lots.id, categories.name as category, lots.end_date, lots.winner_id, rates.rate_date, rates.sum
    FROM users
    JOIN rates
    ON users.id = rates.user_id
    JOIN lots
    ON rates.lot_id = lots.id
    JOIN categories
    ON lots.category_id = categories.id
    WHERE users.id = $userId
    ORDER BY rates.rate_date DESC";
    $lotsRatesSqlResult = mysqli_query($connection, $lotsRatesSql);

    if (!$lotsRatesSqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotsRates = mysqli_fetch_all($lotsRatesSqlResult, MYSQLI_ASSOC);

    return $lotsRates;
}

/**
 * Функция, в которой считаем сколько прошло времени с момента ставки
 * @param $rateDate - дата ставки
 */
function lotRateDifference($rateDate)
{
    $currentDate = time();
    $lotDateUnix = strtotime($rateDate);

    $lotCountdown = $currentDate - $lotDateUnix;

    return $lotCountdown;
}

/**
 * Функция, в которой получаем часы/минуты сколько прошло времени с момента ставки
 * @param $value - время в секундах
 */
function lotRateCount($value)
{
    $allMinutes = floor($value / 60);
    $hours = floor($allMinutes / 60);
    $minute = $allMinutes - $hours * 60;

    $res = [
        $hours, $minute
    ];

    return $res;
}

function addRate($userId, $lotId)
{
    return "INSERT INTO rates (sum, rate_date, user_id, lot_id) VALUES (?, NOW(), $userId, $lotId)";
}

// function lotLastRate($connection, $lotId)
// {
//     $sql = "SELECT user_id FROM rates WHERE lot_id = '$lotId' ORDER BY rate_date DESC LIMIT 1";
//     $sqlResult = mysqli_query($connection, $sql);

//     if (!$sqlResult) {
//         $error = mysqli_error($connection);
//         print("Ошибка MySQL: " . $error);
//     }

//     $lotLastRate = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

//     return $lotLastRate;
// }
