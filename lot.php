<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$lots = getLot($dbConnection, $trid);

if (!empty($lots)) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $trid,

            'categories' => $allCategories,

            'lots' => $lots,
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

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => $title,

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
