<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use DateTime;
use SamyAsm\Chrono\Traits\ChronoCalendarTrait;
use SamyAsm\Chrono\Traits\ChronoFormatTrait;

#[CoversClass(ChronoCalendarTrait::class)]
#[CoversClass(ChronoFormatTrait::class)]
class ChronoCalendarTest extends TestCase
{
    public function testGetFirstDayOfTheWeekFromDate(): void
    {
        // Test with a date string
        $firstDay = Chrono::getFirstDayOfWeek('2023-06-15'); // A Thursday
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Should be the Monday of the same week
        
        // Test with a DateTime object
        $date = new DateTime('2023-06-15');
        $firstDay = Chrono::getFirstDayOfWeek($date);
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d'));
    }

    public function testFormatDateDay(): void
    {
        $formattedDate = Chrono::formatDate('2023-06-15', 'd/m/Y');
        $this->assertEquals('15/06/2023', $formattedDate);
    }

    public function testGetDayOfWeek(): void
    {
        // Test with a date string
        $weekday = Chrono::getWeekday('2023-06-15'); // A Thursday
        // Convert weekday number to day name (1=Monday, 7=Sunday)
        $dayNames = [
            1 => 'MONDAY',
            2 => 'TUESDAY',
            3 => 'WEDNESDAY',
            4 => 'THURSDAY',
            5 => 'FRIDAY',
            6 => 'SATURDAY',
            7 => 'SUNDAY'
        ];
        $this->assertEquals('THURSDAY', $dayNames[$weekday]);

        // Test another day of the week
        $weekday = Chrono::getWeekday('2023-06-16'); // A Friday
        $this->assertEquals('FRIDAY', $dayNames[$weekday]);
    }

    public function testGetWeekDayOfDate(): void
    {
        // Map of date strings to expected day names
        $testCases = [
            '2023-06-11' => 'SUNDAY',
            '2023-06-12' => 'MONDAY',
            '2023-06-13' => 'TUESDAY',
            '2023-06-14' => 'WEDNESDAY',
            '2023-06-15' => 'THURSDAY',
            '2023-06-16' => 'FRIDAY',
            '2023-06-17' => 'SATURDAY',
        ];

        // Map of weekday numbers to day names (1=Monday, 7=Sunday)
        $dayNames = [
            1 => 'MONDAY',
            2 => 'TUESDAY',
            3 => 'WEDNESDAY',
            4 => 'THURSDAY',
            5 => 'FRIDAY',
            6 => 'SATURDAY',
            7 => 'SUNDAY'
        ];

        foreach ($testCases as $dateStr => $expectedDay) {
            $weekday = Chrono::getWeekday($dateStr);
            $this->assertEquals($expectedDay, $dayNames[$weekday]);
        }
    }

    public function testGetMonthFromPosition(): void
    {
        // Test valid months using getMonthName with a date
        $months = [
            1 => 'JAN',
            6 => 'JUN',
            12 => 'DEC'
        ];

        foreach ($months as $position => $expected) {
            $monthName = Chrono::getMonthName("2023-$position-01");
            $this->assertEquals($expected, $monthName);
        }
        
        // Test getMonthName with a month number directly
        $this->assertEquals('JAN', Chrono::getMonthName(1));
        $this->assertEquals('JUN', Chrono::getMonthName(6));
        $this->assertEquals('DEC', Chrono::getMonthName(12));
        
        // Test an invalid position
        $this->assertEquals('UNK', Chrono::getMonthName(13));
    }

    public function testGetMonthday(): void
    {
        // Test with a date
        $date = '2023-06-15';
        
        // Get day of month (15)
        $this->assertEquals(15, Chrono::getMonthday($date, 0));
        
        // Get month name (JUN)
        $this->assertEquals('JUN', Chrono::getMonthName($date));

        // Test with another date
        $date = '2023-12-25';
        $this->assertEquals(25, Chrono::getMonthday($date, 0));
        $this->assertEquals('DEC', Chrono::getMonthName($date));
    }
}
