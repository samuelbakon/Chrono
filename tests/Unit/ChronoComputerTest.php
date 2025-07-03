<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\ChronoComputer;

class ChronoComputerTest extends TestCase
{
    public function testAddDaysToDate(): void
    {
        $date = new \DateTime('2023-06-15 14:30:00');
        $newDate = ChronoComputer::addDaysToDate($date, 5);

        $this->assertEquals('2023-06-20 14:30:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testGetMinuteDateDif(): void
    {
        $date1 = new \DateTime('2023-06-15 10:00:00');
        $date2 = new \DateTime('2023-06-15 14:30:00');

        // Method returns a negative value when the first date is before the second
        $minutesDiff = ChronoComputer::getMinuteDateDif($date1, $date2);
        $this->assertEquals(-270, $minutesDiff); // 4h30 = 270 minutes, negative because date1 < date2

        // Test in the other direction
        $minutesDiff = ChronoComputer::getMinuteDateDif($date2, $date1);
        $this->assertEquals(270, $minutesDiff); // 4h30 = 270 minutes, positive because date2 > date1
    }

    public function testConvertDaysToMinutes(): void
    {
        $this->assertEquals(1440, ChronoComputer::convertDaysToMinutes(1)); // 1 day = 1440 minutes
        $this->assertEquals(2880, ChronoComputer::convertDaysToMinutes(2));
    }

    public function testConvertHoursToMinutes(): void
    {
        $this->assertEquals(60, ChronoComputer::convertHoursToMinutes(1));
        $this->assertEquals(120, ChronoComputer::convertHoursToMinutes(2));
    }

    public function testConvertMinutesToHours(): void
    {
        $this->assertEquals(1.0, ChronoComputer::convertMinutesToHours(60));
        $this->assertEquals(1.5, ChronoComputer::convertMinutesToHours(90));
    }

    public function testAddTimeToDate(): void
    {
        $date = new \DateTime('2023-06-15 10:00:00');
        $time = new \DateTime('1970-01-01 02:30:00'); // Only time part is used

        $newDate = ChronoComputer::addTimeToDate($date, $time);
        $this->assertEquals('2023-06-15 12:30:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testSubtractMinutesToDate(): void
    {
        $date = new \DateTime('2023-06-15 10:00:00');
        $newDate = ChronoComputer::subtractMinutesToDate($date, 30);
        $this->assertEquals('2023-06-15 09:30:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testSubtractHoursToDate(): void
    {
        $date = new \DateTime('2023-06-15 10:00:00');
        $newDate = ChronoComputer::subtractHoursToDate($date, 2);
        $this->assertEquals('2023-06-15 08:00:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testSubtractDaysToDate(): void
    {
        $date = new \DateTime('2023-06-15 10:00:00');
        $newDate = ChronoComputer::subtractDaysToDate($date, 2);
        $this->assertEquals('2023-06-13 10:00:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testGetRemainingDay(): void
    {
        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');
        $yesterday = new \DateTime('yesterday');

        $this->assertEquals(1, ChronoComputer::getRemainingDay($yesterday));
        $this->assertEquals(0, ChronoComputer::getRemainingDay($today));
        $this->assertEquals(-1, ChronoComputer::getRemainingDay($tomorrow));
    }

    public function testLastSeenHelp(): void
    {
        $now = new \DateTime();

        // Test with current time (should be 'Just now' or '1 minute')
        $this->assertMatchesRegularExpression('/(Just now|1 Minute)/', ChronoComputer::lastSeenHelp($now));

        // Test with 5 minutes ago
        $fiveMinutesAgo = clone $now;
        $fiveMinutesAgo->modify('-5 minutes');
        $this->assertEquals('5 Minutes', ChronoComputer::lastSeenHelp($fiveMinutesAgo));

        // Test with 2 hours ago
        $twoHoursAgo = clone $now;
        $twoHoursAgo->modify('-2 hours');
        $this->assertEquals('2 Hours', ChronoComputer::lastSeenHelp($twoHoursAgo));

        // Test with 3 days ago
        $threeDaysAgo = clone $now;
        $threeDaysAgo->modify('-3 days');
        $this->assertEquals('3 Days', ChronoComputer::lastSeenHelp($threeDaysAgo));
    }
}
