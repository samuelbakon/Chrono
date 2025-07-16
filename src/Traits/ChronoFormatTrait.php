<?php

namespace SamBakon\Chrono\Traits;

use DateTime;
use DateTimeInterface;

/**
 * Trait for Chrono formatting methods
 */
trait ChronoFormatTrait
{
    /**
     * Format the date according to the given format (static version)
     * 
     * @param DateTimeInterface|string $date The date to format
     * @param string $format The format string (default: 'Y-m-d H:i:s')
     * @return string Formatted date string
     * @throws \InvalidArgumentException If the date is invalid
     */
    /**
     * Format the date according to the given format (static version)
     * 
     * @param DateTimeInterface|string $date The date to format or format string if $format is a date
     * @param string $format The format string or date string if $date is a format
     * @return string Formatted date string
     * @throws \InvalidArgumentException If the date is invalid
     */
    public static function formatStatic(DateTimeInterface|string $date, string $format = 'Y-m-d H:i:s'): string
    {
        // If both are strings, check if they might be reversed
        if (is_string($date) && is_string($format)) {
            // If $date looks like a format (starts with a format character)
            // and $format looks like a date (contains numbers and separators)
            if (preg_match('/^[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]/', $date) && 
                preg_match('/[0-9]/', $format)) {
                // The arguments are likely reversed, swap them
                [$format, $date] = [$date, $format];
            }
        }

        try {
            // If we have a DateTimeInterface, use it directly
            if ($date instanceof DateTimeInterface) {
                return $date->format($format);
            }
            
            // Try to create a DateTime from the string
            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date) ?: new DateTime($date);
            return $dateTime->format($format);
            
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid date format provided: " . (string)$date);
        }
    }
    
    /**
     * Format the date using a short format (alias for format with default format)
     * 
     * @param DateTimeInterface|string $date The date to format
     * @param string $format The format string (default: 'd/m/Y')
     * @return string Formatted date string
     */
    public static function formatDate(DateTimeInterface|string $date, string $format = 'd/m/Y'): string
    {
        return self::formatStatic($date, $format);
    }
    
    /**
     * Returns the date formatted as a string (Y-m-d H:i:s)
     * 
     * @param DateTimeInterface|string $date The date to format
     * @return string Formatted date string
     */
    public static function toString(DateTimeInterface|string $date): string
    {
        return self::formatStatic($date, 'Y-m-d H:i:s');
    }
}
