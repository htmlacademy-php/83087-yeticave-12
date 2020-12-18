<?php
require_once('helpers.php');
require_once('functions.php');

$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];

$page_content = include_template(
    'main.php',
    [
        'categories' => $categories,

        'announcement' => [
            [
                'name' => '2014 Rossignol District Snowboard',
                'category' => 'Доски и лыжи',
                'price' => 10999,
                'image_url' => 'img/lot-1.jpg',
                'lot_date' => '2020-12-19',
            ],
            [
                'name' => 'DC Ply Mens 2016/2017 Snowboard',
                'category' => 'Доски и лыжи',
                'price' => 159999,
                'image_url' => 'img/lot-2.jpg',
                'lot_date' => '2021-01-14',
            ],
            [
                'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
                'category' => 'Крепления',
                'price' => 8000,
                'image_url' => 'img/lot-3.jpg',
                'lot_date' => '2021-01-15',
            ],
            [
                'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
                'category' => 'Ботинки',
                'price' => 10999,
                'image_url' => 'img/lot-4.jpg',
                'lot_date' => '2021-01-16',
            ],
            [
                'name' => 'Куртка для сноуборда DC Mutiny Charocal',
                'category' => 'Одежда',
                'price' => 7500,
                'image_url' => 'img/lot-5.jpg',
                'lot_date' => '2021-01-17',
            ],
            [
                'name' => 'Маска Oakley Canopy',
                'category' => 'Разное',
                'price' => 5400,
                'image_url' => 'img/lot-6.jpg',
                'lot_date' => '2021-01-18',
            ],
        ],
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'categories' => $categories,

        'content' => $page_content,

        'title' => 'Главная',

        'is_auth' => rand(0, 1),

        'user_name' => 'Павел',
    ]
);

print($layout_content);
