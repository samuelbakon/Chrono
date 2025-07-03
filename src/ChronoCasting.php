<?php

namespace SamyAsm\Chrono;

use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

/**
 * Handles date type conversion, parsing, and formatting
 */
class ChronoCasting
{
    /**
     * Convert a Unix timestamp to a DateTime object
     *
     * @param int $timestamp Unix timestamp to convert
     * @return DateTime|null DateTime object or null on failure
     */
    public static function timeToDate(int $timestamp): ?DateTime
    {
        if ($timestamp < 0) {
            return null;
        }

        try {
            $dateTime = new DateTime("@$timestamp");
            $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            return $dateTime;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create a DateTime object from a string or return null if invalid
     *
     * @param string|DateTimeInterface|null $date Date string or DateTimeInterface object
     * @return DateTime|null A new DateTime instance or null if input was null/invalid
     * @throws Exception If the date string is invalid and not null
     */
    public static function getDate($date = 'now'): ?DateTime
    {
        if ($date === null) {
            return new DateTime();
        }

        try {
            return $date instanceof DateTimeInterface
                ? DateTime::createFromInterface($date)
                : new DateTime($date);
        } catch (Exception $e) {
            throw new Exception(sprintf('Invalid date: %s', $date));
        }
    }

    /**
     * Get date as a formatted string
     *
     * @param string|DateTimeInterface $date Date string or DateTimeInterface object
     * @param string $format Output format (default: 'd-m-Y')
     * @return string Formatted date string
     * @throws Exception If date parsing fails
     */
    public static function getDateAsString(
        string|DateTimeInterface $date = 'now',
        string $format = 'd-m-Y'
    ): string {
        try {
            $dateTime = $date instanceof DateTimeInterface
                ? DateTime::createFromInterface($date)
                : new DateTime($date);

            return $dateTime->format($format);
        } catch (Exception $e) {
            throw new Exception(sprintf('Failed to format date: %s', $e->getMessage()));
        }
    }

    /**
     * Check if a date string is valid
     *
     * @param string $date Date string to validate
     * @param string $format Expected format (default: 'd-m-Y')
     * @return bool True if the date is valid and matches the format
     */
    public static function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        // Check if the date was created and matches the input format
        if ($d === false) {
            return false;
        }

        // Check if the formatted date matches the input
        return $d->format($format) === $date;
    }

    /**
     * Parse a short day name to its full name
     *
     * @param string $dayShortEn Short day name (e.g., 'Mon', 'Tue')
     * @return string Full day name in English or original string if not found
     */
    public static function parseDay(string $dayShortEn): string
    {
        $days = [
            'Mon' => 'Monday',
            'Tue' => 'Tuesday',
            'Wed' => 'Wednesday',
            'Thu' => 'Thursday',
            'Fri' => 'Friday',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday',
        ];

        return $days[ucfirst(strtolower($dayShortEn))] ?? $dayShortEn;
    }

    /**
     * Adjust a date to a specific time
     *
     * @param DateTimeInterface $dateTime Base date
     * @param string $time Time string (format: 'H:i' or 'H:i:s')
     * @return DateTime New DateTime with adjusted time
     * @throws Exception If time format is invalid
     */
    public static function accordDateToTime(DateTimeInterface $dateTime, string $time): DateTime
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            throw new InvalidArgumentException('Invalid time format. Expected "H:i" or "H:i:s"');
        }

        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $seconds = isset($matches[3]) ? (int)$matches[3] : 0;

        // Validate time components
        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59 || $seconds < 0 || $seconds > 59) {
            throw new InvalidArgumentException('Invalid time values');
        }

        $newDate = DateTime::createFromInterface($dateTime);
        $newDate->setTime($hours, $minutes, $seconds);

        return $newDate;
    }

    /**
     * Check if a time string is in a regular format (HH:MM or HH:MM:SS)
     *
     * @param string $time Time string to validate
     * @return bool True if the time format is valid
     */
    public static function isRegularTime(string $time): bool
    {
        return (bool)preg_match('/^([01]?\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $time);
    }

    /**
     * Convert a DateTimeInterface to a DateTime object
     *
     * @param DateTimeInterface $dateTime The DateTimeInterface to convert
     * @return DateTime A new DateTime instance
     */
    public static function dateFromInterface(DateTimeInterface $dateTime): DateTime
    {
        return DateTime::createFromInterface($dateTime);
    }

    /**
     * Convert a DateTimeInterface to a DateTime object (alias for dateFromInterface)
     *
     * @param DateTimeInterface|null $dateTime The DateTimeInterface to convert, or null
     * @return DateTime|null A new DateTime instance or null if input was null
     */
    public static function interfaceToDateTime(?DateTimeInterface $dateTime = null): ?DateTime
    {
        return $dateTime ? DateTime::createFromInterface($dateTime) : null;
    }
}
