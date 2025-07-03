<?php

namespace SamyAsm\Chrono;

use DatePeriod;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

class DateUtil
{
    public const PERIODS = [
        'DAY' => 'DAY',
        'YESTERDAY' => 'YESTERDAY',
        'MONTH' => 'MONTH',
        'TOMORROW' => 'TOMORROW',
        'WEEK' => 'WEEK',
        'YEAR' => 'YEAR',
    ];

    public const DAYS = [
        'MON' => 'MONDAY',
        'TUE' => 'TUESDAY',
        'WED' => 'WEDNESDAY',
        'THU' => 'THURSDAY',
        'FRI' => 'FRIDAY',
        'SAT' => 'SATURDAY',
        'SUN' => 'SUNDAY',
    ];

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

    public const ONE_YEAR = 365;
    public const ONE_DAY = 1;
    public const ONE_DAY_IN_MINUTE = 1440;
    public const ONE_HOUR_IN_MINUTE = 60;
    public const ONE_MONTH_IN_MINUTE = 43200;
    public const MIDNIGHT_TIME = '00:00';

    /**
     * Convert a Unix timestamp to a DateTime object
     *
     * @param int $timestamp Unix timestamp to convert
     * @return DateTime|null DateTime object or null on failure
     */
    public static function timeToDate(int $timestamp): ?DateTime
    {
        try {
            $dateTime = new DateTime("@$timestamp");
            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
            return $dateTime;
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
            // Create a new DateTime instance from the interface to ensure we have a mutable object
            $date = DateTime::createFromInterface($dateTime);
            
            // Set the day to the first day of the month and reset time to midnight
            $date->setDate((int)$date->format('Y'), (int)$date->format('m'), 1);
            $date->setTime(0, 0, 0);
            
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the first day of the week (Monday) for the given date
     *
     * @param DateTimeInterface $dateTime The reference date
     * @return DateTime|null First day of the week as DateTime or null on failure
     */
    public static function getFirstDayOfTheWeekFromDate(DateTimeInterface $dateTime): ?DateTime
    {
        try {
            // Create a new DateTime instance from the interface to ensure we have a mutable object
            $date = DateTime::createFromInterface($dateTime);
            
            // Get the day of the week (0=Sunday, 6=Saturday)
            $dayOfWeek = (int)$date->format('w');
            
            // Calculate days to subtract to get to Monday (1=Monday, 0=Sunday in our calculation)
            $daysToSubtract = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;
            
            if ($daysToSubtract > 0) {
                $date->sub(new \DateInterval('P' . $daysToSubtract . 'D'));
            }
            
            // Reset time to start of day
            $date->setTime(0, 0, 0);
            
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
            // Create a new DateTime instance from the interface to ensure we have a mutable object
            $date = DateTime::createFromInterface($dateTime);
            
            // Set to January 1st of the same year and reset time to midnight
            $date->setDate((int)$date->format('Y'), 1, 1);
            $date->setTime(0, 0, 0);
            
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
        if ($date instanceof DateTimeInterface) {
            return (int) $date->format('w');
        }
        return (int) date('w', strtotime($date));
    }

    /**
     * Get the month day or month name from a date
     *
     * @param string|DateTimeInterface $date Date string or DateTimeInterface object
     * @param int $mode 0 to return month as int, any other value to return month name
     * @return int|string Month as integer or month name
     */
    public static function getMonthday(string|DateTimeInterface $date, int $mode = 0): int|string
    {
        if ($date instanceof DateTimeInterface) {
            $month = (int) $date->format('m');
        } else {
            $dateObj = new DateTime($date);
            $month = (int) $dateObj->format('m');
        }

        return $mode === 0 ? $month : self::getMonthFromPosition($month);
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
     * @return int Day of the year
     */
    public static function getYearDay(): int
    {
        try {
            $currentYear = (new DateTime())->format('Y');
            $startOfYear = new DateTime($currentYear . '-01-01');
            $now = new DateTime();
            $diff = $startOfYear->diff($now);
            
            return (int) $diff->days;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get the current year as a string
     *
     * @return string Current year as a 4-digit string
     */
    public static function getYear(): string
    {
        try {
            return (new DateTime())->format('Y');
        } catch (Exception $e) {
            return '1759';
        }
    }

    /**
     * Convert a DateTimeInterface to a DateTime object
     *
     * @param DateTimeInterface|null $dateTime The DateTimeInterface to convert, or null
     * @return DateTime|null A new DateTime instance or null if input was null
     * @throws Exception If the date/time string is invalid
     */
    public static function interfaceToDateTime(?DateTimeInterface $dateTime = null): ?DateTime
    {
        if ($dateTime === null) {
            return null;
        }

        if ($dateTime instanceof DateTime) {
            return clone $dateTime;
        }

        return new DateTime('@' . $dateTime->getTimestamp());
    }

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
    public static function getSecondsDateDif(DateTimeInterface $dateTime1, DateTimeInterface $dateTime2): int
    {
        $timestamp1 = $dateTime1->getTimestamp();
        $timestamp2 = $dateTime2->getTimestamp();
        return $timestamp1 - $timestamp2;
    }

    /**
     * Get the number of days remaining between the given date and now
     *
     * @param DateTimeInterface $dateTime The date to compare with current time
     * @return int Number of days remaining (can be negative if the date is in the past)
     * @throws Exception If date operations fail
     */
    public static function getRemainingDay(DateTimeInterface $dateTime): int
    {
        return (int)self::getDateDayDif($dateTime, new DateTime());
    }

    public static function getIntervalOfToday(): array
    {
        return self::getInterval('DAY');
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
        $now = new DateTime();
        $targetDate = $date instanceof DateTimeInterface 
            ? DateTime::createFromInterface($date) 
            : new DateTime($date);
            
        $interval = $now->diff($targetDate);
        
        // Get all interval components
        $components = [
            'year' => (int)$interval->format('%y'),
            'month' => (int)$interval->format('%m'),
            'day' => (int)$interval->format('%d'),
            'hour' => (int)$interval->format('%h'),
            'minute' => (int)$interval->format('%i'),
            'second' => (int)$interval->format('%s')
        ];
        
        // Determine the most significant time unit
        if ($components['year'] > 0) {
            return $components['year'] . ' Year' . ($components['year'] > 1 ? 's' : '');
        } elseif ($components['month'] > 0) {
            return $components['month'] . ' Month' . ($components['month'] > 1 ? 's' : '');
        } elseif ($components['day'] > 0) {
            return $components['day'] . ' Day' . ($components['day'] > 1 ? 's' : '');
        } elseif ($components['hour'] > 0) {
            return $components['hour'] . ' Hour' . ($components['hour'] > 1 ? 's' : '');
        } elseif ($components['minute'] > 0) {
            return $components['minute'] . ' Minute' . ($components['minute'] > 1 ? 's' : '');
        } else {
            return $components['second'] . ' Second' . ($components['second'] !== 1 ? 's' : '');
        }
    }

    /**
     * Get a date range based on a period type
     *
     * @param string $period The period type (e.g., 'DAY', 'YESTERDAY', 'MONTH', 'WEEK', 'YEAR')
     * @return array{start: DateTimeInterface, end: DateTimeInterface} Array containing start and end dates
     * @throws InvalidArgumentException If period is not recognized
     * @throws Exception If date operations fail
     */
    public static function getInterval(string $period): array
    {
        $today = new DateTime('today');
        $startDate = null;
        $endDate = null;

        switch (strtoupper($period)) {
            case 'DAY':
                $startDate = clone $today;
                $endDate = (clone $startDate)->add(new \DateInterval('P1D'));
                break;

            case 'YESTERDAY':
                $endDate = clone $today;
                $startDate = (clone $today)->sub(new \DateInterval('P1D'));
                break;

            case 'MONTH':
                $startDate = self::getFirstDayOfTheMonthFromDate($today);
                if ($startDate === null) {
                    throw new Exception('Could not determine first day of month');
                }
                $daysInMonth = (int)$today->format('t');
                $daysRemaining = $daysInMonth - (int)$today->format('j');
                $endDate = (clone $today)->add(new \DateInterval("P{$daysRemaining}D"));
                break;

            case 'TOMORROW':
                $startDate = (clone $today)->add(new \DateInterval('P1D'));
                $endDate = (clone $startDate)->add(new \DateInterval('P1D'));
                break;

            case 'WEEK':
                $startDate = self::getFirstDayOfTheWeekFromDate($today);
                if ($startDate === null) {
                    throw new Exception('Could not determine first day of week');
                }
                $daysRemaining = 6 - (int)$today->format('w'); // 0 (Sunday) to 6 (Saturday)
                $endDate = (clone $today)->add(new \DateInterval("P{$daysRemaining}D"));
                break;

            case 'YEAR':
                $startDate = self::getFirstDayOfTheYearFromDate($today);
                if ($startDate === null) {
                    throw new Exception('Could not determine first day of year');
                }
                $dayOfYear = (int)$today->format('z');
                $isLeapYear = (bool)$today->format('L');
                $daysInYear = $isLeapYear ? 366 : 365;
                $daysRemaining = $daysInYear - $dayOfYear - 1;
                $endDate = (clone $today)->add(new \DateInterval("P{$daysRemaining}D"));
                break;

            default:
                throw new InvalidArgumentException(sprintf('Unsupported period type: %s', $period));
        }

        return [
            'start' => $startDate,
            'end' => $endDate
        ];
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
            return null;
        }
        
        if ($date instanceof DateTimeInterface) {
            return self::interfaceToDateTime($date);
        }
        
        try {
            return new DateTime($date);
        } catch (\Throwable $t) {
            throw new Exception('Impossible de construire la date : ' . $date);
        }
    }

    /**
     * @param string $date
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getDateAsString($date = 'now'): string
    {
        return self::getDate($date)->format('H:i Y-M-d');
    }

    /**
     * Get the start of the day (midnight) for the given date
     *
     * @param string|DateTimeInterface $date The date to process (default: 'now')
     * @return DateTime Start of the day as DateTime
     * @throws Exception If the date is invalid
     */
    public static function getDateFromZero(string|DateTimeInterface $date = 'now'): DateTime
    {
        try {
            $dateTime = $date instanceof DateTimeInterface 
                ? DateTime::createFromInterface($date)
                : new DateTime($date);
                
            $dateTime->setTime(0, 0, 0);
            return $dateTime;
        } catch (Exception $e) {
            throw new Exception(sprintf('Invalid date provided to getDateFromZero: %s', $e->getMessage()));
        }
    }

    /**
     * Get the end of the day (23:59:59) for the given date
     *
     * @param string|DateTimeInterface $date The date to process (default: 'now')
     * @return DateTime End of the day as DateTime
     * @throws Exception If the date is invalid
     */
    public static function getDateAtEnd(string|DateTimeInterface $date = 'now'): DateTime
    {
        try {
            $dateTime = $date instanceof DateTimeInterface 
                ? DateTime::createFromInterface($date)
                : new DateTime($date);
                
            $dateTime->setTime(23, 59, 59);
            return $dateTime;
        } catch (Exception $e) {
            throw new Exception(sprintf('Invalid date provided to getDateAtEnd: %s', $e->getMessage()));
        }
    }

    public static function getIntervalFromDates($date_interval = '15-02-2020/17-14-2020'): array
    {
        try {
            if (self::isValidDate($date_interval)) {
                $stored = $date_interval;

                $date = (new DateTime(self::getDate($date_interval)->format('Y-m-d')))->add(new \DateInterval('P1D'));

                $date_interval = $stored . '/' . $date->format('d-m-Y 00:00');
            }

            $dates = explode('-', $date_interval);

            if (2 == count($dates)) {
                return [
                    'start' => new DateTime(str_replace('/', '-', $dates[0])),
                    'end' => new DateTime(str_replace('/', '-', $dates[1])),
                ];
            } else {
                $dates = explode('/', $date_interval);
                if (2 == count($dates)) {
                    return [
                        'start' => new DateTime($dates[0]),
                        'end' => new DateTime($dates[1]),
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'start' => null,
                'end' => null,
            ];
        }

        return [
            'start' => null,
            'end' => null,
        ];
    }

    /**
     * @param int $hours
     *
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function addHoursToDate(DateTimeInterface $dateTime, $hours = 1): DateTime|null
    {
        return self::addMinutesToDate($dateTime, self::convertHoursToMinutes($hours));
    }

    /**
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function addTimeToDate(?DateTimeInterface $dateTime, ?DateTimeInterface $time): DateTime|null
    {
        if (!$time && $dateTime) {
            return self::dateFromInterface($dateTime);
        }

        if ($time && !$dateTime) {
            return null;
        }

        $t = $time->format('H:i');

        $t = explode(':', $t);

        if (2 !== count($t)) {
            return self::dateFromInterface($dateTime);
        }

        $d = self::addMinutesToDate($dateTime, self::convertHoursToMinutes($t[0]) + $t[1]);

        return $d;
    }

    /**
     * Ajoute un nombre de secondes à un objet DateTimeInterface.
     *
     * @throws Exception
     */
    public static function addSecondsToDate(?DateTimeInterface $dateTime, ?int $seconds): ?DateTimeInterface
    {
        if (null === $dateTime || null === $seconds) {
            return null;
        }

        $dateTime = self::dateFromInterface($dateTime); // Convertir en DateTime si nécessaire

        $interval = new \DateInterval('PT' . $seconds . 'S');
        $dateTime->add($interval);

        return $dateTime;
    }

    /**
     * @return DateTime
     *
     * @throws Exception
     */
    public static function dateFromInterface(DateTimeInterface $dateTime): DateTime
    {
        return new DateTime($dateTime->format('Y-m-d H:i:s'));
    }

    public static function convertDaysToMinutes($days = 1): int
    {
        return $days * 24 * 60;
    }

    public static function convertHoursToMinutes($days = 1): int
    {
        return $days * 60;
    }

    public static function convertMinutesToHours($minutes = 1): float|int
    {
        if ($minutes < 1) {
            return 0;
        }

        $hours = floor($minutes / 60);

        return $hours;
    }

    /**
     * @param int $days
     *
     * @return DateTime|null
     *
     * @throws Exception
     */
    public static function addDaysToDate(DateTimeInterface $dateTime, $days = 1): DateTime|null
    {
        return self::addMinutesToDate($dateTime, self::convertDaysToMinutes($days));
    }

    /**
     * @param int $minutes
     *
     * @return DateTime|null
     *
     * @throws Exception
     */
    public static function addMinutesToDate(DateTimeInterface $dateTime, $minutes = 15): DateTime|null
    {
        if (!$minutes || 0 === $minutes) {
            $minutes = 15;
        }

        $hours = intdiv($minutes, 60);

        $minutes %= 60;

        $days = intdiv($hours, 24);

        $hours %= 24;

        $dateTime = new DateTime($dateTime->format('Y-m-d H:i'));

        try {
            return $dateTime->add(new \DateInterval('P0Y0M' . $days . 'DT' . $hours . 'H' . $minutes . 'M0S'));
        } catch (Exception $e) {
            return (new DateTime())->add(new \DateInterval('P0Y0M' . $days . 'DT' . $hours . 'H' . $minutes . 'M0S'));
        }
    }

    /**
     * @param DateTime $date
     * @param int       $minutes
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function subtractMinutesToDate(DateTimeInterface $date, $minutes = 1): DateTime|null
    {
        $date = $date->format('Y-m-d H:i:s');
        $time = strtotime($date);
        $time -= $minutes * 60;
        $date = date('Y-m-d H:i:s', $time);

        return new DateTime($date);
    }

    /**
     * @param DateTime $date
     * @param int       $hours
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function subtractHoursToDate(DateTimeInterface $date, $hours = 1): DateTime
    {
        return self::subtractMinutesToDate($date, $hours * 60);
    }

    /**
     * @param DateTime $date
     * @param int       $days
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function subtractDaysToDate(DateTimeInterface $date, $days = 1): DateTime
    {
        return self::subtractHoursToDate($date, $days * 24);
    }

    public static function isValidDate(string $date): bool
    {
        try {
            new DateTime($date);

            return $date && true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Function to get all the dates in given range

    /**
     * @param string $format
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getDatesFromRange(string $start, string $end, $format = 'd-m-Y'): array
    {
        // Declare an empty array
        $array = [];

        // Variable that store the date interval
        // of period 1 day
        $interval = new \DateInterval('P1D');

        $realEnd = new DateTime($end);

        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = [
                'date' => $date->format($format),
                'day' => self::parseDay($date->format('D')),
            ];
        }

        // Return the array elements
        return $array;
    }

    public static function getWeekDayOfDate(DateTimeInterface $date): mixed
    {
        return self::parseDay($date->format('D'));
    }

    public static function parseDay($dayShortEn)
    {
        $dayShortEn = strtoupper($dayShortEn);

        if (isset(self::DAYS[$dayShortEn])) {
            return self::DAYS[$dayShortEn];
        }

        return null;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function accordDateToTime(DateTimeInterface $dateTime, string $time): DateTime
    {
        $f = $dateTime->format('d-m-Y') . ' ' . $time;

        return new DateTime($f);
    }

    public static function isRegularTime(string $time)
    {
        $t = explode(':', $time);

        return (!(intval($t[0]) >= 24) && !(intval($t[1]) >= 60)) && preg_match('#^[0-9]{2}:[0-9]{2}#', $time);
    }

    /**
     * @return DatePeriod
     *
     * @throws Exception
     */
    public static function getDayDatesInPeriod(DateTimeInterface $start_date, DateTimeInterface $end_date): DatePeriod
    {
        return new DatePeriod(
            new DateTime($start_date->format('Y-m-d')),
            new \DateInterval('P1D'),
            new DateTime($end_date->format('Y-m-d'))
        );
    }

    public static function getMonthsOfYear($year)
    {
        $months = [];

        foreach (range(1, 12) as $m) {
            $start = self::getDate("01-$m-" . $year . ' 00:00');

            $end = self::subtractDaysToDate(self::getDate('01-' . (($m + 1) % 12) . '-' . $year . ' 23:59'));

            $months[] = [
                'start' => $start,
                'end' => $end,
            ];
        }

        return $months;
    }

    public static function getDayOfWeek(DateTimeInterface $date): string
    {
        $date = $date->format('y-m-d');
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $dayOfWeek = date('w', strtotime($date));

        return strtoupper($days[$dayOfWeek]);
    }

    public static function adjustFilterInterval(
        ?DateTimeInterface $start_date = null,
        ?DateTimeInterface $end_date = null
    ): array {
        if (($start_date && $end_date) && $end_date->format('Y-m-d H:i') < $start_date->format('Y-m-d H:i')) {
            $end_date = $start_date;
        }

        if ($start_date && $end_date) {
            if ($start_date->format('Y-m-d H:i') == $end_date->format('Y-m-d H:i')) {
                $end_date = DateUtil::addMinutesToDate(DateUtil::addHoursToDate($end_date, 23), 59);
            }
        }

        if ($end_date) {
            if ($end_date->format('Y-m-d H:i') == $end_date->format('Y-m-d 00:00')) {
                $end_date = DateUtil::addMinutesToDate(DateUtil::addHoursToDate($end_date, 23), 59);
            }
        }

        return [$start_date, $end_date];
    }

    public static function formatDateDay(DateTimeInterface $date): string
    {
        return $date->format('j/m/Y');
    }
}
