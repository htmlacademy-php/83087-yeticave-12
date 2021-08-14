<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$lots = getLot($dbConnection, $trid);

if ($lots[0]['id'] === null) {
    redirect('404.php');
}

$lotRates = lotRates($dbConnection, $trid);

$userId = $_SESSION['userId'] ?? '';

if (!empty($lots)) {
    $pageContent = include_template(
        'lot.php',
        [
            'id' => $trid,

            'lots' => $lots,

            'lotRates' => $lotRates,

            'lotRateQty' => count($lotRates),

            'connection' => $dbConnection,

            'userId' => intval($userId),
        ]
    );

    $title = $lots[0]['name'];
} else {
    http_response_code(404);

    $pageContent = include_template(
        '404.php',
    );

    $title = 'Ошибка';
}

if (checkSession()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = [];

        $lotCost = validateFloatNumber(
            'cost',
            $errors,
            'Поле не может быть пустым',
            'Введите минимальную ставку',
            lotMinRate($dbConnection, $trid),
            'Ставка не может быть ниже минимальной',
        );

        if ($lotCost > LOT_PRICE_LIMIT) {
            $errors['cost'] = 'Ставка не может быть равна или больше ' . LOT_PRICE_LIMIT;
        }

        if (count($errors)) {
            $pageContent = include_template(
                "lot.php",
                [
                    'id' => $trid,

                    'lots' => $lots,

                    'lotRates' => $lotRates,

                    'lotRateQty' => count($lotRates),

                    'connection' => $dbConnection,

                    'errors' => $errors,

                    'userId' => $userId,
                ]
            );
        } else {
            addRate($dbConnection, $userId, $trid);
        }
    }
}

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => $title,

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'] ?? '',
    ]
);

print($layoutContent);
