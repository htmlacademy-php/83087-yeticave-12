<?php
require_once('functions.php');

$config = require 'config.php';
$dbConnection = getConnection($config);

$lotsNoWinner = lotsWithoutWinner($dbConnection);

foreach ($lotsNoWinner as $lotNoWinner) {
    $lotId = $lotNoWinner['id'];

    $userId = intval(winnerUserId($dbConnection, $lotId));
    updateWinner($dbConnection, $lotId, $userId);
}
