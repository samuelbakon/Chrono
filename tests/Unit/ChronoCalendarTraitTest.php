<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use SamyAsm\Chrono\Traits\ChronoCalendarTrait;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

#[CoversClass(ChronoCalendarTrait::class)]
class ChronoCalendarTraitTest extends TestCase
{
    public function testGetFirstDayOfWeek(): void
    {
        // Test with a Wednesday (2023-06-14 is a Wednesday)
        $date = '2023-06-14';
        $firstDay = Chrono::getFirstDayOfWeek($date);
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Monday
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
        
        // Test with a Monday (2023-06-12 is a Monday)
        $monday = '2023-06-12';
        $firstDay = Chrono::getFirstDayOfWeek($monday);
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Same Monday
        
        // Test with a Sunday (2023-06-18 is a Sunday)
        $sunday = '2023-06-18';
        $firstDay = Chrono::getFirstDayOfWeek($sunday);
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Previous Monday
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14 14:30:45');
        $firstDay = Chrono::getFirstDayOfWeek($dateTime);
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d'));
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
    }

    public function testGetFirstDayOfMonth(): void
    {
        // Test with a date
        $date = '2023-06-14';
        $firstDay = Chrono::getFirstDayOfMonth($date);
        $this->assertEquals('2023-06-01', $firstDay->format('Y-m-d'));
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
        
        // Test with the first day of month
        $firstDayOfMonth = '2023-06-01';
        $result = Chrono::getFirstDayOfMonth($firstDayOfMonth);
        $this->assertEquals('2023-06-01', $result->format('Y-m-d'));
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14 14:30:45');
        $firstDay = Chrono::getFirstDayOfMonth($dateTime);
        $this->assertEquals('2023-06-01', $firstDay->format('Y-m-d'));
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
    }

    public function testGetFirstDayOfYear(): void
    {
        // Test with a date
        $date = '2023-06-14';
        $firstDay = Chrono::getFirstDayOfYear($date);
        $this->assertEquals('2023-01-01', $firstDay->format('Y-m-d'));
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
        
        // Test with the first day of year
        $firstDayOfYear = '2023-01-01';
        $result = Chrono::getFirstDayOfYear($firstDayOfYear);
        $this->assertEquals('2023-01-01', $result->format('Y-m-d'));
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14 14:30:45');
        $firstDay = Chrono::getFirstDayOfYear($dateTime);
        $this->assertEquals('2023-01-01', $firstDay->format('Y-m-d'));
        $this->assertEquals('00:00:00', $firstDay->format('H:i:s'));
    }

    public function testGetWeekday(): void
    {
        // Test with known dates (1=Monday to 7=Sunday)
        $this->assertEquals(1, Chrono::getWeekday('2023-06-12')); // Monday
        $this->assertEquals(2, Chrono::getWeekday('2023-06-13')); // Tuesday
        $this->assertEquals(3, Chrono::getWeekday('2023-06-14')); // Wednesday
        $this->assertEquals(4, Chrono::getWeekday('2023-06-15')); // Thursday
        $this->assertEquals(5, Chrono::getWeekday('2023-06-16')); // Friday
        $this->assertEquals(6, Chrono::getWeekday('2023-06-17')); // Saturday
        $this->assertEquals(7, Chrono::getWeekday('2023-06-18')); // Sunday
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14'); // Wednesday
        $this->assertEquals(3, Chrono::getWeekday($dateTime));
    }

    public function testGetMonthday(): void
    {
        // Test day of month (mode 0)
        $this->assertEquals(14, Chrono::getMonthday('2023-06-14', 0));
        $this->assertEquals(1, Chrono::getMonthday('2023-06-01', 0));
        $this->assertEquals(31, Chrono::getMonthday('2023-01-31', 0));
        
        // Test month name (mode 1)
        $this->assertEquals('JUN', Chrono::getMonthday('2023-06-14', 1));
        $this->assertEquals('DEC', Chrono::getMonthday('2023-12-25', 1));
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14');
        $this->assertEquals(14, Chrono::getMonthday($dateTime, 0));
        $this->assertEquals('JUN', Chrono::getMonthday($dateTime, 1));
        
        // Test invalid mode
        $this->expectException(InvalidArgumentException::class);
        Chrono::getMonthday('2023-06-14', 2);
    }

    public function testGetDayOfYear(): void
    {
        // Test with known dates (day of year is 0-based in PHP, but our method adds 1)
        $this->assertEquals(1, Chrono::getDayOfYear('2023-01-01'));
        $this->assertEquals(32, Chrono::getDayOfYear('2023-02-01'));
        $this->assertEquals(60, Chrono::getDayOfYear('2023-03-01')); // Not a leap year
        $this->assertEquals(91, Chrono::getDayOfYear('2023-04-01'));
        $this->assertEquals(365, Chrono::getDayOfYear('2023-12-31'));
        
        // Test with leap year
        $this->assertEquals(60, Chrono::getDayOfYear('2024-02-29')); // 2024 is a leap year
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-01-15');
        $this->assertEquals(15, Chrono::getDayOfYear($dateTime));
    }

    public function testGetYear(): void
    {
        $this->assertEquals(2023, Chrono::getYear('2023-06-14'));
        $this->assertEquals(2000, Chrono::getYear('2000-01-01'));
        $this->assertEquals(1999, Chrono::getYear('1999-12-31'));
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14');
        $this->assertEquals(2023, Chrono::getYear($dateTime));
    }

    public function testGetMonthName(): void
    {
        // Test with month numbers
        $this->assertEquals('JAN', Chrono::getMonthName(1));
        $this->assertEquals('JUN', Chrono::getMonthName(6));
        $this->assertEquals('DEC', Chrono::getMonthName(12));
        
        // Test with date strings
        $this->assertEquals('JAN', Chrono::getMonthName('2023-01-15'));
        $this->assertEquals('JUN', Chrono::getMonthName('2023-06-14'));
        $this->assertEquals('DEC', Chrono::getMonthName('2023-12-25'));
        
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-14');
        $this->assertEquals('JUN', Chrono::getMonthName($dateTime));
        
        // Test with invalid month number
        $this->assertEquals('UNK', Chrono::getMonthName(0));
        $this->assertEquals('UNK', Chrono::getMonthName(13));
        
        // Test with $full parameter (should be ignored in current implementation)
        $this->assertEquals('JAN', Chrono::getMonthName(1, true));
    }
}
