<?php

namespace SamyAsm\Chrono\Traits;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Trait for Chrono utility methods
 */
trait ChronoUtilsTrait
{
    /**
     * Gets the month name from a position (1-12)
     * 
     * @param int $position Month position (1-12)
     * @return string Short month name or 'UNK' if invalid
     */
    public static function getMonthNameFromPosition(int $position): string
    {
        $months = [
            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'
        ];
        
        return $months[$position] ?? 'UNK';
    }

    /**
     * Validates a date string against a format
     * 
     * @param string $date Date string to validate
     * @param string $format Format to validate against
     * @return bool True if valid, false otherwise
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Gets the number of days in a month
     * 
     * @param int $month Numeric representation of a month (1-12)
     * @param int|null $year Year (defaults to current year)
     * @return int Number of days in the month
     */
    public static function getDaysInMonth(int $month, ?int $year = null): int
    {
        $year = $year ?? (int) date('Y');
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * Alias for getMonthNameFromPosition for backward compatibility
     * 
     * @param int $position Month position (1-12)
     * @return string Short month name or 'UNK' if invalid
     */
    public static function getMonthFromPosition(int $position): string
    {
        return self::getMonthNameFromPosition($position);
    }
}
