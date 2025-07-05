<?php

namespace SamyAsm\Chrono\Traits;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Trait for calendar-related operations
 * 
 * This trait provides static methods for calendar operations.
 * All methods are designed to work without instance state.
 */
trait ChronoCalendarTrait
{
    /**
     * Gets the first day of the week for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return DateTime First day of the week (Monday)
     */
    public static function getFirstDayOfWeek(DateTimeInterface|string $date): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        $dayOfWeek = (int) $dateTime->format('N'); // 1 (Monday) to 7 (Sunday)
        $daysToSubtract = $dayOfWeek - 1; // Days to go back to Monday
        
        return $dateTime->modify("-$daysToSubtract days")->setTime(0, 0, 0);
    }

    /**
     * Gets the first day of the month for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return DateTime First day of the month
     */
    public static function getFirstDayOfMonth(DateTimeInterface|string $date): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        return new DateTime($dateTime->format('Y-m-01 00:00:00'), $dateTime->getTimezone());
    }

    /**
     * Gets the first day of the year for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return DateTime First day of the year
     */
    public static function getFirstDayOfYear(DateTimeInterface|string $date): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        return new DateTime($dateTime->format('Y-01-01 00:00:00'), $dateTime->getTimezone());
    }

    /**
     * Gets the weekday number (1-7) for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return int Weekday number (1=Monday to 7=Sunday)
     */
    public static function getWeekday(DateTimeInterface|string $date): int
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        return (int) $dateTime->format('N');
    }

    /**
     * Gets the day of the month (1-31) or month name for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @param int $mode 0 for day of month, 1 for month name
     * @return int|string Day of month or month name
     * @throws InvalidArgumentException If mode is invalid
     */
    public static function getMonthday(DateTimeInterface|string $date, int $mode = 0)
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        if ($mode === 0) {
            return (int) $dateTime->format('j');
        } elseif ($mode === 1) {
            return strtoupper($dateTime->format('M'));
        }
        
        throw new InvalidArgumentException('Invalid mode. Use 0 for day of month or 1 for month name.');
    }

    /**
     * Gets the day of the year (1-366) for a given date
     */
    public static function getDayOfYear(DateTimeInterface|string $date): int
    {
        $dateTime = $date instanceof DateTimeInterface
            ? $date
            : new DateTime($date);
            
        return (int) $dateTime->format('z') + 1;
    }

    /**
     * Gets the year for a given date
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return int The year
     */
    public static function getYear(DateTimeInterface|string $date): int
    {
        $dateTime = $date instanceof DateTimeInterface
            ? (clone $date)
            : new DateTime($date);
            
        return (int) $dateTime->format('Y');
    }

    /**
     * Gets the month name for a given date or month number
     * 
     * @param DateTimeInterface|string|int $date The date or month number (1-12)
     * @param bool $full Whether to return the full month name (not implemented)
     * @return string Month name in uppercase (e.g., 'JAN')
     * @throws InvalidArgumentException If the month number is out of range (1-12)
     */
    public static function getMonthName(DateTimeInterface|string|int $date, bool $full = false): string
    {
        $month = is_int($date) 
            ? $date 
            : (int) (($date instanceof DateTimeInterface ? $date : new DateTime($date))->format('n'));
            
        if ($month < 1 || $month > 12) {
            return 'UNK';
        }
            
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        return $months[$month - 1] ?? 'UNK';
    }

    // Formatting methods have been moved to ChronoFormatTrait
}
