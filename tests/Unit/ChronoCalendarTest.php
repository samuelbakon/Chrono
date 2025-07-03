<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\ChronoCalendar;

class ChronoCalendarTest extends TestCase
{
    public function testGetFirstDayOfTheWeekFromDate(): void
    {
        $date = new \DateTime('2023-06-15'); // A Thursday
        $firstDay = ChronoCalendar::getFirstDayOfTheWeekFromDate($date);

        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Should be the Monday of the same week
    }

    public function testFormatDateDay(): void
    {
        $date = new \DateTime('2023-06-15');
        $formattedDate = ChronoCalendar::formatDateDay($date);

        $this->assertEquals('15/06/2023', $formattedDate);
    }

    public function testGetDayOfWeek(): void
    {
        // Test with a DateTime object
        $date = new \DateTime('2023-06-15'); // A Thursday
        $dayOfWeek = ChronoCalendar::getDayOfWeek($date);
        $this->assertEquals('THURSDAY', $dayOfWeek);

        // Test another day of the week
        $date = new \DateTime('2023-06-16'); // A Friday
        $dayOfWeek = ChronoCalendar::getDayOfWeek($date);
        $this->assertEquals('FRIDAY', $dayOfWeek);
    }

    public function testGetWeekDayOfDate(): void
    {
        // The method uses format('D') which returns the day in 3 letters in English
        // Let's verify the conversion works for all days
        $days = [
            '2023-06-11' => 'SUNDAY',
            '2023-06-12' => 'MONDAY',
            '2023-06-13' => 'TUESDAY',
            '2023-06-14' => 'WEDNESDAY',
            '2023-06-15' => 'THURSDAY',
            '2023-06-16' => 'FRIDAY',
            '2023-06-17' => 'SATURDAY',
        ];

        foreach ($days as $dateStr => $expectedDay) {
            $date = new \DateTime($dateStr);
            $this->assertEquals($expectedDay, ChronoCalendar::getWeekDayOfDate($date));
        }
    }

    public function testGetMonthFromPosition(): void
    {
        // Test valid months
        $months = [
            1 => 'JAN',
            6 => 'JUN',
            12 => 'DEC'
        ];

        foreach ($months as $position => $expected) {
            $this->assertEquals($expected, ChronoCalendar::getMonthFromPosition($position));
        }

        // Test an invalid position
        $this->assertEquals('UNK', ChronoCalendar::getMonthFromPosition(13));
    }

    public function testGetMonthday(): void
    {
        // Test with a date (mode 0 to get the month number)
        $date = new \DateTime('2023-06-15');
        $this->assertEquals(6, ChronoCalendar::getMonthday($date->format('Y-m-d'), 0));

        // Test with a date string (mode 0 to get the month number)
        $this->assertEquals(12, ChronoCalendar::getMonthday('2023-12-25', 0));

        // Test with mode 1 to get the month name
        $date = new \DateTime('2023-06-15');
        $this->assertEquals('JUN', ChronoCalendar::getMonthday($date->format('Y-m-d'), 1));

        // Test with a date string and mode 1
        $this->assertEquals('DEC', ChronoCalendar::getMonthday('2023-12-25', 1));
    }
}
