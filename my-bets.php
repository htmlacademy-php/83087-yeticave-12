<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$userId = $_SESSION['userId'];

$pageСontent = include_template(
    'my-bets.php',
    [
        'categories' => $allCategories,

        'lots' => getLotsRates($dbConnection, $userId),

        'connection' => $dbConnection,

        'userId' => $userId,
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Мои ставки',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutСontent);
