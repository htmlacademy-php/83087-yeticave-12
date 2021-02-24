<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$pageСontent = include_template(
    'login.php',
    [
        'categories' => $allCategories,

        'errors' => $errors,
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Вход',

        'isAuth' => '',

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
