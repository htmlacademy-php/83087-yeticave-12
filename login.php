<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userEmail = validate('email', $errors, 'Введите e-mail', FILTER_VALIDATE_EMAIL);
    $userPassword = validate('password', $errors, 'Введите пароль', FILTER_DEFAULT);

    $emailCheck = mysqli_real_escape_string($dbConnection, $userEmail);
    $sqlEmailCheck = "SELECT * FROM users WHERE email = '$emailCheck'";
    $resEmailCheck = mysqli_query($dbConnection, $sqlEmailCheck);

    $user = $resEmailCheck ? mysqli_fetch_array($resEmailCheck, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        if (password_verify($userPassword, $user['password'])) {

            $_SESSION['userName'] = $user['name'];

            $_SESSION['userId'] = $user['id'];

            header("Location: /");
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors)) {
        $pageСontent = include_template(
            'login.php',
            [
                'categories' => $allCategories,

                'errors' => $errors,
            ]
        );
    }
}

$pageСontent = include_template(
    'login.php',
    [
        'categories' => $allCategories,

        'errors' => $errors,
    ]
);

$layoutСontent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageСontent,

        'title' => 'Вход',
    ]
);

print($layoutСontent);
