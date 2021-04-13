<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$lots = getLot($dbConnection, $trid);

$lotRates = lotRates($dbConnection, $trid);

$userId = $_SESSION['userId'];

$lotMinRate = lotMinRate($dbConnection, $trid, true);

if (!empty($lots)) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $trid,

            'categories' => $allCategories,

            'lots' => $lots,

            'lotRates' => $lotRates,

            'lotRateQty' => count($lotRates),

            'currentPrice' => lotMinRate($dbConnection, $trid),

            'lotMinRate' => $lotMinRate,
        ]
    );

    $title = $lots[0]['name'];
} else {
    http_response_code(404);

    $pageСontent = include_template(
        '404.php',
        [
            'categories' => $allCategories,
        ]
    );

    $title = 'Ошибка';
}

if (checkSession()) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];

        $lotCost = validateFloatNumber('cost', $errors, 'Поле не может быть пустым', 'Введите минимальную ставку', $lotMinRate[0]['min_rate'], 'Ставка не может быть ниже минимальной');

        if (count($errors)) {
            $pageСontent = include_template(
                "lot.php",
                [
                    'id' => $trid,

                    'categories' => $allCategories,

                    'lots' => $lots,

                    'lotRates' => $lotRates,

                    'lotRateQty' => count($lotRates),

                    'currentPrice' => lotMinRate($dbConnection, $trid),

                    'lotMinRate' => lotMinRate($dbConnection, $trid, true),

                    'errors' => $errors,
                ]
            );
        } else {
            $sql = addRate($userId, $trid);
            $stmt = db_get_prepare_stmt($dbConnection, $sql, $_POST);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                redirect("/lot.php?id=" . $trid);
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($dbConnection);
            }

            mysqli_close($dbConnection);
        }
    }
}

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => $title,

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutСontent);
