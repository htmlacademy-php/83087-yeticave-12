<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$pageСontent = include_template(
    'main.php',
    [
        'categories' => getCategories($dbConnection),

        'lots' => getLots($dbConnection),
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => getCategories($dbConnection),

        'content' => $pageСontent,

        'title' => 'Главная',

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
