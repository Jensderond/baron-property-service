<?php

declare(strict_types=1);

namespace App\Helpers;

class ArrayHelper
{
    /**
     * Recursively sorts an array by keys and values.
     *
     * @param array $array The array to sort.
     * @return void
     */
    public static function sort(array &$array): void
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        ksort($array);
    }
}
