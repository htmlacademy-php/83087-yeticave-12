<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);
$allCategories = getCategories($dbConnection);

$searchedLots = searchLot($dbConnection, $_GET['search']);


$searchedLotsQty = count($searchedLots);
$currentPage = intval($_GET['page']) ?: 1;
$pages = $searchedLotsQty / LOTS_PER_PAGE;
$totalPages = ceil($pages);

if (!empty($searchedLots)) {
    $pageСontent = include_template(
        'search.php',
        [
            'categories' => $allCategories,

            'lots' => $searchedLots,
        ]
    );
} else {
    $pageСontent = include_template(
        'search.php',
        [
            'categories' => $allCategories,

            'errors' => 'Ничего не найдено по вашему запросу',
        ]
    );
}

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Поиск',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutСontent);
