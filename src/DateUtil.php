<?php

namespace Samy\SuperDate;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;

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

    public static function timeToDate(int $timestamp): ?DateTimeInterface
    {
        $dateTime = new DateTime("@$timestamp"); // Crée un objet DateTime depuis le timestamp
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get())); // Définit le fuseau horaire

        return $dateTime;
    }

    /**
     * @return DateTimeInterface|null
     */
    public static function getFirstDayOfTheMonthFromDate(DateTimeInterface $dateTime)
    {
        try {
            return new DateTime($dateTime->format('01-m-Y'));
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @return DateTimeInterface|null
     */
    public static function getFirstDayOfTheWeekFromDate(DateTimeInterface $dateTime)
    {
        try {
            $rank = self::getWeekday($dateTime->format('Y-m-d'));
            $rank -= 1;
            $date = new DateTime($dateTime->format('Y-m-d'));
            $date = $date->sub(new \DateInterval('P'.$rank.'D'));

            return $date;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @return DateTimeInterface|null
     */
    public static function getFirstDayOfTheYearFromDate(DateTimeInterface $dateTime)
    {
        try {
            return new DateTime($dateTime->format('01-01-Y'));
        } catch (Exception $exception) {
            return null;
        }
    }

    public static function getWeekday($date)
    {
        return date('w', strtotime($date));
    }

    public static function getMonthday($date, $mode = 0) // 0 for int, another to get name
    {$date = new DateTime($date);
        $date = $date->format('m');

        if (0 === $mode) {
            return intval($date);
        }

        return self::getMonthFromPosition(intval($date));
    }

    public static function getMonthFromPosition($position)
    {
        if (isset(self::MONTHS[$position])) {
            return self::MONTHS[$position];
        }

        return 'UNK';
    }

    public static function getYearDay()
    {
        $date = new DateTime('Y');
        $date = date($date->format('y-01-01'));
        $diff = new DateTime();
        try {
            $diff = (new DateTime())->diff(new DateTime($date))->days;
        } catch (Exception $e) {
        }

        return $diff;
    }

    public static function getYear()
    {
        try {
            $date = new DateTime('Y');

            return $date->format('Y');
        } catch (Exception $exception) {
            return '1759';
        }
    }

    /**
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function interfaceToDateTime(?DateTimeInterface $dateTime = null): ?DateTime
    {
        if (!$dateTime) {
            return null;
        }

        return new DateTime($dateTime->format('Y-m-d H:i'));
    }

    public static function getDateDayDif(DateTimeInterface $dateTime1, DateTimeInterface $dateTime2, $normalize = false)
    {
        if ($normalize) {
            $dateTime1 = new DateTime($dateTime1->format('Y-m-d'));
            $dateTime2 = new DateTime($dateTime2->format('Y-m-d'));
        }
        $diff = date_diff($dateTime1, $dateTime2);
        $_negative = (0 === $diff->invert) ? -1 : 1;

        return $diff->days * $_negative;
    }

    public static function getMinuteDateDif(DateTimeInterface $dateTime1, DateTimeInterface $dateTime2, $absolute = false)
    {
        return self::getSecondsDateDif($dateTime1, $dateTime2) / 60;
    }

    public static function getSecondsDateDif(DateTimeInterface $dateTime1, DateTimeInterface $dateTime2): int
    {
        $diff = $dateTime1->diff($dateTime2);
        $seconds = ($diff->days * 24 * 60 * 60) + ($diff->h * 60 * 60) + $diff->i * 60 + $diff->s;
        $is_negative = (0 === $diff->invert) ? -1 : 1;

        return $seconds * $is_negative;
    }

    /**
     * @return float|int
     *
     * @throws Exception
     */
    public static function getRemainingDay(DateTimeInterface $dateTime): int
    {
        return self::getDateDayDif($dateTime, new DateTime());
    }

    public static function getIntervalOfToday()
    {
        return self::getInterval('DAY');
    }

    public static function lastSeenHelp($date): string
    {
        $mydate = date('Y-m-d H:i:s');
        $theDiff = '';
        // echo $mydate;//2014-06-06 21:35:55
        $datetime1 = date_create($date);
        $datetime2 = date_create($mydate);
        $interval = date_diff($datetime1, $datetime2);
        // echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
        $min = $interval->format('%i');
        $sec = $interval->format('%s');
        $hour = $interval->format('%h');
        $mon = $interval->format('%m');
        $day = $interval->format('%d');
        $year = $interval->format('%y');
        if ('00000' == $interval->format('%i%h%d%m%y')) {
            // echo $interval->format('%i%h%d%m%y')."<br>";
            return $sec.' Secondes';
        } elseif ('0000' == $interval->format('%h%d%m%y')) {
            return $min.' Minutes';
        } elseif ('000' == $interval->format('%d%m%y')) {
            return $hour.' Hours';
        } elseif ('00' == $interval->format('%m%y')) {
            return $day.' Days';
        } elseif ('0' == $interval->format('%y')) {
            return $mon.' Month';
        } else {
            return $year.' Year';
        }
    }

    /**
     * @return DateTimeInterface[]|[]
     */
    public static function getInterval($period): array
    {
        try {
            $date = new DateTime();
            $today = $date->format('Y-m-d');

            $today = new DateTime($today);

            if ('DAY' == $period) {
                $start_date = $today;
                $limit = (new DateTime($start_date->format('Y-m-d')))->add(new \DateInterval('P1D'));
            } elseif ('YESTERDAY' == $period) {
                $limit = $today;
                $start_date = (new DateTime($limit->format('Y-m-d')))->sub(new \DateInterval('P1D'));
            } elseif ('MONTH' == $period) {
                $start_date = DateUtil::getFirstDayOfTheMonthFromDate($today);
                $add = DateUtil::getMonthday((new DateTime())->format('Y-m-d'));
                $add = 30 - ($add + 0);
                $limit = $today->add(new \DateInterval('P'.$add.'D'));
            } elseif ('TOMORROW' == $period) {
                $start_date = (new DateTime($today->format('Y-m-d')))->add(new \DateInterval('P1D'));
                $limit = $limit = (new DateTime($start_date->format('Y-m-d')))->add(new \DateInterval('P1D'));
            } elseif ('WEEK' == $period) {
                $start_date = DateUtil::getFirstDayOfTheWeekFromDate($today);
                $add = DateUtil::getWeekday((new DateTime())->format('Y-m-d'));
                $add = 7 - ($add + 0);
                $limit = $today->add(new \DateInterval('P'.$add.'D'));
            } else {// default YEAR
                $start_date = DateUtil::getFirstDayOfTheYearFromDate($today);
                $add = DateUtil::getYearDay();
                $add = 365 - ($add + 1);
                $limit = $today->add(new \DateInterval('P'.$add.'D'));
            }
        } catch (Exception $exception) {
            return [
                'start' => null,
                'end' => null,
            ];
        }

        return [
            'start' => $start_date,
            'end' => $limit,
        ];
    }

    /**
     * @param string $date
     *
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function getDate($date = 'now'): ?DateTime
    {
        if ($date) {
            try {
                return new DateTime($date);
            } catch (\Throwable $t) {
                throw new Exception('Impossible de construire la date : '.$date);
            }
        }

        return null;
    }

    /**
     * @param string $date
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getDateAsString($date = 'now')
    {
        return self::getDate($date)->format('H:i Y-M-d');
    }

    /**
     * Get first date of date day.
     *
     * @param string $date
     *
     * @return DateTimeInterface
     *
     * @throws Exception
     */
    public static function getDateFromZero($date = 'now')
    {
        return new DateTime((new DateTime($date))->format('Y-m-d').' 00:00');
    }

    /**
     * @param string $date
     *
     * @return DateTimeInterface
     *
     * @throws Exception
     */
    public static function getDateAtEnd($date = 'now')
    {
        return self::addHoursToDate(new DateTime((new DateTime($date))->format('Y-m-d').' 00:00'), 24);
    }

    public static function getIntervalFromDates($date_interval = '15-02-2020/17-14-2020')
    {
        try {
            if (self::isValidDate($date_interval)) {
                $stored = $date_interval;

                $date = (new DateTime(self::getDate($date_interval)->format('Y-m-d')))->add(new \DateInterval('P1D'));

                $date_interval = $stored.'/'.$date->format('d-m-Y 00:00');
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
        } catch (Exception $exception) {
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
    public static function addHoursToDate(DateTimeInterface $dateTime, $hours = 1)
    {
        return self::addMinutesToDate($dateTime, self::convertHoursToMinutes($hours));
    }

    /**
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function addTimeToDate(?DateTimeInterface $dateTime, ?DateTimeInterface $time)
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

        $interval = new \DateInterval('PT'.$seconds.'S');
        $dateTime->add($interval);

        return $dateTime;
    }

    /**
     * @return DateTime
     *
     * @throws Exception
     */
    public static function dateFromInterface(DateTimeInterface $dateTime)
    {
        return new DateTime($dateTime->format('Y-m-d H:i:s'));
    }

    public static function convertDaysToMinutes($days = 1)
    {
        return $days * 24 * 60;
    }

    public static function convertHoursToMinutes($days = 1)
    {
        return $days * 60;
    }

    public static function convertMinutesToHours($minutes = 1)
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
    public static function addDaysToDate(DateTimeInterface $dateTime, $days = 1)
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
    public static function addMinutesToDate(DateTimeInterface $dateTime, $minutes = 15)
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
            return $dateTime->add(new \DateInterval('P0Y0M'.$days.'DT'.$hours.'H'.$minutes.'M0S'));
        } catch (Exception $exception) {
            return (new DateTime())->add(new \DateInterval('P0Y0M'.$days.'DT'.$hours.'H'.$minutes.'M0S'));
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
    public static function subtractMinutesToDate(DateTimeInterface $date, $minutes = 1)
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
    public static function subtractHoursToDate(DateTimeInterface $date, $hours = 1)
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
    public static function subtractDaysToDate(DateTimeInterface $date, $days = 1)
    {
        return self::subtractHoursToDate($date, $days * 24);
    }

    public static function isValidDate($date)
    {
        try {
            new DateTime($date);

            return $date && true;
        } catch (Exception $exception) {
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
    public static function getDatesFromRange(string $start, string $end, $format = 'd-m-Y')
    {
        // Declare an empty array
        $array = [];

        // Variable that store the date interval
        // of period 1 day
        $interval = new \DateInterval('P1D');

        $realEnd = new DateTime($end);

        $realEnd->add($interval);

        $period = new \DatePeriod(new DateTime($start), $interval, $realEnd);

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

    public static function getWeekDayOfDate(DateTimeInterface $date)
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
    public static function accordDateToTime(DateTimeInterface $dateTime, string $time)
    {
        $f = $dateTime->format('d-m-Y').' '.$time;

        return new DateTime($f);
    }

    public static function isRegularTime(string $time)
    {
        $t = explode(':', $time);

        return (!(intval($t[0]) >= 24) && !(intval($t[1]) >= 60)) && preg_match('#^[0-9]{2}:[0-9]{2}#', $time);
    }

    /**
     * @return \DatePeriod
     *
     * @throws Exception
     */
    public static function getDayDatesInPeriod(DateTimeInterface $start_date, DateTimeInterface $end_date)
    {
        return new \DatePeriod(
            new DateTime($start_date->format('Y-m-d')),
            new \DateInterval('P1D'),
            new DateTime($end_date->format('Y-m-d'))
        );
    }

    public static function getMonthsOfYear($year)
    {
        $months = [];

        foreach (range(1, 12) as $m) {
            $start = self::getDate("01-$m-".$year.' 00:00');

            $end = self::subtractDaysToDate(self::getDate('01-'.(($m + 1) % 12).'-'.$year.' 23:59'));

            $months[] = [
                'start' => $start,
                'end' => $end,
            ];
        }

        return $months;
    }

    public static function getDayOfWeek(DateTimeInterface $date)
    {
        $date = $date->format('y-m-d');
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $dayOfWeek = date('w', strtotime($date));

        return strtoupper($days[$dayOfWeek]);
    }

    public static function adjustFilterInterval(?DateTimeInterface $start_date = null, ?DateTimeInterface $end_date = null)
    {
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

    public static function formatDateDay(DateTimeInterface $date): string{
        return $date->format('j/m/Y');
    }
}
