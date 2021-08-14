<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$pageContent = include_template(
    '404.php'
);

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => 'Страница не найдена',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'] ?? '',
    ]
);

print($layoutContent);
