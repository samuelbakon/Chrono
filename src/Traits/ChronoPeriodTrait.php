<?php

namespace SamBakon\Chrono\Traits;

use DatePeriod;
use DateTime;
use DateTimeInterface;
use DateInterval;
use InvalidArgumentException;

/**
 * Trait for handling date periods and intervals
 * 
 * This trait provides static methods for working with date ranges and periods.
 * All methods are designed to work without instance state.
 */
trait ChronoPeriodTrait
{
    /**
     * Gets today's interval (from 00:00:00 to 23:59:59)
     * 
     * @return array{start: DateTime, end: DateTime} Array with 'start' and 'end' DateTime objects
     */
    public static function getTodayInterval(): array
    {
        $today = new DateTime();
        $start = (clone $today)->setTime(0, 0, 0);
        $end = (clone $today)->setTime(23, 59, 59);
        
        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Adjusts a filter interval with start and end dates
     * 
     * @param DateTimeInterface|string|null $start Start date or null
     * @param DateTimeInterface|string|null $end End date or null
     * @param string $defaultInterval Default interval if both dates are null (default: 'P1M' for 1 month)
     * @return array{start: DateTime, end: DateTime} Adjusted date range with start and end dates
     */
    public static function adjustInterval(
        DateTimeInterface|string|null $start = null, 
        DateTimeInterface|string|null $end = null,
        string $defaultInterval = 'P1M'
    ): array {
        $now = new DateTime();
        
        // Convert input to DateTime if needed
        $convertToDateTime = function($date) {
            if ($date === null) return null;
            if ($date instanceof DateTime) return $date;
            if ($date instanceof DateTimeInterface) return DateTime::createFromInterface($date);
            return new DateTime($date);
        };
        
        // If both dates are null, use default interval
        if ($start === null && $end === null) {
            $end = $now;
            $start = (clone $end)->sub(new DateInterval($defaultInterval));
        }
        // If only end date is null, set it to start + default interval
        elseif ($end === null) {
            $start = $convertToDateTime($start);
            $end = (clone $start)->add(new DateInterval($defaultInterval));
        }
        // If only start date is null, set it relative to end date
        elseif ($start === null) {
            $end = $convertToDateTime($end);
            $start = (clone $end)->sub(new DateInterval($defaultInterval));
        }
        
        // Convert to DateTime if needed
        $start = $convertToDateTime($start);
        $end = $convertToDateTime($end);
        
        // Ensure start is before end
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        
        // Set time to start and end of day if times are the same (likely not specified)
        if ($start->format('H:i:s') === $end->format('H:i:s')) {
            $start->setTime(0, 0, 0);
            $end->setTime(23, 59, 59);
        }
        
        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Gets all dates between two dates as formatted strings
     * 
     * @param DateTimeInterface|string $start Start date
     * @param DateTimeInterface|string $end End date (inclusive)
     * @param string $format Output format (default: 'Y-m-d')
     * @return array<int, string> Array of formatted date strings
     */
    public static function getDateRange(
        DateTimeInterface|string $start, 
        DateTimeInterface|string $end, 
        string $format = 'Y-m-d'
    ): array {
        $start = $start instanceof DateTimeInterface 
            ? (clone $start) 
            : new DateTime($start);
            
        $end = $end instanceof DateTimeInterface 
            ? (clone $end)
            : new DateTime($end);
        
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        
        $interval = new DateInterval('P1D');
        $end->modify('+1 day'); // Include end date
        
        $period = new DatePeriod($start, $interval, $end);
        
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }
        
        return $dates;
    }

    /**
     * Gets all days between two dates as DateTime objects
     * 
     * @param DateTimeInterface|string $start Start date
     * @param DateTimeInterface|string $end End date (inclusive)
     * @return array<int, DateTime> Array of DateTime objects
     */
    public static function getDaysInPeriod(
        DateTimeInterface|string $start, 
        DateTimeInterface|string $end
    ): array {
        $start = $start instanceof DateTimeInterface 
            ? DateTime::createFromInterface($start)->setTime(0, 0, 0)
            : (new DateTime($start))->setTime(0, 0, 0);
            
        $end = $end instanceof DateTimeInterface 
            ? DateTime::createFromInterface($end)->setTime(0, 0, 0)
            : (new DateTime($end))->setTime(0, 0, 0);
        
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        
        $interval = new DateInterval('P1D');
        $end = (clone $end)->modify('+1 day'); // Include end date
        
        $period = new DatePeriod($start, $interval, $end);
        
        $days = [];
        foreach ($period as $date) {
            $days[] = $date;
        }
        
        return $days;
    }

    /**
     * Gets date range with time set to start and end of day
     * 
     * @param DateTimeInterface|string $date The date to get the range for
     * @return array{start: DateTime, end: DateTime} Array with 'start' and 'end' DateTime objects
     */
    public static function getDayRange(DateTimeInterface|string $date): array
    {
        $date = $date instanceof DateTimeInterface 
            ? (clone $date) 
            : new DateTime($date);
        
        $start = (clone $date)->setTime(0, 0, 0);
        $end = (clone $date)->setTime(23, 59, 59);
        
        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Gets the number of days between two dates
     * 
     * @param DateTimeInterface|string $start Start date
     * @param DateTimeInterface|string $end End date
     * @return int Number of days between the dates (positive if end is after start)
     */
    public static function getDaysBetween(
        DateTimeInterface|string $start, 
        DateTimeInterface|string $end
    ): int {
        $start = $start instanceof DateTimeInterface 
            ? DateTime::createFromInterface($start)->setTime(0, 0, 0)
            : (new DateTime($start))->setTime(0, 0, 0);
            
        $end = $end instanceof DateTimeInterface 
            ? DateTime::createFromInterface($end)->setTime(0, 0, 0)
            : (new DateTime($end))->setTime(0, 0, 0);
        
        $interval = $start->diff($end);
        return (int) $interval->format('%r%a');
    }

    /**
     * Checks if a date is within a range (inclusive)
     * 
     * @param DateTimeInterface|string $date The date to check
     * @param DateTimeInterface|string $start Start of the range
     * @param DateTimeInterface|string $end End of the range
     * @return bool True if the date is within the range, false otherwise
     */
    public static function isDateInRange(
        DateTimeInterface|string $date,
        DateTimeInterface|string $start,
        DateTimeInterface|string $end
    ): bool {
        $date = $date instanceof DateTimeInterface 
            ? (clone $date)
            : new DateTime($date);
            
        $start = $start instanceof DateTimeInterface 
            ? DateTime::createFromInterface($start)->setTime(0, 0, 0)
            : (new DateTime($start))->setTime(0, 0, 0);
            
        $end = $end instanceof DateTimeInterface 
            ? DateTime::createFromInterface($end)->setTime(23, 59, 59)
            : (new DateTime($end))->setTime(23, 59, 59);
        
        return $date >= $start && $date <= $end;
    }
}
