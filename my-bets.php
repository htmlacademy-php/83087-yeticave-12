<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$userId = $_SESSION['userId'];

$pageContent = include_template(
    'my-bets.php',
    [
        'lots' => getLotsRates($dbConnection, $userId),

        'connection' => $dbConnection,

        'userId' => $userId,
    ]
);

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => 'Мои ставки',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutContent);
