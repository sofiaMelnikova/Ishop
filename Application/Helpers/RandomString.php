<?php

namespace Application\Helpers;


class RandomString
{
    /**
     * @param int $length
     * @param string $symbols
     * @return string
     */
    public static function get (int $length = 12, string $symbols = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'):string {
        $countSymbols = strlen($symbols);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $symbols[rand(0, $countSymbols - 1)];
        }
        return $result;
    }
}