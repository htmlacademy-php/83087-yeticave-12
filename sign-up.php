<?php
require_once('helpers.php');
require_once('functions.php');

$config = require 'config.php';

$dbConnection = getConnection($config);

$allCategories = getCategories($dbConnection);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $userEmail = validate('email', $errors, 'Введите e-mail', FILTER_VALIDATE_EMAIL);
    $userPassword = validate('password', $errors, 'Введите пароль', FILTER_DEFAULT);
    $userName = validate('name', $errors, 'Введите имя', FILTER_SANITIZE_SPECIAL_CHARS);
    $userContact = validate('message', $errors, 'Напишите как с вами связаться', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($userEmail === false) {
        $errors['email'] = "Данный E-mail адрес не валидный";
    } else {
        $emailCheck = mysqli_real_escape_string($dbConnection, $userEmail);
        $sqlEmailCheck = "SELECT id FROM users WHERE email = '$emailCheck'";
        $resEmailCheck = mysqli_query($dbConnection, $sqlEmailCheck);

        if (mysqli_num_rows($resEmailCheck) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }

    if (count($errors)) {
        $pageСontent = include_template(
            'sign-up.php',
            [
                'categories' => $allCategories,

                'errors' => $errors,
            ]
        );
    } else {
        $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (reg_date, email, name, password, contact) VALUES (NOW(), '$userEmail', '$userName', '$userPasswordHash', '$userContact')";

        if (mysqli_query($dbConnection, $sql)) {
            header("Location: login.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($dbConnection);
        }
        mysqli_close($dbConnection);
    }
}

$pageСontent = include_template(
    'sign-up.php',
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

        'title' => 'Регистрация',
    ]
);

print($layoutСontent);
