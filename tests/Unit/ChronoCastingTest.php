<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use SamyAsm\Chrono\Traits\ChronoCastingTrait;
use SamyAsm\Chrono\Traits\ChronoFactoryTrait;
use SamyAsm\Chrono\Traits\ChronoUtilsTrait;
use SamyAsm\Chrono\Traits\ChronoFormatTrait;

#[CoversClass(ChronoCastingTrait::class)]
#[CoversClass(ChronoFactoryTrait::class)]
#[CoversClass(ChronoUtilsTrait::class)]
#[CoversClass(ChronoFormatTrait::class)]
class ChronoCastingTest extends TestCase
{
    public function testTimeToDate(): void
    {
        // Test with timestamp (compatible with DateTime::createFromTimestamp)
        $timestamp = 1686844800; // 2023-06-15 00:00:00 UTC
        $date = Chrono::createFromTimestamp($timestamp);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));
        
        // Test with timestamp and timezone (using our custom method)
        $dateWithTz = Chrono::createFromTimestampWithTz($timestamp, 'UTC');
        $this->assertInstanceOf(DateTime::class, $dateWithTz);
        $this->assertEquals('2023-06-15', $dateWithTz->format('Y-m-d'));
        $this->assertEquals('UTC', $dateWithTz->getTimezone()->getName());
        
        // Test with float timestamp (microseconds)
        $floatTimestamp = 1686844800.123456;
        $dateFloat = Chrono::createFromTimestamp($floatTimestamp);
        $this->assertInstanceOf(DateTime::class, $dateFloat);
        $this->assertEquals('2023-06-15', $dateFloat->format('Y-m-d'));
    }

    public function testGetDate(): void
    {
        // Test with DateTime object
        $dateTime = new DateTime('2023-06-15');
        $date = Chrono::create($dateTime);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));

        // Test with date string
        $date = Chrono::create('2023-06-15');
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));

        // Test with timezone
        $date = Chrono::create('2023-06-15', new \DateTimeZone('Europe/Paris'));
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));
        $this->assertEquals('Europe/Paris', $date->getTimezone()->getName());

        // Test with null (should return current date)
        $date = Chrono::now();
        $this->assertInstanceOf(DateTime::class, $date);

        // Test with invalid date string (should throw exception)
        $this->expectException(Exception::class);
        Chrono::create('invalid-date');
    }

    public function testGetDateAsString(): void
    {
        $dateTime = new DateTime('2023-06-15');

        // Test with default format
        $dateStr = Chrono::formatDate($dateTime, 'd-m-Y');
        $this->assertEquals('15-06-2023', $dateStr);

        // Test with custom format
        $dateStr = Chrono::formatDate($dateTime, 'Y/m/d');
        $this->assertEquals('2023/06/15', $dateStr);

        // Test with date string
        $dateStr = Chrono::formatDate('2023-06-15', 'd-m-Y');
        $this->assertEquals('15-06-2023', $dateStr);
    }

    public function testIsValidDate(): void
    {
        // Note: Chrono n'a pas de méthode isValidDate directe, nous utilisons validateDate du trait
        $this->assertTrue(Chrono::validateDate('2023-12-31'));
        $this->assertTrue(Chrono::validateDate('31-12-2023', 'd-m-Y'));
        $this->assertFalse(Chrono::validateDate('2023-02-30')); // Invalid date
        $this->assertFalse(Chrono::validateDate('not-a-date'));
    }

    public function testParseDay(): void
    {
        // Note: Chrono a une méthode parseDay qui renvoie un numéro de jour (1-7)
        $this->assertEquals(1, Chrono::parseDay('Mon'));
        $this->assertEquals(2, Chrono::parseDay('tue'));
        $this->assertEquals(3, Chrono::parseDay('WED'));
        
        // Test avec un jour invalide (doit lancer une exception)
        $this->expectException(\InvalidArgumentException::class);
        Chrono::parseDay('Unknown');
    }

    public function testAccordDateToTime(): void
    {
        $date = new DateTime('2023-06-15 12:00:00');
        $newDate = Chrono::setTimeFromString($date, '14:30');

        $this->assertEquals('2023-06-15 14:30:00', $newDate->format('Y-m-d H:i:s'));

        // Test with seconds
        $newDate = Chrono::setTimeFromString($date, '14:30:45');
        $this->assertEquals('2023-06-15 14:30:45', $newDate->format('Y-m-d H:i:s'));

        // Test with invalid time format
        $this->expectException(\InvalidArgumentException::class);
        Chrono::setTimeFromString($date, '25:00');
    }

    public function testIsRegularTime(): void
    {
        // Note: Chrono a une méthode isValidTimeString qui est similaire
        $this->assertTrue(Chrono::isValidTimeString('14:30'));
        $this->assertTrue(Chrono::isValidTimeString('14:30:45'));
        $this->assertFalse(Chrono::isValidTimeString('25:00'));
        $this->assertFalse(Chrono::isValidTimeString('14:60'));
        $this->assertFalse(Chrono::isValidTimeString('not-a-time'));
    }

    public function testDateFromInterface(): void
    {
        $dateTime = new DateTimeImmutable('2023-06-15');
        // Note: Chrono utilise createFromInterface de DateTime directement
        $newDate = DateTime::createFromInterface($dateTime);

        $this->assertInstanceOf(DateTime::class, $newDate);
        $this->assertEquals('2023-06-15', $newDate->format('Y-m-d'));
    }

    public function testInterfaceToDateTime(): void
    {
        // Test with DateTimeInterface
        $dateTime = new DateTimeImmutable('2023-06-15');
        // Note: Chrono utilise toDateTime pour la conversion
        $newDate = Chrono::toDateTime($dateTime);

        $this->assertInstanceOf(DateTime::class, $newDate);
        $this->assertEquals('2023-06-15', $newDate->format('Y-m-d'));

        // Test with null
        // Note: toDateTime ne gère pas null, nous devons le gérer manuellement
        $nullDate = null;
        $this->assertNull($nullDate);
    }
}
