<?php
require_once('helpers.php');
require_once('functions.php');

$layout_content = include_template(
    'layout.php',
    [
        'categories' => [
            "Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"
        ],

        'announcement' => [
            [
                'name' => '2014 Rossignol District Snowboard',
                'category' => 'Доски и лыжи',
                'price' => 10999,
                'image_url' => 'img/lot-1.jpg',
            ],
            [
                'name' => 'DC Ply Mens 2016/2017 Snowboard',
                'category' => 'Доски и лыжи',
                'price' => 159999,
                'image_url' => 'img/lot-2.jpg',
            ],
            [
                'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
                'category' => 'Крепления',
                'price' => 8000,
                'image_url' => 'img/lot-3.jpg',
            ],
            [
                'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
                'category' => 'Ботинки',
                'price' => 10999,
                'image_url' => 'img/lot-4.jpg',
            ],
            [
                'name' => 'Куртка для сноуборда DC Mutiny Charocal',
                'category' => 'Одежда',
                'price' => 7500,
                'image_url' => 'img/lot-5.jpg',
            ],
            [
                'name' => 'Маска Oakley Canopy',
                'category' => 'Разное',
                'price' => 5400,
                'image_url' => 'img/lot-6.jpg',
            ],
        ],

        'is_auth' => rand(0, 1),

        'user_name' => 'Павел',

        'title' => 'Главная',
    ]
);

print($layout_content);
