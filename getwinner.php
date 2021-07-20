<?php
session_start();
require_once('vendor/autoload.php');
require_once('functions.php');

$config = require 'config.php';
$dbConnection = getConnection($config);

$lotsWithoutWinner = lotsWithoutWinner($dbConnection);

foreach ($lotsWithoutWinner as $lotWithoutWinner) {
    $lotId = $lotWithoutWinner['id'];
    $userId = intval(getWinnerId($dbConnection, $lotId));
    updateWinner($dbConnection, $lotId, $userId);

    $userData = getUserData($dbConnection, $userId);
    $userEmail = $userData[0]['email'];
    $userName = $userData[0]['name'];
    $lotName = getLot($dbConnection, $lotId);

    $transport = (new Swift_SmtpTransport($config['swiftmailer']['host'], $config['swiftmailer']['port']))
        ->setUsername($config['swiftmailer']['username'])
        ->setPassword($config['swiftmailer']['password']);
    $mailer = new Swift_Mailer($transport);
    $messageContent = include_template(
        'email.php',
        [
            'userName' => $userName,
            'lotId' => $lotId,
            'lotName' => $lotName[0]['name'],
        ]
    );
    $message = (new Swift_Message('Ваша ставка победила'))
        ->setFrom(['keks@phpdemo.ru'])
        ->setTo([$userEmail])
        ->setBody($messageContent, 'text/html');

    $result = $mailer->send($message);
}
