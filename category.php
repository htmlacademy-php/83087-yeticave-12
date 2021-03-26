<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentCategory = $_GET['id'];

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$categoryName = getCategoryName($dbConnection, $currentCategory);

$lotsQtyByCategory = getLotsQtyByCategory($dbConnection, $currentCategory);
$currentCategoryPage = intval($_GET['page']) ?: 1;
$pages = $lotsQtyByCategory / LOTS_PER_PAGE;
$totalPages = ceil($pages);
$lotsByCategory = getLotsByCategory($dbConnection, $currentCategory, $pages);

$pageContent = include_template(
    'category.php',
    [
        'categories' => $allCategories,

        'lots' => $lotsByCategory,

        'categoryName' => $categoryName,

        'totalPages' => $totalPages,

        'categoryId' => $currentCategory,

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
