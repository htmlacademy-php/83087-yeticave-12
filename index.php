<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$con = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}

$sqlCategories = "SELECT name, code FROM categories";
$resultCategories = mysqli_query($con, $sqlCategories);
$categories = mysqli_fetch_all($resultCategories, MYSQLI_ASSOC);

$sqlLot = "SELECT lots.name, lots.id, categories.name as category, image_url, price, end_date FROM lots JOIN categories WHERE lots.category_id = categories.id ORDER BY create_date DESC";
$resultLot = mysqli_query($con, $sqlLot);
$lots = mysqli_fetch_all($resultLot, MYSQLI_ASSOC);

if (!$resultLot || !$resultCategories) {
    $error = mysqli_error($con);
    print("Ошибка MySQL: " . $error);
}

$pageСontent = include_template(
    'main.php',
    [
        'categories' => $categories,

        'lots' => $lots,
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $categories,

        'content' => $pageСontent,

        'title' => 'Главная',

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
