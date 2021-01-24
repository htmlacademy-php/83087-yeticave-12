<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$pageСontent = include_template(
    'main.php',
    [
        'categories' => getCategories(getConnection($config)),

        'lots' => getLots(getConnection($config), "SELECT lots.name, lots.id, categories.name as category, image_url, price, end_date
        FROM lots JOIN categories
        WHERE lots.category_id = categories.id
        ORDER BY create_date DESC"),
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => getCategories(getConnection($config)),

        'content' => $pageСontent,

        'title' => 'Главная',

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
