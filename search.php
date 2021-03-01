<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);
$allCategories = getCategories($dbConnection);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $trimmedSearch = trim($_GET['search']);

    $searchedLots = searchLot($dbConnection, $trimmedSearch);

    if (!empty($trimmedSearch)) {
        $lotsQuantity = count($searchedLots);
        $lotsLimit = 9;
        $offset = 0;

        $productsOnPage = array_slice($searchedLots, $offset, $lotsLimit, true);

        // if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        //     $url = "https://";
        // } else {
        //     $url = "http://";
        // }

        // $url .= $_SERVER['HTTP_HOST'];
        // $url .= $_SERVER['REQUEST_URI'];
        // $url .= '?page=1';

        // echo $url;

        $pageСontent = include_template(
            'search.php',
            [
                'categories' => $allCategories,

                'lots' => $productsOnPage,
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
