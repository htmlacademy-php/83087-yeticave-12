<?php
session_start();
require_once('helpers.php');
require_once('functions.php');
require_once('getwinner.php');

$config = require 'config.php';

$dbConnection = getConnection($config);
$allCategories = getCategories($dbConnection);

$pageContent = include_template(
    'main.php',
    [
        'categories' => $allCategories,

        'lots' => getLots($dbConnection),

        'connection' => $dbConnection,
    ]
);

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => 'Главная',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'] ?? '',

        'mainpage' => true,
    ]
);

print($layoutContent);
