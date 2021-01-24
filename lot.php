<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$dbConnection = getConnection($config);

$lots = getLot($dbConnection, $currentLot);

// echo '<pre>';
// print_r($lots);
// echo '</pre>';
// echo $lots[0]['name'];

if (!empty($lots)) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $currentLot,

            'categories' => getCategories($dbConnection),

            'lots' => $lots,
        ]
    );

    $title = $lots[0]['name'];
} else {
    http_response_code(404);

    $pageСontent = include_template(
        '404.php',
        [
            'categories' => getCategories($dbConnection),
        ]
    );

    $title = 'Ошибка';
}

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => getCategories($dbConnection),

        'content' => $pageСontent,

        'title' => $title,

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
