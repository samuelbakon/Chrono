<?php

namespace SamBakon\Chrono\Traits;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Trait for type casting and conversion
 * 
 * This trait provides static methods for type casting and conversion.
 * All methods are designed to work without instance state.
 */
trait ChronoCastingTrait
{
    /**
     * Converts a timestamp to a DateTime object
     * 
     * @param int $timestamp Unix timestamp
     * @return DateTime|null DateTime object or null if timestamp is invalid
     */
    public static function timestampToDateTime(int $timestamp): ?DateTime
    {
        if ($timestamp < 0) {
            return null;
        }
        
        try {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            return $date;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Gets a DateTime object from various input types
     * 
     * @param mixed $input Input to convert to DateTime (DateTime, DateTimeImmutable, timestamp, or date string)
     * @return DateTime The converted DateTime object
     * @throws InvalidArgumentException If the input cannot be converted to a valid date
     */
    public static function toDateTime(mixed $input): DateTime
    {
        if ($input instanceof DateTime) {
            return clone $input;
        }
        
        if ($input instanceof DateTimeImmutable) {
            return DateTime::createFromImmutable($input);
        }
        
        if (is_numeric($input)) {
            $date = new DateTime();
            $date->setTimestamp((int) $input);
            return $date;
        }
        
        if (is_string($input)) {
            return new DateTime($input);
        }
        
        throw new InvalidArgumentException('Invalid date format');
    }

    /**
     * Formats a DateTime object as a string
     * 
     * @param DateTimeInterface $date The date to format
     * @param string $format The format string (default: 'd-m-Y')
     * @return string Formatted date string
     */
    public static function formatDateTime(DateTimeInterface $date, string $format = 'd-m-Y'): string
    {
        return $date->format($format);
    }

    /**
     * Gets a date at the start of the day (00:00:00)
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return DateTime New DateTime object set to the start of the day
     */
    public static function getStartOfDay(DateTimeInterface|string $date): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface 
            ? DateTime::createFromInterface($date)
            : new DateTime($date);
            
        return $dateTime->setTime(0, 0, 0);
    }

    /**
     * Gets a date at the end of the day (23:59:59)
     * 
     * @param DateTimeInterface|string $date The date to process
     * @return DateTime New DateTime object set to the end of the day
     */
    public static function getEndOfDay(DateTimeInterface|string $date): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface 
            ? DateTime::createFromInterface($date)
            : new DateTime($date);
            
        return $dateTime->setTime(23, 59, 59);
    }

    /**
     * Creates a DateTime object from a DateTimeInterface
     * 
     * @param DateTimeInterface $date The date to convert
     * @return DateTime A new DateTime instance
     * @deprecated Use DateTime::createFromInterface() instead
     */
    public static function fromDateTimeInterface(DateTimeInterface $date): DateTime
    {
        if ($date instanceof DateTime) {
            return clone $date;
        }
        
        return DateTime::createFromInterface($date);
    }

    /**
     * Converts a DateTimeInterface to a DateTime object
     * 
     * @param DateTimeInterface|null $date The date to convert (or null)
     * @return DateTime|null A new DateTime instance or null if input was null
     * @deprecated Use DateTime::createFromInterface() directly instead
     */
    public static function toDateTimeFromInterface(?DateTimeInterface $date): ?DateTime
    {
        if ($date === null) {
            return null;
        }
        
        if ($date instanceof DateTime) {
            return clone $date;
        }
        
        return DateTime::createFromInterface($date);
    }

    /**
     * Checks if a date string is valid according to the specified format
     * 
     * @param string $date The date string to validate
     * @param string $format The expected date format (default: 'Y-m-d')
     * @return bool True if the date is valid and matches the format
     */
    public static function isValidDateString(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Parses a day string (e.g., 'monday', 'tuesday') into a day number (1-7)
     * 
     * @param string $day Day name (e.g., 'monday') or day number as string
     * @return int Day number (1-7, where 1 is Monday and 7 is Sunday)
     * @throws InvalidArgumentException If the day string is invalid
     */
    public static function parseDay(string $day): int
    {
        $day = strtolower(trim($day));
        $days = [
            'monday' => 1, 'mon' => 1, 'tuesday' => 2, 'tue' => 2,
            'wednesday' => 3, 'wed' => 3, 'thursday' => 4, 'thu' => 4,
            'friday' => 5, 'fri' => 5, 'saturday' => 6, 'sat' => 6,
            'sunday' => 7, 'sun' => 7
        ];
        
        if (isset($days[$day])) {
            return $days[$day];
        }
        
        $dayNum = (int) $day;
        if ($dayNum >= 1 && $dayNum <= 7) {
            return $dayNum;
        }
        
        throw new InvalidArgumentException("Invalid day: {$day}");
    }

    /**
     * Sets the time of a date from a time string (e.g., '14:30' or '14:30:45')
     * 
     * @param DateTimeInterface|string $date The base date
     * @param string $time Time string in HH:MM or HH:MM:SS format
     * @return DateTime New DateTime object with the time set
     * @throws InvalidArgumentException If the time format is invalid
     */
    public static function setTimeFromString(DateTimeInterface|string $date, string $time): DateTime
    {
        $dateTime = $date instanceof DateTimeInterface 
            ? DateTime::createFromInterface($date)
            : new DateTime($date);
            
        if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            throw new InvalidArgumentException('Invalid time format. Expected HH:MM or HH:MM:SS');
        }
        
        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];
        $seconds = $matches[3] ?? 0;
        
        if ($hours < 0 || $hours > 23 || $minutes < 0 || $minutes > 59 || $seconds < 0 || $seconds > 59) {
            throw new InvalidArgumentException('Invalid time values. Hours: 0-23, Minutes: 0-59, Seconds: 0-59');
        }
        
        return $dateTime->setTime($hours, $minutes, (int) $seconds);
    }

    /**
     * Checks if a time string is in a valid 24-hour format (HH:MM or HH:MM:SS)
     * 
     * @param string $time Time string to validate
     * @return bool True if the time string is in a valid 24-hour format
     */
    public static function isValidTimeString(string $time): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $time);
    }
}
