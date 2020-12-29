<?php
require_once('helpers.php');
require_once('functions.php');

$pageСontent = include_template('main.php');

$layoutСontent = include_template(
    'layout.php',
    [
        // 'categories' => $categories,

        'content' => $pageСontent,

        'title' => 'Главная',

        'isAuth' => rand(0, 1),

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
