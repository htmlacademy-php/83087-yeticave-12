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
    $userPassword = validate('password', $errors, 'Введите пароль', FILTER_DEFAULT);
    if (mb_strlen($userPassword) > DEFAULT_LENGTH_LIMIT) {
        $errors['password'] = "Пароль не может быть длинее 128 символов";
    }
    $userName = validate('name', $errors, 'Введите имя', FILTER_SANITIZE_SPECIAL_CHARS);
    if (mb_strlen($userName) > DEFAULT_LENGTH_LIMIT) {
        $errors['name'] = "Имя слишком длинное";
    }
    $userContact = validate('message', $errors, 'Напишите как с вами связаться', FILTER_SANITIZE_SPECIAL_CHARS);
    if (mb_strlen($userContact) > TEXT_LENGTH_LIMIT) {
        $errors['message'] = "Вы превысили допустимую длину текста";
    }

    if ($userEmail === false) {
        $errors['email'] = "Данный E-mail адрес не валидный";
    } elseif (mb_strlen($userEmail) > DEFAULT_LENGTH_LIMIT) {
        $errors['email'] = "Длина E-mail превышает допустимый размер";
    } else {
        $emailCheck = mysqli_real_escape_string($dbConnection, $userEmail);
        $sqlEmailCheck = "SELECT id FROM users WHERE email = '$emailCheck'";
        $resEmailCheck = mysqli_query($dbConnection, $sqlEmailCheck);

        if (mysqli_num_rows($resEmailCheck) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }

    if (count($errors)) {
        $pageContent = include_template(
            'sign-up.php',
            [
                'errors' => $errors,
            ]
        );
    } else {
        $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (reg_date, email, name, password, contact)
        VALUES (NOW(), '$userEmail', '$userName', '$userPasswordHash', '$userContact')";

        if (mysqli_query($dbConnection, $sql)) {
            redirect('login.php');
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($dbConnection);
        }
        mysqli_close($dbConnection);
    }
}

$pageContent = include_template(
    'sign-up.php',
    [
        'errors' => $errors,
    ]
);

$layoutContent = include_template(
    'layout.php',
    [
        'categories' => $allCategories,

        'content' => $pageContent,

        'title' => 'Регистрация',
    ]
);

print($layoutContent);
