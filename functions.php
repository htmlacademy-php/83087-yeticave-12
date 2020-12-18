<?php

/**
 * Функция для форматирования суммы и добавления к ней знака рубля
 * @param int $number_rate сумма лота
 * @return string Отформатированная сумма вместе со знаком рубля
 */
function formatPrice($number_rate)
{
    $number_rate = ceil($number_rate);

    if ($number_rate >= 1000) {
        $number_rate = number_format($number_rate, 0, ".", " ");
    }

    return $number_rate . ' ₽';
}

/**
 * Функция возврата оставшегося времени лота в формате ЧЧ:ММ
 * @param string $lot_date дата вида - ГГГГ-ММ-ДД
 */
function getDifferenceTime($lot_date)
{
    $current_date = time();
    $lot_date_unix = strtotime($lot_date);

    $date_diff = $lot_date_unix - $current_date;
    $all_minutes = floor($date_diff / 60);
    $hours = floor($all_minutes / 60);
    $minute = $all_minutes - $hours * 60;

    $res = [
        $hours, $minute
    ];

    return $res;
}
