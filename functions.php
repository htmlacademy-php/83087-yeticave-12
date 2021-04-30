<?php

/**
 * Функция для форматирования суммы и добавления к ней знака рубля
 * @param int $sum сумма лота
 * @return string Отформатированная сумма вместе со знаком рубля
 */
function formatPrice(int $sum)
{
    $sum = ceil($sum);

    if ($sum >= 1000) {
        $sum = number_format($sum, 0, ".", " ");
    }

    return $sum . ' ₽';
}

/**
 * Функция для вывода текста, которая удаляет теги HTML и PHP из строки
 * @param string $str текст
 * @return string Отформатированный текст
 */
function stripTags(string $str)
{
    $text = strip_tags($str);

    return $text;
}

/**
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lotDate дата вида - ГГГГ-ММ-ДД
 * @param bool $showSeconds вывести секунды
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

/**
 * Функция подключения к базе данных
 * @param array $config данные подключения
 */
function getConnection(array $config)
{
    $connection = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
    mysqli_set_charset($connection, "utf8");

    if ($connection == false) {
        print("Ошибка подключения: " . mysqli_connect_error());
    }

    return $connection;
}

/**
 * Функция получения категорий
 * @param object $connection соединение с базой данных
 */
function getCategories(object $connection)
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

/**
 * Функция получения названия категории
 * @param object $connection соединение с базой данных
 * @param int $id id категории
 */
function getCategoryName(object $connection, int $id)
{
    $requestCategory = "SELECT name FROM categories
    WHERE categories.id = '$id'";

    $resultCategory = mysqli_query($connection, $requestCategory);

    if (!$resultCategory) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $categoryName = mysqli_fetch_all($resultCategory, MYSQLI_ASSOC);

    return $categoryName[0]['name'];
}

/**
 * Функция получения всех лотов
 * @param object $connection соединение с базой данных
 * @param $category получение лотов по категории
 */
function getLots(object $connection, $category = null)
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

/**
 * Функция получения лота по id
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return array $lots
 */
function getLot(object $connection, int $lotId)
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
 * @param $name поле ввода
 */
function getPostVal($name)
{
    return $_POST[$name] ?? "";
}

/**
 * Функция валидации полей
 * @param $field поле ввода
 * @param $errors ошибки
 * @param string $errorText текст ошибки
 * @param $filter тип фильтра поля ввода
 */
function validate($field, &$errors, string $errorText, $filter)
{
    if (empty($_POST[$field])) {
        $errors[$field] = $errorText;

        return false;
    }

    $fieldValue = filter_input(INPUT_POST, $field, $filter);
    return $fieldValue;
}

/**
 * Функция валидации чисел с плавающей точкой чисел
 * @param $field поле ввода числа
 * @param $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 * @param $minRate минимальное число
 * @param string $errorMinRateValidateText текст ошибки минимального числа
 */
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

/**
 * Функция валидации целых чисел
 * @param $field поле ввода числа
 * @param $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 */
function validateIntNumber($field, &$errors, string $errorText, string $errorValidateText)
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

/**
 * Функция валидация даты
 * @param $date дата, которую указал пользователь
 * @param $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 */
function validateDate($date, &$errors, string $errorText, string $errorValidateText)
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

/**
 * Функция, которая проверяет запущена ли сессия
 */
function checkSession()
{
    if (!isset($_SESSION['userName'])) {
        return 0;
    }

    return 1;
}

/**
 * Функция подсчета количества лотов по поиску
 * @param object $connection соединение с базой данных
 * @param string $searchText текст поиска
 */
function getLotsQtyBySearch(object $connection, string $searchText)
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

/**
 * Функция поиска лота
 * @param object $connection соединение с базой данных
 * @param string $searchText текст поиска
 * @param int $page количество страниц, для пагинации на странице поиска
 * @return array возвращает лоты по тексту поиска
 */
function searchLot(object $connection, string $searchText, int $page)
{
    $searchText = mysqli_real_escape_string($connection, $searchText);
    $requestSearch =  "SELECT lots.image_url, lots.name, categories.name AS category, lots.price, lots.end_date FROM lots
    JOIN categories
    ON lots.category_id = categories.id
    WHERE MATCH(lots.name,lots.description) AGAINST('$searchText') LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);
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
 * @param object $connection соединение с базой данных
 * @param $imageUrl url адрес изображения лота
 */
function addLot(object $connection, array $fields)
{
    $sql = "INSERT INTO lots (create_date, user_id, name, category_id, description, price, price_step, end_date, image_url) VALUES (NOW(), 1, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($connection, $sql, $fields);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        $lotId = mysqli_insert_id($connection);

        redirect("lot.php?id=" . $lotId);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }

    mysqli_close($connection);
}

/**
 * Функция редиректа со страницы
 * @param $link - адрес страницы, на которую нужно сделать редирект
 */
function redirect($link)
{
    header("Location: " . $link);

    exit;
}

/**
 * Функция подсчета количества лотов по категории
 * @param object $connection соединение с базой данных
 * @param int $category категория лотов
 */
function getLotsQtyByCategory(object $connection, int $category)
{
    $lotsSql = "SELECT COUNT(*) AS cnt FROM lots WHERE lots.category_id = $category";

    $lotsResult = mysqli_query($connection, $lotsSql);

    if (!$lotsResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);

    return $allLots[0]['cnt'];
}

/**
 * Функция вывода лотов по категории
 * @param object $connection соединение с базой данных
 * @param int $category категория лотов
 * @param int $page количество страниц категории
 */
function getLotsByCategory(object $connection, int $category, int $page)
{
    $lotsSqlLimit = "SELECT lots.id, lots.image_url, lots.name, categories.name AS category, lots.price, lots.end_date FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.category_id = $category LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);

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
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 */
function lotRates(object $connection, int $lotId)
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
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @param int $step шаг ставки лота
 */
function lotMinRate(object $connection, int $lotId, int $step = null)
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
 * Функция вывода ставок
 * @param object $connection соединение с базой данных
 * @param int $userId id пользователя
 */
function getLotsRates(object $connection, int $userId)
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
 * @param string $rateDate дата ставки
 */
function lotRateDifference(string $rateDate)
{
    $currentDate = time();
    $lotDateUnix = strtotime($rateDate);

    $lotCountdown = $currentDate - $lotDateUnix;

    return $lotCountdown;
}

/**
 * Функция, в которой получаем часы/минуты сколько прошло времени с момента ставки
 * @param int $value время в секундах
 */
function lotRateCount(int $value)
{
    $allMinutes = floor($value / 60);
    $hours = floor($allMinutes / 60);
    $minute = $allMinutes - $hours * 60;

    $res = [
        $hours, $minute
    ];

    return $res;
}

/**
 * Функция добавления ставки
 * @param object $connection соединение с базой данных
 * @param int $userId id юзера, который добавляет ставку
 * @param int $lotId id лота, к которому добавляется ставка
 */
function addRate(object $connection, int $userId, int $lotId)
{
    $sql = "INSERT INTO rates (sum, rate_date, user_id, lot_id) VALUES (?, NOW(), $userId, $lotId)";
    $stmt = db_get_prepare_stmt($connection, $sql, $_POST);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        redirect("/lot.php?id=" . $lotId);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }

    mysqli_close($connection);
}

/**
 * Функция, которая показывает сколько времени прошло с момента ставки
 * @param string $date время ставки
 */
function lotRateDatePassed(string $date)
{
    $rateDate = $date;
    $dateFormat = date("d.m.y в H:i", strtotime($rateDate));
    $dateFormatTommorow = date("H:i", strtotime($rateDate));
    $countRateDate = lotRateDifference($date);
    $rateDatePassed = lotRateCount($countRateDate);

    if ($rateDatePassed[0] < 1) {
        echo "$rateDatePassed[1] " .
            get_noun_plural_form(
                $rateDatePassed[1],
                'минута',
                'минуты',
                'минут'
            ) . " назад";
    } elseif ($rateDatePassed[0] == 1) {
        echo "Час назад";
    } elseif ($rateDatePassed[0] > 1 && $rateDatePassed[0] < 24) {
        echo "{$rateDatePassed[0]} " .
            get_noun_plural_form(
                $rateDatePassed[0],
                'час',
                'часа',
                'часов'
            ) . " назад";
    } elseif ($rateDatePassed[0] >= 24 && $rateDatePassed[0] < 48) {
        echo "Вчера, в " . $dateFormatTommorow;
    } else {
        echo stripTags($dateFormat);
    }
}

/**
 * Функция определения лота без победителя
 * @param object $connection - соединение с базой данных
 */
function lotsWithoutWinner(object $connection)
{
    $currentDate = date('Y-m-d');
    $lotsNoWinnerSql = "SELECT id FROM lots WHERE winner_id IS NULL AND end_date <= '$currentDate'";
    $lotsNoWinnerSqlResult = mysqli_query($connection, $lotsNoWinnerSql);

    if (!$lotsNoWinnerSqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotsNoWinner = mysqli_fetch_all($lotsNoWinnerSqlResult, MYSQLI_ASSOC);

    return $lotsNoWinner;
}

/**
 * Функция, которая возвращает id юзера последней ставки
 * @param object $connection - соединение с базой данных
 * @param int $lotId - id лота
 */
function winnerUserId(object $connection, int $lotId)
{
    $userIdSql = "SELECT user_id FROM rates WHERE lot_id = $lotId ORDER BY rate_date DESC LIMIT 1";
    $userIdSqlResult = mysqli_query($connection, $userIdSql);
    $userId = mysqli_fetch_assoc($userIdSqlResult);

    return $userId;
}

/**
 * Функция записи победителя в лот
 * @param object $connection - соединение с базой данных
 * @param int $lotId - id лота
 * @param int $userId - id победителя
 */
function updateWinner(object $connection, int $lotId, int $userId)
{
    $sql = "UPDATE lots SET winner_id = $userId WHERE id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $update = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $update;
}

/**
 * Функция отображения кол-ва ставок
 * @param object $connection - соединение с базой данных
 * @param int $lotId - id лота
 */
function rateQty($connection, $lotId)
{
    $sql = "SELECT COUNT(lot_id) as rate_qty FROM rates WHERE lot_id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $rateQtyResult = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    $rateQty = $rateQtyResult[0]['rate_qty'];

    if ($rateQty > 0) {
        echo "{$rateQty} " . get_noun_plural_form($rateQty, 'ставка', 'ставки', 'ставок');
    } else {
        echo 'Стартовая цена';
    }
}
