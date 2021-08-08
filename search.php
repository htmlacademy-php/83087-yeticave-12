<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);
$allCategories = getCategories($dbConnection);

$searchedLotsQty = getLotsQtyBySearch($dbConnection, $_GET['search']);
if (isset($_GET['page'])) {
    $currentSearchPage = intval($_GET['page']);
} else {
    $currentSearchPage = 1;
}
$pages = $searchedLotsQty / LOTS_PER_PAGE;
$totalPages = ceil($pages);
$searchedLots = searchLot($dbConnection, trim($_GET['search']), $currentSearchPage);

if (!empty($searchedLots)) {
    $pageСontent = include_template(
        'search.php',
        [
            'lots' => $searchedLots,

            'searchedWord' => $_GET['search'],

            'currentSearchPage' => $currentSearchPage,

            'totalPages' => $totalPages,
        ]
    );
} else {
    $pageСontent = include_template(
        'search.php',
        [
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

        'userName' => $_SESSION['userName'] ?? '',
    ]
);

print($layoutСontent);
