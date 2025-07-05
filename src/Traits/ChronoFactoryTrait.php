<?php

namespace SamyAsm\Chrono\Traits;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Trait for Chrono factory methods
 * 
 * This trait provides factory methods for creating DateTime objects and instances of the class using the trait.
 */
trait ChronoFactoryTrait
{
    /**
     * Creates a DateTime object for the current date and time
     * 
     * @return DateTime Current date and time
     */
    public static function createNow(): DateTime
    {
        return new DateTime();
    }
    
    /**
     * Creates a new instance for the current date and time
     * 
     * @return DateTime
     */
    public static function now(): DateTimeInterface
    {
        return new DateTime();
    }

    /**
     * Creates a DateTime object from various input types (internal use)
     * 
     * @param DateTimeInterface|string $date The date to create from
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime The created DateTime object
     * @internal This method is for internal use only
     */
    protected static function createDateTimeInternal(DateTimeInterface|string $date, DateTimeZone|string|null $timezone = null): DateTime
    {
        if ($date instanceof DateTime) {
            $dt = clone $date;
        } elseif ($date instanceof DateTimeInterface) {
            $dt = DateTime::createFromInterface($date);
        } else {
            $dt = new DateTime($date);
        }
        
        if ($timezone !== null) {
            $timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
            $dt->setTimezone($timezone);
        }
        
        return $dt;
    }
    
    /**
     * Creates a new instance for a specific date with optional timezone
     * 
     * @param DateTimeInterface|string $date The date to create from
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime
     */
    public static function create(DateTimeInterface|string $date, DateTimeZone|string|null $timezone = null): DateTimeInterface
    {
        return self::createDateTimeInternal($date, $timezone);
    }
    
    /**
     * Creates a DateTime object from various input types
     * 
     * @param DateTimeInterface|string $date The date to create from
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime The created DateTime object
     */
    public static function createDateTime(DateTimeInterface|string $date, DateTimeZone|string|null $timezone = null): DateTime
    {
        if ($date instanceof DateTime) {
            $dt = clone $date;
        } elseif ($date instanceof DateTimeInterface) {
            $dt = DateTime::createFromInterface($date);
        } else {
            $dt = new DateTime($date);
        }
        
        if ($timezone !== null) {
            $timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
            $dt->setTimezone($timezone);
        }
        
        return $dt;
    }

    /**
     * Creates a DateTime object from a timestamp
     * 
     * @param int $timestamp Unix timestamp
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime The created DateTime object
     */
    public static function createDateTimeFromTimestamp(int $timestamp, DateTimeZone|string|null $timezone = null): DateTime
    {
        $date = new DateTime('@' . $timestamp);
        if ($timezone !== null) {
            $timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
            $date->setTimezone($timezone);
        }
        return $date;
    }
    
    /**
     * Creates a new instance from a timestamp with timezone support
     * 
     * @param int $timestamp Unix timestamp
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime
     */
    public static function createFromTimestampWithTz(int $timestamp, DateTimeZone|string|null $timezone = null): DateTimeInterface
    {
        return self::createDateTimeFromTimestamp($timestamp, $timezone);
    }
    
    /**
     * Creates a new instance from a timestamp (compatible with DateTime::createFromTimestamp)
     * 
     * @param float|int $timestamp Unix timestamp with microseconds
     * @return DateTime
     */
    public static function createFromTimestamp(float|int $timestamp): DateTimeInterface
    {
        $date = new DateTime();
        $date->setTimestamp((int)$timestamp);
        return $date;
    }

    /**
     * Creates a new instance from a format
     * 
     * @param string $format Format accepted by date_create_from_format()
     * @param string $time String representing the time
     * @param DateTimeZone|string|null $timezone Optional timezone
     * @return DateTime
     */
    public static function createFromFormat(string $format, string $time, DateTimeZone|string|null $timezone = null): DateTimeInterface
    {
        if ($timezone !== null) {
            $timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
            $date = DateTime::createFromFormat($format, $time, $timezone);
        } else {
            $date = DateTime::createFromFormat($format, $time);
        }

        if ($date === false) {
            throw new InvalidArgumentException('Invalid date/time string or format');
        }

        return new DateTime($date);
    }
}
