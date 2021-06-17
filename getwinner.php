<?php
require_once('vendor/autoload.php');
require_once('functions.php');

$config = require 'config.php';
$dbConnection = getConnection($config);

$lotsNoWinner = lotsWithoutWinner($dbConnection);

foreach ($lotsNoWinner as $lotNoWinner) {
    $lotId = $lotNoWinner['id'];
    $userId = intval(winnerUserId($dbConnection, $lotId));
    updateWinner($dbConnection, $lotId, $userId);

    $userEmail = winnerUserEmail($dbConnection, $userId);

    $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
        ->setUsername('36a3308ec06188')
        ->setPassword('96b20787611324');
    $mailer = new Swift_Mailer($transport);
    $messageContent = include_template('email.php', ['userName' => winnerUserName($dbConnection, $userId), 'lotId' => $lotId, 'lotName' => lotWinnerName($dbConnection, $lotId)]);
    $message = (new Swift_Message('Ваша ставка победила'))
        ->setFrom(['keks@phpdemo.ru'])
        ->setTo([$userEmail])
        ->setBody($messageContent, 'text/html');

    $result = $mailer->send($message);
}


// $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 2525))
//     ->setUsername('36a3308ec06188')
//     ->setPassword('96b20787611324');
// $mailer = new Swift_Mailer($transport);
// $messageContent = include_template('email.php', ['userName' => winnerUserName($dbConnection, 3), 'lotId' => 9, 'lotName' => lotWinnerName($dbConnection, 9)]);
// $message = (new Swift_Message('Ваша ставка победила'))
//     ->setFrom(['keks@phpdemo.ru'])
//     ->setTo(['papan41k@gmail.com'])
//     ->setBody($messageContent, 'text/html');

// $result = $mailer->send($message);
