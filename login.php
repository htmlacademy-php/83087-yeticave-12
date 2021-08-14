<?php
session_start();
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

$errors = [];

if (checkSession()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = validate('email', $errors, 'Введите e-mail', FILTER_VALIDATE_EMAIL);
    if (mb_strlen($userEmail) > DEFAULT_LENGTH_LIMIT) {
        $errors['email'] = "Длина E-mail превышает допустимый размер";
    }
    $userPassword = validate('password', $errors, 'Введите пароль', FILTER_DEFAULT);
    if (mb_strlen($userPassword) > DEFAULT_LENGTH_LIMIT) {
        $errors['password'] = "Пароль не может быть длинее 128 символов";
    }

    $emailCheck = mysqli_real_escape_string($dbConnection, $userEmail);
    $sqlEmailCheck = "SELECT * FROM users WHERE email = '$emailCheck'";
    $resEmailCheck = mysqli_query($dbConnection, $sqlEmailCheck);

    $user = $resEmailCheck ? mysqli_fetch_array($resEmailCheck, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        if (password_verify($userPassword, $user['password'])) {
            $_SESSION['userName'] = $user['name'];

            $_SESSION['userId'] = $user['id'];

            redirect('/');
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (count($errors)) {
        $pageContent = include_template(
            'login.php',
            [
                'errors' => $errors,
            ]
        );
    }
}

$pageContent = include_template(
    'login.php',
    [
        'errors' => $errors,
    ]
);

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => 'Вход',
    ]
);

print($layoutContent);
