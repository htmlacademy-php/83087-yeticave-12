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
function get_dt_range($lot_date)
{
    date_default_timezone_set('Europe/Kiev');
    $current_date = time();
    $lot_date_unix = strtotime($lot_date);

    $date_diff = $lot_date_unix - $current_date;

    $minute = floor($date_diff / 60);
    $res['hours'] = floor($minute / 60);
    $res['minute'] = $minute - $res['hours'] * 60;

    return $res;
}
