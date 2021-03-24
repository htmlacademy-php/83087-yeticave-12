<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentCategory = $_GET['code'];

$trurl = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_URL);

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$categoryName = getCategoryName($dbConnection, $currentCategory);

$allLots = getLots($dbConnection, $trurl);
$limit = 1;
$currentCategoryPage = intval($_GET['page']) ?: 1;
$offset = ($currentCategoryPage - 1) * $limit;
$lotsOnPage = array_slice($allLots, $offset, $limit, true);
$lotsQuantity = count($allLots);
$pages = $lotsQuantity / $limit;
$pagesTotal = ceil($pages);

pagination($dbConnection);

$pageContent = include_template(
    'category.php',
    [
        'categories' => $allCategories,

        'lots' => $lotsOnPage,

        'categoryName' => $categoryName,

        'pagesTotal' => $pagesTotal,

        'trurl' => $trurl,

        'currentCategoryPage' => $currentCategoryPage,
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => $categoryName,

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutСontent);
