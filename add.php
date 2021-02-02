<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);
$categoriesId = array_column($allCategories, 'name', 'id');

echo '<pre>';
var_dump($categoriesId);
echo '</pre>';

$userCheck = $_GET['user'];

$trueUser = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT);

// if ($trueUser > 0) {

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['lot-name', 'category', 'message', 'file', 'lot-rate', 'lot-step', 'lot-date'];

    $errors = [];

    $rules = [
        'lot-name' => function () {
            return validateFilled('lot-name');
        },
        'category' => function () use ($categoriesId) {
            return validateCategory('category', $categoriesId);
        },
        'message' => function () {
            return validateFilled('message');
        },
        'lot-rate' => function () {
            return validateFilled('lot-rate');
        },
        'lot-step' => function () {
            return validateFilled('lot-step');
        },
        'lot-date' => function () {
            return validateFilled('lot-date');
        },
    ];

    $lotFields = filter_input_array(
        INPUT_POST,
        [
            'lot-name' => FILTER_DEFAULT,
            'category' => FILTER_DEFAULT,
            'message' => FILTER_DEFAULT,
            'lot-rate' => FILTER_DEFAULT,
            'lot-step' => FILTER_DEFAULT,
            'lot-date' => FILTER_DEFAULT,
        ],
        true
    );

    foreach ($lotFields as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        if (in_array($key, $required) && empty($value)) {
            $errors[$key] = "Поле $key надо заполнить";
        }
    }

    $errors = array_filter($errors);

    if (!empty($_FILES['file']['name'])) {
        $tmpName = $_FILES['file']['tmp_name'];
        $path = $_FILES['file']['name'];

        $finfo = mime_content_type($path);
        $fileType = finfo_file($finfo, $tmpName);
        if ($finfo !== "image/jpeg") {
            $errors['file'] = 'Загрузите картинку в формате GIF';
        } else {
            move_uploaded_file($tmpName, 'uploads/' . $path);
            $lotFields['path'] = $path;
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    echo '<pre>';
    print_r($_POST);
    print_r($errors);
    echo '</pre>';

    if (count($errors)) {
        $pageСontent = include_template(
            'add-lot.php',
            [
                'categories' => $allCategories,

                'errors' => $errors,
            ]
        );
    }
} else {
    $pageСontent = include_template(
        'add-lot.php',
        [
            'categories' => $allCategories,
        ]
    );
}
// } else {
//     $pageСontent = include_template(
//         'login.php',
//         [
//             'categories' => $allCategories,

//             'isAuth' => 1,
//         ]
//     );

//     $title = 'Вход';
// }

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Добавление нового лота',

        'isAuth' => 1,

        'userName' => 'Павел',
    ]
);

print($layoutСontent);
