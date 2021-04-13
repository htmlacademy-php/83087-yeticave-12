<?php
require_once('functions.php');

$config = require 'config.php';
$dbConnection = getConnection($config);

$currentDate = date('Y-m-d');
$lotsNoWinnerSql = "SELECT id FROM lots WHERE winner_id IS NULL AND end_date <= '$currentDate'";
$lotsNoWinnerSqlResult = mysqli_query($dbConnection, $lotsNoWinnerSql);

if (!$lotsNoWinnerSqlResult) {
    $error = mysqli_error($dbConnection);
    print("Ошибка MySQL: " . $error);
}

$lotsNoWinner = mysqli_fetch_all($lotsNoWinnerSqlResult, MYSQLI_ASSOC);

foreach ($lotsNoWinner as $lotNoWinner) {
    $lotId = $lotNoWinner['id'];
    $usersRateIdSql = "SELECT user_id FROM rates WHERE lot_id = '$lotId' ORDER BY rate_date DESC LIMIT 1";
    $usersRateIdSqlResult = mysqli_query($dbConnection, $usersRateIdSql);
    $usersRateId = mysqli_fetch_all($usersRateIdSqlResult, MYSQLI_ASSOC);

    foreach ($usersRateId as $userRateId) {
        $winnerId = $userRateId['user_id'];
        $winnerUpdateSql = "UPDATE lots SET winner_id = '$winnerId' WHERE id = '$lotId'";
        $winnerUpdateSqlResult = mysqli_query($dbConnection, $winnerUpdateSql);
    }
}
