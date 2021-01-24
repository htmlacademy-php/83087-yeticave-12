<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$lots = getLots(getConnection($config), "SELECT lots.name, description, lots.id, categories.name as category, image_url, price, end_date
            FROM lots JOIN categories
            WHERE lots.category_id = categories.id AND lots.id = $currentLot
            ORDER BY create_date DESC");

if (!empty($lots)) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $currentLot,

            'categories' => getCategories(getConnection($config)),

            'lots' => $lots,
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => getCategories(getConnection($config)),

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
            'categories' => getCategories(getConnection($config)),
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => getCategories(getConnection($config)),

            'content' => $pageСontent,

            'title' => 'Ошибка',

            'isAuth' => rand(0, 1),

            'userName' => 'Павел',
        ]
    );
}

print($layoutСontent);
