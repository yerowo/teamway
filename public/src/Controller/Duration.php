<?php


namespace App\Controller;

use DateTime;

final class Duration
{
    const WORK_SHIFT_DURATION = 8;

    /**
     * Checks if the input date is valid.
     *
     * @param string $date The date string in "Y-m-d" format.
     *
     * @return bool True if the date is valid, false otherwise.
     *
     * @throws InvalidArgumentException if the input date is not a string.
     * @throws RuntimeException if the input date is invalid.
     */
    public static function workDateIsValid(string $date): bool
    {
        if (!is_string($date)) {
            throw new InvalidArgumentException("Input date must be a string.");
        }

        $dt = DateTime::createFromFormat("Y-m-d", $date);
        if (!$dt) {
            throw new RuntimeException("Invalid input date: $date");
        }

        $lastErrors = $dt->getLastErrors();
        if ($lastErrors['warning_count'] > 0 || $lastErrors['error_count'] > 0) {
            throw new RuntimeException("Invalid input date: $date");
        }

        return true;
    }

    /**
     * Formats the input timestamp to the specified date format.
     *
     * @param int $timestamp The Unix timestamp.
     * @param string $format The output date format string (optional).
     *
     * @return string The formatted date string.
     *
     * @throws InvalidArgumentException if the input timestamp is not an integer.
     */
    public static function formatTimeStamp(int $timestamp, string $format = 'd M, Y'): string
    {
        if (!is_int($timestamp)) {
            throw new InvalidArgumentException("Input timestamp must be an integer.");
        }

        return date($format, $timestamp);
    }
}
