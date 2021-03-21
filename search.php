<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);
$allCategories = getCategories($dbConnection);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // $trimmedSearch = trim($_GET['search']);

    $searchedLots = searchLot($dbConnection, $_GET['search']);

    if (!empty($searchedLots)) {
        // $lotsQuantity = count($searchedLots);
        // $lotsLimit = 9;
        // $offset = 0;

        // $productsOnPage = array_slice($searchedLots, $offset, $lotsLimit, true);

        // $cur_page = $_GET['page'] ?? 1;
        // $page_items = 6;

        // $result = mysqli_query($dbConnection, "SELECT COUNT(*) as cnt FROM lots");
        // $items_count = mysqli_fetch_assoc($result)['cnt'];

        // $pages_count = ceil($items_count / $page_items);
        // $offset = ($cur_page - 1) * $page_items;

        // $pages = range(1, $pages_count);

        // $sql = 'SELECT gifs.id, title, path, like_count, users.name FROM gifs '
        //     . 'JOIN users ON gifs.user_id = users.id '
        //     . 'ORDER BY show_count DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;

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
