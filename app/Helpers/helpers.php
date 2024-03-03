<?php

/**
 * set and display currency format
 *
 * @param  int $value
 * @return string
 */
function currencyFormat(int $value): string
{
    return "R$ " .  number_format($value, 2, ',', '.');
}
