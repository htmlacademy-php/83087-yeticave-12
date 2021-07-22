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
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lotDate дата вида - ГГГГ-ММ-ДД
 * @param bool $showSeconds вывести секунды
 * @return array оставшееся время
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
        $result = [
            $hours, $minute, $seconds
        ];
    } else {
        $result = [
            $hours, $minute
        ];
    }

    return $result;
}

/**
 * Функция подключения к базе данных
 * @param array $config данные подключения
 * @return object возвращает данные сервера
 */
function getConnection(array $config)
{
    $connection = mysqli_connect(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['password'],
        $config['db']['database']
    );
    mysqli_set_charset($connection, "utf8");

    if ($connection === false) {
        print("Ошибка подключения: " . mysqli_connect_error());
    }

    return $connection;
}

/**
 * Функция получения категорий
 * @param object $connection соединение с базой данных
 * @return array возвращает массив категорий
 */
function getCategories(object $connection)
{
    $sql = "SELECT id, name, code FROM categories";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $categories = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $categories;
}

/**
 * Функция получения названия категории
 * @param object $connection соединение с базой данных
 * @param int $id id категории
 * @return string возвращает название категории
 */
function getCategoryName(object $connection, int $id)
{
    $sql = "SELECT name FROM categories
    WHERE categories.id = $id";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    if (mysqli_num_rows($sqlResult) <= 0) {
        return null;
    }

    $categoryName = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $categoryName[0]['name'];
}

/**
 * Функция получения всех лотов
 * @param object $connection соединение с базой данных
 * @param string $category получение лотов по категории
 * @return array возвращает массив лотов
 */
function getLots(object $connection, string $category = null)
{
    $sql = "SELECT lots.name, lots.id, categories.name as category, image_url, end_date,
    COUNT(rates.lot_id) as rate_qty, create_date,
    IFNULL(MAX(rates.sum), price) AS current_price
	FROM lots
    INNER JOIN categories ON lots.category_id = categories.id
    LEFT JOIN rates ON lots.id = rates.lot_id
    WHERE lots.category_id = categories.id
    AND lots.end_date > CURRENT_DATE()";

    if (!empty($category)) {
        $sql .= " AND categories.code = '$category'";
    }

    $sql .= " GROUP BY lots.id ORDER BY create_date DESC
    LIMIT 6";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция получения лота по id
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return array возвращает лот по id
 */
function getLot(object $connection, int $lotId)
{
    $sql = "SELECT lots.name, description, lots.id, lots.user_id, categories.name as category, image_url, end_date,
    IFNULL(MAX(rates.sum), price) AS current_price
    FROM lots JOIN categories
    LEFT JOIN rates ON lots.id = rates.lot_id
    WHERE lots.category_id = categories.id AND lots.id = $lotId
    ORDER BY create_date DESC";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция сохранения значения полей формы после валидации
 * @param string $name поле ввода
 */
function getPostVal($name)
{
    return $_POST[$name] ?? "";
}

/**
 * Функция валидации полей
 * @param string $field поле ввода
 * @param array $errors ошибки
 * @param string $errorText текст ошибки
 * @param mixed $filter тип фильтра поля ввода
 * @return string возвращает валидорованную строку ввода
 */
function validate($field, &$errors, $errorText, $filter)
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
 * @param string $field поле ввода числа
 * @param array $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 * @param string $minRate минимальное число
 * @param string $errorMinRateValidateText текст ошибки минимального числа
 * @return string возвращает валидорованную строку ввода
 */
function validateFloatNumber(
    $field,
    &$errors,
    $errorText,
    $errorValidateText,
    $minRate = null,
    $errorMinRateValidateText = null
) {
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
 * @param string $field поле ввода числа
 * @param array $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 * @return string возвращает валидорованную строку ввода
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
 * @param string $date дата, которую указал пользователь
 * @param array $errors ошибки
 * @param string $errorText текст ошибки
 * @param string $errorValidateText текст ошибки валидации
 * @return string возвращает валидированную дату
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
 * @return bool возвращает булевое значение, запущена ли сессия или нет, в формате 1/0
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
 * @return string возвращает количество лотов по поиску
 */
function getLotsQtyBySearch(object $connection, string $searchText)
{
    $sql = "SELECT COUNT(*) AS cnt FROM lots WHERE MATCH(name,description) AGAINST('$searchText')";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

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
    $sql =  "SELECT lots.id, lots.image_url, lots.name, categories.name AS category, lots.price, lots.end_date FROM lots
    JOIN categories
    ON lots.category_id = categories.id
    WHERE MATCH(lots.name,lots.description) AGAINST('$searchText')
    LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция sql запроса на создание нового лота
 * @param object $connection соединение с базой данных
 * @param array $fields поля на создание нового лота
 */
function addLot(object $connection, array $fields)
{
    $sql = "INSERT
    INTO lots (create_date, user_id, name, category_id, description, price, price_step, end_date, image_url)
    VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
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
 * @param string $link - адрес страницы, на которую нужно сделать редирект
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
 * @return string возвращает количество лотов по категории
 */
function getLotsQtyByCategory(object $connection, int $category)
{
    $sql = "SELECT COUNT(*) AS cnt FROM lots WHERE lots.category_id = $category";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $allLots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $allLots[0]['cnt'];
}

/**
 * Функция вывода лотов по категории
 * @param object $connection соединение с базой данных
 * @param int $category категория лотов
 * @param int $page количество страниц категории
 * @return array возвращает лоты по категории
 */
function getLotsByCategory(object $connection, int $category, int $page)
{
    $sql = "SELECT lots.id, lots.image_url, lots.name, categories.name AS category, lots.end_date,
    COUNT(rates.lot_id) as rate_qty, IFNULL(MAX(rates.sum), price) AS current_price
    FROM lots
    JOIN categories ON lots.category_id = categories.id
    LEFT JOIN rates ON lots.id = rates.lot_id
    WHERE lots.category_id = $category
    AND lots.end_date > CURRENT_DATE()
    GROUP BY lots.id
    ORDER BY lots.create_date DESC
    LIMIT " . LOTS_PER_PAGE . " OFFSET " . LOTS_PER_PAGE * ($page - 1);

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lots = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lots;
}

/**
 * Функция вывода ставок по лоту
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return array возвращает ставки по лоту
 */
function lotRates(object $connection, int $lotId)
{
    $sql = "SELECT rates.rate_date, rates.sum, users.name
    FROM rates
    JOIN users
    ON users.id = rates.user_id
    WHERE rates.lot_id = $lotId
    ORDER BY rates.sum DESC
    LIMIT 10";

    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotRates = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lotRates;
}

/**
 * Функция возврата начальной цены лота
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return string возвращает начальную цену лота
 */
function startingPrice(object $connection, int $lotId)
{
    $sql = "SELECT price FROM lots WHERE id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $price = mysqli_fetch_assoc($sqlResult);

    return $price['price'];
}

/**
 * Функция возврата значения текущей ставки
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return int возвращает значение текущей ставки
 */
function currentRate(object $connection, int $lotId)
{
    $sql = "SELECT sum FROM rates WHERE lot_id = $lotId ORDER BY sum DESC LIMIT 1";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $rate = mysqli_fetch_assoc($sqlResult);

    return (int)($rate['sum'] ?? '');
}

/**
 * Функция возврата минимальной ставки
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return int возвращает значение минимальной ставки
 */
function lotMinRate(object $connection, int $lotId)
{
    $sql = "SELECT price_step FROM lots WHERE id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $currentRate = currentRate($connection, $lotId);
    $startingPrice = startingPrice($connection, $lotId);

    $rateStep = mysqli_fetch_assoc($sqlResult);

    if (!empty($currentRate)) {
        $rate = $currentRate + $rateStep['price_step'];
    } else {
        $rate = $startingPrice + $rateStep['price_step'];
    }

    return $rate;
}

/**
 * Функция вывода ставок
 * @param object $connection соединение с базой данных
 * @param int $userId id пользователя
 * @return array возвращает ставки пользователя
 */
function getLotsRates(object $connection, int $userId)
{
    $sql = "SELECT lots.image_url, lots.name, lots.id, categories.name as category, lots.end_date,
    lots.winner_id, rates.rate_date, rates.sum
    FROM users
    JOIN rates
    ON users.id = rates.user_id
    JOIN lots
    ON rates.lot_id = lots.id
    JOIN categories
    ON lots.category_id = categories.id
    WHERE users.id = $userId
    ORDER BY rates.rate_date DESC";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotsRates = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lotsRates;
}

/**
 * Функция возврата контактов владельца лота
 * @param object $connection соединение с базой данных
 * @param int $lotId id лота
 * @return string возвращает данные владельца лота
 */
function userContacts(object $connection, int $lotId)
{
    $sql = "SELECT contact FROM lots JOIN users ON lots.user_id = users.id WHERE lots.id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $contacts = mysqli_fetch_assoc($sqlResult);

    return $contacts['contact'];
}

/**
 * Функция, в которой считаем сколько прошло времени с момента ставки
 * @param string $rateDate дата ставки
 * @return int возвращает время с момента ставки
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
 * @return array возвращает часы/минуты с момента ставки
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
    } elseif ($rateDatePassed[0] === 1) {
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
        echo strip_tags($dateFormat);
    }
}

/**
 * Функция определения лота без победителя
 * @param object $connection - соединение с базой данных
 * @return array возвращает лоты без победителей
 */
function lotsWithoutWinner(object $connection)
{
    $currentDate = date('Y-m-d');
    $sql = "SELECT id FROM lots WHERE winner_id IS NULL AND end_date <= '$currentDate'";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotsWithoutWinner = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $lotsWithoutWinner;
}

/**
 * Функция, которая возвращает название лота, который был выигран
 * @param object $connection - соединение с базой данных
 * @param int $lotId - id лота
 * @return string возвращает название лота, который выигран
 */
function lotWinnerName(object $connection, $lotId)
{
    $sql = "SELECT name FROM lots WHERE id = $lotId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $lotName = mysqli_fetch_assoc($sqlResult);

    return $lotName;
}

/**
 * Функция, которая возвращает id юзера последней ставки
 * @param object $connection - соединение с базой данных
 * @param int $lotId - id лота
 * @return int возвращает id юзера последней ставки
 */
function getWinnerId(object $connection, int $lotId)
{
    $sql = "SELECT user_id FROM rates WHERE lot_id = $lotId ORDER BY rate_date DESC LIMIT 1";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $userId = mysqli_fetch_assoc($sqlResult);

    return $userId;
}

/**
 * Функция, которая возвращает email и имя  победителя
 * @param object $connection - соединение с базой данных
 * @param int $userId - id пользователя
 * @return array возвращает данные победителя
 */
function getUserData(object $connection, int $userId)
{
    $sql = "SELECT email, name FROM users WHERE id = $userId";
    $sqlResult = mysqli_query($connection, $sql);

    if (!$sqlResult) {
        $error = mysqli_error($connection);
        print("Ошибка MySQL: " . $error);
    }

    $userData = mysqli_fetch_all($sqlResult, MYSQLI_ASSOC);

    return $userData;
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
