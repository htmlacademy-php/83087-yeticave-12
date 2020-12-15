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
