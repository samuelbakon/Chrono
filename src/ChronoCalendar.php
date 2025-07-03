<?php

namespace SamyAsm\Chrono;

use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Handles calendar-related operations (weeks, months, years, etc.)
 */
class ChronoCalendar
{
    public const MONTHS = [
        1 => 'JAN',
        2 => 'FEB',
        3 => 'MAR',
        4 => 'APR',
        5 => 'MAY',
        6 => 'JUN',
        7 => 'JUL',
        8 => 'AOT',
        9 => 'SEP',
        10 => 'OCT',
        11 => 'NOV',
        12 => 'DEC',
    ];

    public const DAYS = [
        'Mon' => 'MONDAY',
        'Tue' => 'TUESDAY',
        'Wed' => 'WEDNESDAY',
        'Thu' => 'THURSDAY',
        'Fri' => 'FRIDAY',
        'Sat' => 'SATURDAY',
        'Sun' => 'SUNDAY',
    ];

    /**
     * Get the first day of the week (Monday) for the given date
     *
     * @param DateTimeInterface $dateTime The reference date
     * @return DateTime|null First day of the week as DateTime or null on failure
     */
    public static function getFirstDayOfTheWeekFromDate(DateTimeInterface $dateTime): ?DateTime
    {
        try {
            $date = DateTime::createFromInterface($dateTime);
            $dayOfWeek = (int)$date->format('N') - 1; // 0 (Monday) to 6 (Sunday)
            $date->modify("-{$dayOfWeek} days")->setTime(0, 0, 0);
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the first day of the month for the given date
     *
     * @param DateTimeInterface $dateTime The reference date
     * @return DateTime|null First day of the month as DateTime or null on failure
     */
    public static function getFirstDayOfTheMonthFromDate(DateTimeInterface $dateTime): ?DateTime
    {
        try {
            $date = DateTime::createFromInterface($dateTime);
            $date->setDate(
                (int)$date->format('Y'),
                (int)$date->format('m'),
                1
            )->setTime(0, 0, 0);
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the first day of the year for the given date
     *
     * @param DateTimeInterface $dateTime The reference date
     * @return DateTime|null First day of the year as DateTime or null on failure
     */
    public static function getFirstDayOfTheYearFromDate(DateTimeInterface $dateTime): ?DateTime
    {
        try {
            $date = DateTime::createFromInterface($dateTime);
            $date->setDate(
                (int)$date->format('Y'),
                1,
                1
            )->setTime(0, 0, 0);
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the day of the week (0 for Sunday through 6 for Saturday)
     *
     * @param string|DateTimeInterface $date Date string or DateTimeInterface object
     * @return int Day of the week (0-6)
     */
    public static function getWeekday(string|DateTimeInterface $date): int
    {
        try {
            $dateTime = $date instanceof DateTimeInterface
                ? DateTime::createFromInterface($date)
                : new DateTime($date);
            return (int)$dateTime->format('w');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get month abbreviation from its position (1-12)
     *
     * @param int $position Month number (1-12)
     * @return string Three-letter month abbreviation or 'UNK' if invalid
     */
    public static function getMonthFromPosition(int $position): string
    {
        return self::MONTHS[$position] ?? 'UNK';
    }

    /**
     * Get the day of the year (0-365/366)
     *
     * @param DateTimeInterface|null $date Optional date (uses current date if null)
     * @return int Day of the year (0-365/366)
     */
    public static function getYearDay(?DateTimeInterface $date = null): int
    {
        try {
            $dateTime = $date ? DateTime::createFromInterface($date) : new DateTime();
            return (int)$dateTime->format('z');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get the current year as a string
     *
     * @param DateTimeInterface|null $date Optional date (uses current date if null)
     * @return string Current year as a 4-digit string
     */
    public static function getYear(?DateTimeInterface $date = null): string
    {
        try {
            $dateTime = $date ? DateTime::createFromInterface($date) : new DateTime();
            return $dateTime->format('Y');
        } catch (Exception $e) {
            return (string)date('Y');
        }
    }

    /**
     * Get the month day or month name from a date
     *
     * @param string|DateTimeInterface $date Date string or DateTimeInterface object
     * @param int $mode 0 to return month as int, any other value to return 3-letter month name
     * @return int|string Month as integer or 3-letter month name
     */
    public static function getMonthday(string|DateTimeInterface $date, int $mode = 0): int|string
    {
        try {
            $dateTime = $date instanceof DateTimeInterface
                ? DateTime::createFromInterface($date)
                : new DateTime($date);

            if ($mode === 0) {
                return (int)$dateTime->format('n'); // 1-12
            }

            return strtoupper($dateTime->format('M')); // 3-letter month name in uppercase
        } catch (Exception $e) {
            return $mode === 0 ? 1 : 'JAN';
        }
    }

    /**
     * Format a date as 'd/m/Y'
     *
     * @param DateTimeInterface $date The date to format
     * @return string Formatted date string
     */
    public static function formatDateDay(DateTimeInterface $date): string
    {
        try {
            $dateTime = DateTime::createFromInterface($date);
            return $dateTime->format('d/m/Y');
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get the day of the week in uppercase (e.g., 'THURSDAY')
     *
     * @param DateTimeInterface $date The date to check
     * @return string Uppercase day name
     */
    public static function getDayOfWeek(DateTimeInterface $date): string
    {
        try {
            $dateTime = DateTime::createFromInterface($date);
            return strtoupper($dateTime->format('l'));
        } catch (Exception $e) {
            return 'SUNDAY';
        }
    }

    /**
     * Get the weekday name in uppercase from a date (e.g., 'MONDAY')
     *
     * @param DateTimeInterface $date The date to check
     * @return string Uppercase day name
     */
    public static function getWeekDayOfDate(DateTimeInterface $date): string
    {
        try {
            $dateTime = DateTime::createFromInterface($date);
            return strtoupper($dateTime->format('l'));
        } catch (Exception $e) {
            return 'SUNDAY';
        }
    }
}
