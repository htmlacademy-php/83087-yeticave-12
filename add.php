<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

if (checkSession()) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required = ['lot-name', 'category', 'message', 'file', 'lot-rate', 'lot-step', 'lot-date'];

        $errors = [];

        $lotName        = validate('lot-name', $errors, 'Введите наименование лота', FILTER_SANITIZE_SPECIAL_CHARS);
        $lotCategory    = validate('category', $errors, 'Выберите категорию', FILTER_DEFAULT);
        $lotDescription = validate('message', $errors, 'Напишите описание лота', FILTER_SANITIZE_SPECIAL_CHARS);
        $lotRate        = validateFloatNumber('lot-rate', $errors, 'Введите начальную цену', 'Число должно быть больше 0');
        $lotStep        = validateIntNumber('lot-step', $errors, 'Введите шаг ставки', 'Число должно быть больше 0');
        $lotDate        = validateDate('lot-date', $errors, 'Введите дату завершения торгов', 'Дата должна быть больше текущей даты, хотя бы на один день');

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
        } else {
            $errors['file'] = 'Добавьте изображение лота';
        }

        if (count($errors)) {
            $pageСontent = include_template(
                'add-lot.php',
                [
                    'categories' => $allCategories,

                    'errors' => $errors,
                ]
            );
        } else {
            $_POST['image_url'] = $fileUrl;

            $sql = "INSERT INTO lots (create_date, user_id, name, category_id, description, price, price_step, end_date, image_url) VALUES (NOW(), 1, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = db_get_prepare_stmt($dbConnection, $sql, $_POST);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $lotId = mysqli_insert_id($dbConnection);

                redirect("lot.php?id=" . $lotId);
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($dbConnection);
            }
            mysqli_close($dbConnection);
        }
    } else {
        $pageСontent = include_template(
            'add-lot.php',
            [
                'categories' => $allCategories,
            ]
        );
    }
} else {
    http_response_code(403);
}

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Добавление нового лота',

        'isAuth' => checkSession(),

        'userName' => $_SESSION['userName'],
    ]
);

print($layoutСontent);
