<?php

namespace SamyAsm\Chrono;

use DateTime;
use DateTimeInterface;
use DatePeriod;
use Exception;
use InvalidArgumentException;

/**
 * Handles date ranges, intervals, and periods
 */
class ChronoPeriod
{
    public const PERIODS = [
        'DAY' => 'DAY',
        'YESTERDAY' => 'YESTERDAY',
        'MONTH' => 'MONTH',
        'TOMORROW' => 'TOMORROW',
        'WEEK' => 'WEEK',
        'YEAR' => 'YEAR',
    ];

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
        $now = new DateTime();
        $start = clone $now;
        $end = clone $now;

        switch (strtoupper($period)) {
            case 'DAY':
            case 'TODAY':
                $start->setTime(0, 0, 0);
                $end->setTime(23, 59, 59);
                break;

            case 'YESTERDAY':
                $start->modify('-1 day')->setTime(0, 0, 0);
                $end->modify('-1 day')->setTime(23, 59, 59);
                break;

            case 'TOMORROW':
                $start->modify('+1 day')->setTime(0, 0, 0);
                $end->modify('+1 day')->setTime(23, 59, 59);
                break;

            case 'WEEK':
                $start->modify('monday this week')->setTime(0, 0, 0);
                $end->modify('sunday this week')->setTime(23, 59, 59);
                break;

            case 'MONTH':
                $start->modify('first day of this month')->setTime(0, 0, 0);
                $end->modify('last day of this month')->setTime(23, 59, 59);
                break;

            case 'YEAR':
                $start->modify('first day of january this year')->setTime(0, 0, 0);
                $end->modify('last day of december this year')->setTime(23, 59, 59);
                break;

            default:
                throw new InvalidArgumentException(sprintf('Unknown period: %s', $period));
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get date range from a date interval string
     *
     * @param string $dateInterval Date interval string in format 'd-m-Y/d-m-Y' or 'd-m-Y H:i/d-m-Y H:i'
     * @return array{start: DateTimeInterface, end: DateTimeInterface}
     * @throws Exception If date parsing fails
     */
    public static function getIntervalFromDates(string $dateInterval): array
    {
        $parts = explode('/', $dateInterval);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid date interval format. Expected "start/end"');
        }

        // Try to detect if time is included in the format
        $timeIncluded = strpos($parts[0], ':') !== false;
        $format = $timeIncluded ? 'd-m-Y H:i' : 'd-m-Y';

        $start = DateTime::createFromFormat($format, $parts[0]);
        $end = DateTime::createFromFormat($format, $parts[1]);

        if ($start === false || $end === false) {
            throw new Exception('Failed to parse date interval');
        }

        if (!$timeIncluded) {
            $start->setTime(0, 0, 0);
            $end->setTime(23, 59, 59);
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get all dates between two dates
     *
     * @param string $start Start date string
     * @param string $end End date string
     * @param string $format Output format (default: 'Y-m-d')
     * @return array<string> Array of dates in the specified format
     * @throws Exception If date parsing fails
     */
    public static function getDatesFromRange(string $start, string $end, string $format = 'Y-m-d'): array
    {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $endDate->setTime(0, 0, 1); // Include the end date in the result

        $interval = new \DateInterval('P1D'); // 1 day interval
        $dateRange = new DatePeriod($startDate, $interval, $endDate);

        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format($format);
        }

        return $dates;
    }

    /**
     * Get all days between two dates as DatePeriod
     *
     * @param DateTimeInterface $startDate Start date
     * @param DateTimeInterface $endDate End date
     * @return DatePeriod Period containing all days between dates
     */
    public static function getDayDatesInPeriod(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): DatePeriod {
        $start = DateTime::createFromInterface($startDate)->setTime(0, 0, 0);
        $end = DateTime::createFromInterface($endDate)->setTime(23, 59, 59);

        $interval = new \DateInterval('P1D'); // 1 day interval
        return new DatePeriod($start, $interval, $end);
    }

    /**
     * Adjust filter interval to ensure it's valid
     *
     * @param DateTimeInterface|null $startDate Start date (or null for current date - 1 month)
     * @param DateTimeInterface|null $endDate End date (or null for current date)
     * @return array{start: DateTimeInterface, end: DateTimeInterface} Adjusted date range
     */
    public static function adjustFilterInterval(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ): array {
        $end = $endDate ? DateTime::createFromInterface($endDate) : new DateTime();

        if ($startDate === null) {
            $start = clone $end;
            $start->modify('-1 month');
        } else {
            $start = DateTime::createFromInterface($startDate);
        }

        // Ensure start is before end
        if ($start > $end) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get today's date range (from 00:00:00 to 23:59:59)
     *
     * @return array{start: DateTime, end: DateTime} Today's date range
     */
    public static function getIntervalOfToday(): array
    {
        $start = new DateTime('today');
        $end = new DateTime('today');
        $end->setTime(23, 59, 59);

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
