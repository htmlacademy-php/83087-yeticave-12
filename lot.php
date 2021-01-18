<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentLot = $_GET['id'];

$trid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$con = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}

$sqlCategories = "SELECT name, code FROM categories";
$resultCategories = mysqli_query($con, $sqlCategories);
$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

$sqlLot = "SELECT lots.name, description, lots.id, categories.name as category, image_url, price, end_date FROM lots JOIN categories WHERE lots.category_id = categories.id AND lots.id = $currentLot ORDER BY create_date DESC";
$resultLot = mysqli_query($con, $sqlLot);
$lots = mysqli_fetch_all($resultLot, MYSQLI_ASSOC);
$lotsName = $lots[$currentLot]['name'];

if (!$resultLot || !$resultCategories) {
    $error = mysqli_error($con);
    print("Ошибка MySQL: " . $error);
}

if ($trid) {
    $pageСontent = include_template(
        'lot.php',
        [
            'id' => $currentLot,

            'categories' => $categories,

            'lots' => $lots,
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => $categories,

            'content' => $pageСontent,

            'title' => $lotsName,

            'isAuth' => rand(0, 1),

            'userName' => 'Павел',
        ]
    );

    print($layoutСontent);
} else {
    http_response_code(404);

    $pageСontent = include_template(
        '404.php',
        [
            'categories' => $categories,
        ]
    );

    $layoutСontent = include_template(
        'layout.php',
        [
            'categories' => $categories,

            'content' => $pageСontent,

            'title' => 'Ошибка',

            'isAuth' => rand(0, 1),

            'userName' => 'Павел',
        ]
    );

    print($layoutСontent);
}
