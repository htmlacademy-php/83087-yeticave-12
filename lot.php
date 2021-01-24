<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$dbConnection = getConnection($config);

$lots = getLot($dbConnection, $currentLot);

if (!empty($lots)) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $currentLot,

            'categories' => getCategories($dbConnection),

            'lots' => $lots,
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => getCategories($dbConnection),

            'content' => $pageСontent,

            'title' => $lots[$currentLot]['name'],

            'isAuth' => rand(0, 1),

            'userName' => 'Павел',
        ]
    );
} else {
    http_response_code(404);

    $pageСontent = include_template(
        '404.php',
        [
            'categories' => getCategories($dbConnection),
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => getCategories($dbConnection),

            'content' => $pageСontent,

            'title' => 'Ошибка',

            'isAuth' => rand(0, 1),

            'userName' => 'Павел',
        ]
    );
}

print($layoutСontent);
