<?php

namespace SamyAsm\Chrono;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Handles date calculations and comparisons
 */
class ChronoComputer
{
    /**
     * Get the difference in days between two dates
     *
     * @param DateTimeInterface $dateTime1 First date
     * @param DateTimeInterface $dateTime2 Second date
     * @param bool $normalize If true, normalize dates to start of day
     * @return int Difference in days (negative if $dateTime1 is before $dateTime2)
     */
    public static function getDateDayDif(
        DateTimeInterface $dateTime1,
        DateTimeInterface $dateTime2,
        bool $normalize = false
    ): int {
        if ($normalize) {
            $date1 = DateTime::createFromInterface($dateTime1)->setTime(0, 0);
            $date2 = DateTime::createFromInterface($dateTime2)->setTime(0, 0);
        } else {
            $date1 = $dateTime1;
            $date2 = $dateTime2;
        }

        $diff = $date1->diff($date2);
        return (int) ($diff->days * ($diff->invert ? -1 : 1));
    }

    /**
     * Get the difference in minutes between two dates
     *
     * @param DateTimeInterface $dateTime1 First date
     * @param DateTimeInterface $dateTime2 Second date
     * @param bool $absolute If true, return absolute difference
     * @return float Difference in minutes (negative if $dateTime1 is before $dateTime2 and $absolute is false)
     */
    public static function getMinuteDateDif(
        DateTimeInterface $dateTime1,
        DateTimeInterface $dateTime2,
        bool $absolute = false
    ): float {
        $secondsDiff = self::getSecondsDateDif($dateTime1, $dateTime2);
        $minutes = $secondsDiff / 60;
        return $absolute ? abs($minutes) : $minutes;
    }

    /**
     * Get the difference in seconds between two dates
     *
     * @param DateTimeInterface $dateTime1 First date
     * @param DateTimeInterface $dateTime2 Second date
     * @return int Difference in seconds (negative if $dateTime1 is before $dateTime2)
     */
    public static function getSecondsDateDif(
        DateTimeInterface $dateTime1,
        DateTimeInterface $dateTime2
    ): int {
        return $dateTime1->getTimestamp() - $dateTime2->getTimestamp();
    }

    /**
     * Add hours to a date
     *
     * @param DateTimeInterface $dateTime The base date
     * @param int $hours Number of hours to add
     * @return DateTimeInterface|null The new date or null on failure
     */
    public static function addHoursToDate(DateTimeInterface $dateTime, int $hours = 1): ?DateTimeInterface
    {
        try {
            $newDate = DateTime::createFromInterface($dateTime);
            $newDate->modify(sprintf('+%d hours', $hours));
            return $newDate;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Add days to a date
     *
     * @param DateTimeInterface $dateTime The base date
     * @param int $days Number of days to add
     * @return DateTimeInterface|null The new date or null on failure
     */
    public static function addDaysToDate(DateTimeInterface $dateTime, int $days = 1): ?DateTimeInterface
    {
        try {
            $newDate = DateTime::createFromInterface($dateTime);
            $newDate->modify(sprintf('+%d days', $days));
            return $newDate;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Add minutes to a date
     *
     * @param DateTimeInterface $dateTime The base date
     * @param int $minutes Number of minutes to add
     * @return DateTimeInterface|null The new date or null on failure
     */
    public static function addMinutesToDate(DateTimeInterface $dateTime, int $minutes = 15): ?DateTimeInterface
    {
        try {
            $newDate = DateTime::createFromInterface($dateTime);
            $newDate->modify(sprintf('+%d minutes', $minutes));
            return $newDate;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Add seconds to a date
     *
     * @param DateTimeInterface|null $dateTime The base date (or null for current time)
     * @param int|null $seconds Number of seconds to add (or null for 0)
     * @return DateTimeInterface|null The new date or null on failure
     */
    public static function addSecondsToDate(
        ?DateTimeInterface $dateTime = null,
        ?int $seconds = null
    ): ?DateTimeInterface {
        try {
            $newDate = $dateTime ? DateTime::createFromInterface($dateTime) : new DateTime();
            $newDate->modify(sprintf('+%d seconds', $seconds ?? 0));
            return $newDate;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the number of days remaining between the given date and now
     *
     * @param DateTimeInterface $dateTime The date to compare with current time
     * @return int Number of days remaining (1 for yesterday, 0 for today, -1 for tomorrow)
     * @throws Exception If date operations fail
     */
    public static function getRemainingDay(DateTimeInterface $dateTime): int
    {
        $now = new DateTime();
        $target = DateTime::createFromInterface($dateTime);

        // Set both dates to midnight for accurate day difference
        $now->setTime(0, 0, 0);
        $target->setTime(0, 0, 0);

        // Calculate the difference in days
        $diff = $now->diff($target);
        $days = (int)$diff->format('%r%a');

        // Return 1 for yesterday, 0 for today, -1 for tomorrow
        if ($days > 0) {
            return -1; // Future date (tomorrow)
        } elseif ($days < 0) {
            return 1; // Past date (yesterday)
        }
        return 0; // Today
    }

    /**
     * Get a human-readable string representing the time elapsed since a given date
     *
     * @param string|DateTimeInterface $date The date to compare with current time
     * @return string Human-readable time difference (e.g., '5 Minutes', '2 Hours', '3 Days')
     * @throws Exception If date creation fails
     */
    public static function lastSeenHelp(string|DateTimeInterface $date): string
    {
        $dateTime = $date instanceof DateTimeInterface
            ? DateTime::createFromInterface($date)
            : new DateTime($date);

        $now = new DateTime();
        $diff = $now->diff($dateTime);

        if ($diff->y > 0) {
            return $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years');
        }

        if ($diff->m > 0) {
            return $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
        }

        if ($diff->d > 0) {
            return $diff->d . ' ' . ($diff->d === 1 ? 'Day' : 'Days');
        }

        if ($diff->h > 0) {
            return $diff->h . ' ' . ($diff->h === 1 ? 'Hour' : 'Hours');
        }

        if ($diff->i > 0) {
            return $diff->i . ' ' . ($diff->i === 1 ? 'Minute' : 'Minutes');
        }

        return 'Just now';
    }

    /**
     * Convert days to minutes
     *
     * @param int $days Number of days (default: 1)
     * @return int Number of minutes
     */
    public static function convertDaysToMinutes(int $days = 1): int
    {
        return $days * 24 * 60;
    }

    /**
     * Convert hours to minutes
     *
     * @param int $hours Number of hours (default: 1)
     * @return int Number of minutes
     */
    public static function convertHoursToMinutes(int $hours = 1): int
    {
        return $hours * 60;
    }

    /**
     * Convert minutes to hours
     *
     * @param int $minutes Number of minutes (default: 1)
     * @return float Hours (with decimal places)
     */
    public static function convertMinutesToHours(int $minutes = 1): float
    {
        return $minutes / 60.0;
    }

    /**
     * Add a time to a date
     *
     * @param DateTimeInterface $dateTime Base date
     * @param DateTimeInterface $time Time to add (only time part is used)
     * @return DateTime New date with time added
     * @throws Exception If date operations fail
     */
    public static function addTimeToDate(
        DateTimeInterface $dateTime,
        DateTimeInterface $time
    ): DateTime {
        $newDate = DateTime::createFromInterface($dateTime);
        $hours = (int)$time->format('H');
        $minutes = (int)$time->format('i');
        $seconds = (int)$time->format('s');

        // Add the time components individually
        if ($hours > 0) {
            $newDate->modify("+{$hours} hours");
        }
        if ($minutes > 0) {
            $newDate->modify("+{$minutes} minutes");
        }
        if ($seconds > 0) {
            $newDate->modify("+{$seconds} seconds");
        }

        return $newDate;
    }

    /**
     * Subtract minutes from a date
     *
     * @param DateTimeInterface $date Base date
     * @param int $minutes Number of minutes to subtract (default: 1)
     * @return DateTime New date with minutes subtracted
     */
    public static function subtractMinutesToDate(
        DateTimeInterface $date,
        int $minutes = 1
    ): DateTime {
        $newDate = DateTime::createFromInterface($date);
        $interval = new DateInterval("PT{$minutes}M");
        $newDate->sub($interval);
        return $newDate;
    }

    /**
     * Subtract hours from a date
     *
     * @param DateTimeInterface $date Base date
     * @param int $hours Number of hours to subtract (default: 1)
     * @return DateTime New date with hours subtracted
     */
    public static function subtractHoursToDate(
        DateTimeInterface $date,
        int $hours = 1
    ): DateTime {
        $newDate = DateTime::createFromInterface($date);
        $interval = new DateInterval("PT{$hours}H");
        $newDate->sub($interval);
        return $newDate;
    }

    /**
     * Subtract days from a date
     *
     * @param DateTimeInterface $date Base date
     * @param int $days Number of days to subtract (default: 1)
     * @return DateTime New date with days subtracted
     */
    public static function subtractDaysToDate(
        DateTimeInterface $date,
        int $days = 1
    ): DateTime {
        $newDate = DateTime::createFromInterface($date);
        $interval = new DateInterval("P{$days}D");
        $newDate->sub($interval);
        return $newDate;
    }
}
