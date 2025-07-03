<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\ChronoCasting;

class ChronoCastingTest extends TestCase
{
    public function testTimeToDate(): void
    {
        $timestamp = 1686844800; // 2023-06-15 00:00:00 UTC
        $date = ChronoCasting::timeToDate($timestamp);

        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));

        // Test with invalid timestamp (should return null)
        $invalidDate = ChronoCasting::timeToDate(-1);
        $this->assertNull($invalidDate);
    }

    public function testGetDate(): void
    {
        // Test with DateTime object
        $dateTime = new \DateTime('2023-06-15');
        $date = ChronoCasting::getDate($dateTime);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));

        // Test with date string
        $date = ChronoCasting::getDate('2023-06-15');
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));

        // Test with null (should return current date)
        $date = ChronoCasting::getDate(null);
        $this->assertInstanceOf(\DateTime::class, $date);

        // Test with invalid date string (should throw exception)
        $this->expectException(\Exception::class);
        ChronoCasting::getDate('invalid-date');
    }

    public function testGetDateAsString(): void
    {
        $dateTime = new \DateTime('2023-06-15');

        // Test with default format
        $dateStr = ChronoCasting::getDateAsString($dateTime);
        $this->assertEquals('15-06-2023', $dateStr);

        // Test with custom format
        $dateStr = ChronoCasting::getDateAsString($dateTime, 'Y/m/d');
        $this->assertEquals('2023/06/15', $dateStr);

        // Test with date string
        $dateStr = ChronoCasting::getDateAsString('2023-06-15');
        $this->assertEquals('15-06-2023', $dateStr);
    }

    public function testIsValidDate(): void
    {
        $this->assertTrue(ChronoCasting::isValidDate('2023-12-31'));
        $this->assertTrue(ChronoCasting::isValidDate('31-12-2023', 'd-m-Y'));
        $this->assertFalse(ChronoCasting::isValidDate('2023-02-30')); // Invalid date
        $this->assertFalse(ChronoCasting::isValidDate('not-a-date'));
    }

    public function testParseDay(): void
    {
        $this->assertEquals('Monday', ChronoCasting::parseDay('Mon'));
        $this->assertEquals('Tuesday', ChronoCasting::parseDay('tue'));
        $this->assertEquals('Wednesday', ChronoCasting::parseDay('WED'));
        $this->assertEquals('Unknown', ChronoCasting::parseDay('Unknown'));
    }

    public function testAccordDateToTime(): void
    {
        $date = new \DateTime('2023-06-15 12:00:00');
        $newDate = ChronoCasting::accordDateToTime($date, '14:30');

        $this->assertEquals('2023-06-15 14:30:00', $newDate->format('Y-m-d H:i:s'));

        // Test with seconds
        $newDate = ChronoCasting::accordDateToTime($date, '14:30:45');
        $this->assertEquals('2023-06-15 14:30:45', $newDate->format('Y-m-d H:i:s'));

        // Test with invalid time format
        $this->expectException(\InvalidArgumentException::class);
        ChronoCasting::accordDateToTime($date, '25:00');
    }

    public function testIsRegularTime(): void
    {
        $this->assertTrue(ChronoCasting::isRegularTime('14:30'));
        $this->assertTrue(ChronoCasting::isRegularTime('14:30:45'));
        $this->assertFalse(ChronoCasting::isRegularTime('25:00'));
        $this->assertFalse(ChronoCasting::isRegularTime('14:60'));
        $this->assertFalse(ChronoCasting::isRegularTime('not-a-time'));
    }

    public function testDateFromInterface(): void
    {
        $dateTime = new \DateTimeImmutable('2023-06-15');
        $newDate = ChronoCasting::dateFromInterface($dateTime);

        $this->assertInstanceOf(\DateTime::class, $newDate);
        $this->assertEquals('2023-06-15', $newDate->format('Y-m-d'));
    }

    public function testInterfaceToDateTime(): void
    {
        // Test with DateTimeInterface
        $dateTime = new \DateTimeImmutable('2023-06-15');
        $newDate = ChronoCasting::interfaceToDateTime($dateTime);

        $this->assertInstanceOf(\DateTime::class, $newDate);
        $this->assertEquals('2023-06-15', $newDate->format('Y-m-d'));

        // Test with null
        $nullDate = ChronoCasting::interfaceToDateTime(null);
        $this->assertNull($nullDate);
    }
}
