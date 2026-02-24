<?php

declare(strict_types=1);

namespace App\Helpers;

class ArrayHelper
{
    /**
     * Recursively sorts an array by keys and values.
     *
     * @param  array  $array  The array to sort.
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

    /**
     * Safely gets a value from a nested array using dot notation.
     *
     * @param  array  $data  The source array
     * @param  string  $path  The dot-separated path (e.g., 'project.algemeen.provincie')
     * @param  mixed  $default  The default value if not found
     * @return mixed The value or default
     */
    public static function safeGet(array $data, string $path, mixed $default = null): mixed
    {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (! is_array($current) || ! array_key_exists($key, $current)) {
                return $default;
            }
            $current = $current[$key];
        }

        return $current;
    }

    /**
     * Safely gets a date value from a nested array and converts it to DateTimeImmutable.
     *
     * @param  array  $data  The source array
     * @param  string  $path  The dot-separated path
     * @param  \DateTimeImmutable|null  $default  The default value if not found or invalid
     * @return \DateTimeImmutable|null The date or default
     */
    public static function safeGetDate(array $data, string $path, ?\DateTimeImmutable $default = null): ?\DateTimeImmutable
    {
        $dateString = self::safeGet($data, $path);
        if (! $dateString) {
            return $default;
        }

        try {
            return new \DateTimeImmutable($dateString);
        } catch (\Exception) {
            return $default;
        }
    }

    /**
     * Safely gets a numeric value from a nested array.
     *
     * @param  array  $data  The source array
     * @param  string  $path  The dot-separated path
     * @param  int|float  $default  The default value if not found or invalid
     * @return int|float The numeric value or default
     */
    public static function safeGetNumeric(array $data, string $path, int|float $default = 0): int|float
    {
        $value = self::safeGet($data, $path, $default);

        if (is_numeric($value)) {
            return is_float($value) || str_contains((string) $value, '.') ? (float) $value : (int) $value;
        }

        return $default;
    }

    /**
     * Remaps the v3 'link' field back to 'url' in media arrays for backward compatibility.
     *
     * @param  array  $media  The media array from v3 API response
     * @return array The media array with 'link' renamed to 'url'
     */
    public static function remapMediaLinks(array $media): array
    {
        return array_map(function ($item) {
            if (is_array($item) && array_key_exists('link', $item) && !array_key_exists('url', $item)) {
                $item['url'] = $item['link'];
                unset($item['link']);
            }

            return $item;
        }, $media);
    }

    /**
     * Combines area values with "tot" if they differ.
     *
     * @param  string|null  $from  The from value
     * @param  string|null  $to  The to value
     * @return string The combined string or empty string
     */
    public static function combineAreaValues(?string $from, ?string $to): string
    {
        if (! $from) {
            return '';
        }

        if ($to && $from !== $to) {
            return $from.' tot '.$to;
        }

        return $from;
    }
}
