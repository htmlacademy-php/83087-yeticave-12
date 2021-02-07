<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

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
        'category' => function () {
            return validateFilled('category');
        },
        'message' => function () {
            return validateFilled('message');
        },
        'lot-rate' => function () {
            return validateNumber('lot-rate');
        },
        'lot-step' => function () {
            return validateNumber('lot-step');
        },
        'lot-date' => function () {
            return validateDate('lot-date');
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
    }

    $errors = array_filter($errors);

    if (!empty($_FILES['file']['name'])) {
        $fileNameOriginal = $_FILES['file']['name'];
        $fileType = $_FILES['file']['type'];
        $fileTemporaryName = $_FILES['file']['tmp_name'];
        $filePath = __DIR__ . '/uploads/';
        $fileUrl = '/uploads/' . $fileNameOriginal;

        $mimetype = mime_content_type($fileTemporaryName);

        if (in_array($mimetype, array('image/jpeg', 'image/png'))) {
            move_uploaded_file($fileTemporaryName, $filePath . $fileNameOriginal);
        } else {
            $errors['file'] = 'Загрузите изображение в формате png/jpg/jpeg';
        }

        echo '<pre>';
        print_r($fileNameOriginal . ' - это fileNameOriginal <br>');
        print_r($fileType . ' - это fileType <br>');
        print_r($fileTemporaryName . ' - это fileTemporaryName <br>');
        print_r($filePath . ' - это filePath <br>');
        print_r($fileUrl . ' - это fileUrl <br>');
        print_r($mimetype . ' - это mimetype <br>');
        echo '</pre>';
    } else {
        $errors['file'] = 'Добавьте изображение лота';
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
