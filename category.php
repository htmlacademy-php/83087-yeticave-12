<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';
$currentCategory = intval($_GET['id']);

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$categoryName = getCategoryName($dbConnection, $currentCategory);

if ($categoryName === null) {
    redirect('404.php');
}

$lotsQtyByCategory = getLotsQtyByCategory($dbConnection, $currentCategory);

$currentCategoryPage = intval($_GET['page'] ?? 1);

$pages = $lotsQtyByCategory / LOTS_PER_PAGE;

$totalPages = ceil($pages);

$lotsByCategory = getLotsByCategory($dbConnection, $currentCategory, $currentCategoryPage);

if (!empty($lotsByCategory)) {
    $pageContent = include_template(
        'category.php',
        [
            'lots' => $lotsByCategory,

            'categoryName' => $categoryName,

            'totalPages' => $totalPages,

            'currentCategoryPage' => $currentCategoryPage,

            'categoryId' => $currentCategory,
        ]
    );
} else {
    $pageContent = include_template(
        'category-empty.php',
        [
            'categoryName' => $categoryName,
        ]
    );
}


$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => $categoryName,

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'] ?? '',

        'categoryId' => $currentCategory,
    ]
);

print($layoutСontent);
