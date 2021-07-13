<?php
if (!defined('LOTS_PER_PAGE')) define('LOTS_PER_PAGE', 9);
if (!defined('NAME_LENGTH_LIMIT')) define('NAME_LENGTH_LIMIT', 128);
if (!defined('TEXT_LENGTH_LIMIT')) define('TEXT_LENGTH_LIMIT', 65535);
if (!defined('LOT_PRICE_LIMIT')) define('LOT_PRICE_LIMIT', 65);
if (!defined('LOT_RATE_LIMIT')) define('LOT_RATE_LIMIT', 2147483647);

return [
    'db' => [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'database' => 'yeticave',
    ]
];
