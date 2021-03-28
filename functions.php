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

function stripTags($str)
{
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lotDate дата вида - ГГГГ-ММ-ДД
 */
function getDifferenceTime($lotDate)
{
    $currentDate = time();
    $lotDateUnix = strtotime($lotDate);

    $dateDiff = $lotDateUnix - $currentDate;
    $allMinutes = floor($dateDiff / 60);
    $hours = floor($allMinutes / 60);
    $minute = $allMinutes - $hours * 60;

    $res = [
        $hours, $minute
    ];

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

function validateFloatNumber($field, &$errors, $errorText, $errorValidateText)
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

function searchLot($connection, $searchText)
{
    $searchText = mysqli_real_escape_string($connection, $searchText);
    $requestSearch =  "SELECT * FROM lots WHERE MATCH(name,description) AGAINST('$searchText')";
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
function addLot($lotName, $lotCategory, $lotDescription, $fileUrl, $lotRate, $lotStep, $lotDate)
{
    return "INSERT INTO lots (create_date, user_id, name, category_id, description, image_url, price, price_step, end_date) VALUES (NOW(), 1, '$lotName', '$lotCategory', '$lotDescription', '$fileUrl', '$lotRate', '$lotStep', '$lotDate')";
}

function redirect($id)
{
    header("Location: " . $id);

    exit;
}

function pagination($connection, $category)
{
    $lotsSql = "SELECT COUNT(*) FROM lots WHERE lots.category_id = '$category'";

    $lotsSqlLimit = "SELECT COUNT(*) FROM lots WHERE lots.category_id = 2 LIMIT 6";

    $lotsSqlOffset = "SELECT COUNT(*) FROM lots WHERE lots.category_id = 2 LIMIT 6 OFFSET 6";

    $lotsResult = mysqli_query($connection, $lotsSql);

    if (!$lotsResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);

    var_dump($allLots);
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

define("LOTS_PER_PAGE", 2);

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
