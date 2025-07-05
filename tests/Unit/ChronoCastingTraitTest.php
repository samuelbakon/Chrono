<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use SamyAsm\Chrono\Traits\ChronoCastingTrait;

#[CoversClass(ChronoCastingTrait::class)]
class ChronoCastingTraitTest extends TestCase
{
    public function testToDateTime(): void
    {
        // Test with DateTime
        $dateTime = new DateTime('2023-06-15 14:30:45');
        $result = Chrono::toDateTime($dateTime);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:45', $result->format('Y-m-d H:i:s'));
        $this->assertNotSame($dateTime, $result, 'Should return a clone of the DateTime object');
        
        // Test with DateTimeImmutable
        $dateTimeImmutable = new DateTimeImmutable('2023-07-01 10:15:30');
        $result = Chrono::toDateTime($dateTimeImmutable);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-07-01 10:15:30', $result->format('Y-m-d H:i:s'));
        
        // Test with timestamp
        $timestamp = strtotime('2023-06-15 14:30:45');
        $result = Chrono::toDateTime($timestamp);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:45', $result->format('Y-m-d H:i:s'));
        
        // Test with date string
        $result = Chrono::toDateTime('2023-06-15 14:30:45');
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:45', $result->format('Y-m-d H:i:s'));
        
        // Test with invalid input
        $this->expectException(InvalidArgumentException::class);
        Chrono::toDateTime(['invalid']);
    }

    public function testDateFromInterface(): void
    {
        $dateTime = new DateTimeImmutable('2023-06-15');
        $newDate = DateTime::createFromInterface($dateTime);

        $this->assertInstanceOf(DateTime::class, $newDate);
        $this->assertEquals('2023-06-15', $newDate->format('Y-m-d'));
    }

    public function testTimestampToDateTime(): void
    {
        // Test avec un timestamp valide
        $timestamp = strtotime('2023-06-15 14:30:00');
        $date = Chrono::timestampToDateTime($timestamp);
        
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));
        
        // Test avec un timestamp invalide (négatif)
        $invalidDate = Chrono::timestampToDateTime(-1);
        $this->assertNull($invalidDate);
    }

    public function testFormatDateTime(): void
    {
        $date = new DateTime('2023-06-15 14:30:45');
        
        // Test avec le format par défaut
        $this->assertEquals('15-06-2023', Chrono::formatDateTime($date));
        
        // Test avec différents formats
        $this->assertEquals('2023/06/15 14:30:45', Chrono::formatDateTime($date, 'Y/m/d H:i:s'));
        $this->assertEquals('15 Jun 2023', Chrono::formatDateTime($date, 'd M Y'));
        $this->assertEquals('Thursday, June 15, 2023', Chrono::formatDateTime($date, 'l, F j, Y'));
        
        // Test avec un objet DateTimeImmutable
        $dateImmutable = new DateTimeImmutable('2023-07-01 10:15:30');
        $this->assertEquals('01-07-2023', Chrono::formatDateTime($dateImmutable));
        
        // Test avec un format vide
        $this->assertEquals('', Chrono::formatDateTime($date, ''));
    }

    public function testGetStartOfDay(): void
    {
        // Test avec un objet DateTime
        $date = new DateTime('2023-06-15 14:30:45');
        $startOfDay = Chrono::getStartOfDay($date);
        
        $this->assertEquals('2023-06-15 00:00:00', $startOfDay->format('Y-m-d H:i:s'));
        
        // Test avec une chaîne de date
        $startOfDay = Chrono::getStartOfDay('2023-06-15 14:30:45');
        $this->assertEquals('2023-06-15 00:00:00', $startOfDay->format('Y-m-d H:i:s'));
    }

    public function testGetEndOfDay(): void
    {
        // Test avec un objet DateTime
        $date = new DateTime('2023-06-15 14:30:45');
        $endOfDay = Chrono::getEndOfDay($date);
        
        $this->assertEquals('2023-06-15 23:59:59', $endOfDay->format('Y-m-d H:i:s'));
        
        // Test avec une chaîne de date
        $endOfDay = Chrono::getEndOfDay('2023-06-15 14:30:45');
        $this->assertEquals('2023-06-15 23:59:59', $endOfDay->format('Y-m-d H:i:s'));
    }

    public function testIsValidDateString(): void
    {
        // Test avec une date valide
        $this->assertTrue(Chrono::isValidDateString('2023-12-31'));
        
        // Test avec une date invalide
        $this->assertFalse(Chrono::isValidDateString('2023-02-30'));
        
        // Test avec un format personnalisé
        $this->assertTrue(Chrono::isValidDateString('31/12/2023', 'd/m/Y'));
        $this->assertFalse(Chrono::isValidDateString('31/12/2023')); // Échoue car le format par défaut est Y-m-d
    }

    public function testParseDay(): void
    {
        // Test avec des noms de jours complets
        $this->assertEquals(1, Chrono::parseDay('monday'));
        $this->assertEquals(2, Chrono::parseDay('tuesday'));
        $this->assertEquals(3, Chrono::parseDay('wednesday'));
        $this->assertEquals(4, Chrono::parseDay('thursday'));
        $this->assertEquals(5, Chrono::parseDay('friday'));
        $this->assertEquals(6, Chrono::parseDay('saturday'));
        $this->assertEquals(7, Chrono::parseDay('sunday'));
        
        // Test avec des abréviations
        $this->assertEquals(1, Chrono::parseDay('mon'));
        $this->assertEquals(2, Chrono::parseDay('tue'));
        
        // Test avec des numéros
        $this->assertEquals(1, Chrono::parseDay('1'));
        $this->assertEquals(7, Chrono::parseDay('7'));
        
        // Test avec un jour invalide
        $this->expectException(\InvalidArgumentException::class);
        Chrono::parseDay('invalid');
    }

    public function testSetTimeFromString(): void
    {
        $date = new DateTime('2023-06-15 12:00:00');
        
        // Test avec format HH:MM
        $newDate = Chrono::setTimeFromString($date, '14:30');
        $this->assertEquals('2023-06-15 14:30:00', $newDate->format('Y-m-d H:i:s'));
        
        // Test avec format HH:MM:SS
        $newDate = Chrono::setTimeFromString($date, '14:30:45');
        $this->assertEquals('2023-06-15 14:30:45', $newDate->format('Y-m-d H:i:s'));
        
        // Test avec une chaîne de date
        $newDate = Chrono::setTimeFromString('2023-06-15', '14:30');
        $this->assertEquals('2023-06-15 14:30:00', $newDate->format('Y-m-d H:i:s'));
        
        // Test avec un objet DateTimeImmutable
        $dateImmutable = new DateTimeImmutable('2023-06-15 12:00:00');
        $newDate = Chrono::setTimeFromString($dateImmutable, '15:45');
        $this->assertEquals('2023-06-15 15:45:00', $newDate->format('Y-m-d H:i:s'));
        
        // Test avec minuit
        $newDate = Chrono::setTimeFromString($date, '00:00');
        $this->assertEquals('2023-06-15 00:00:00', $newDate->format('Y-m-d H:i:s'));
        
        // Tests avec des formats invalides
        $invalidFormats = [
            '25:00',    // Heure invalide
            '14:60',    // Minutes invalides
            '14:30:60', // Secondes invalides
            'not-a-time',
            '14',
            '14:30:45:00' // Trop de parties
        ];
        
        foreach ($invalidFormats as $invalidFormat) {
            try {
                Chrono::setTimeFromString($date, $invalidFormat);
                $this->fail(sprintf('Expected exception for time format: %s', $invalidFormat));
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('Invalid time', $e->getMessage());
            }
        }
    }

    public function testIsValidTimeString(): void
    {
        // Tests avec des heures valides (format HH:MM)
        $this->assertTrue(Chrono::isValidTimeString('00:00'), '00:00 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('09:15'), '09:15 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('12:00'), '12:00 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('23:59'), '23:59 should be valid');
        
        // Tests avec des heures valides (format HH:MM:SS)
        $this->assertTrue(Chrono::isValidTimeString('00:00:00'), '00:00:00 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('09:15:30'), '09:15:30 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('12:00:00'), '12:00:00 should be valid');
        $this->assertTrue(Chrono::isValidTimeString('23:59:59'), '23:59:59 should be valid');
        
        // Tests avec des heures invalides (format)
        $this->assertFalse(Chrono::isValidTimeString('24:00'), '24:00 should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:60'), '14:60 should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:30:60'), '14:30:60 should be invalid');
        
        // Tests avec des formats invalides
        $this->assertFalse(Chrono::isValidTimeString(''), 'Empty string should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:30:45:00'), 'Too many parts should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14'), 'Incomplete time should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:'), 'Incomplete minutes should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:30:'), 'Incomplete seconds should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('not-a-time'), 'Non-numeric should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:30:45:00'), 'Too many parts should be invalid');
        
        // Tests avec des valeurs limites
        $this->assertFalse(Chrono::isValidTimeString('-1:00'), 'Negative hours should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:-1:00'), 'Negative minutes should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14:30:-1'), 'Negative seconds should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('24:01'), '24:01 should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('23:60'), '23:60 should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('23:59:60'), '23:59:60 should be invalid');
        
        // Test avec des espaces
        $this->assertFalse(Chrono::isValidTimeString(' 14:30 '), 'Spaces around should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14 :30'), 'Space after hours should be invalid');
        $this->assertFalse(Chrono::isValidTimeString('14: 30'), 'Space before minutes should be invalid');
    }

    public function testFromDateTimeInterface(): void
    {
        // Test avec un objet DateTime
        $dateTime = new DateTime('2023-06-15 14:30:45');
        $result = Chrono::fromDateTimeInterface($dateTime);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:45', $result->format('Y-m-d H:i:s'));
        $this->assertNotSame($dateTime, $result, 'Should return a clone of the DateTime object');
        
        // Test avec un objet DateTimeImmutable
        $dateTimeImmutable = new DateTimeImmutable('2023-07-01 10:15:30');
        $result = Chrono::fromDateTimeInterface($dateTimeImmutable);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-07-01 10:15:30', $result->format('Y-m-d H:i:s'));
    }

    public function testToDateTimeFromInterface(): void
    {
        // Test avec un objet DateTime
        $dateTime = new DateTime('2023-06-15 14:30:45');
        $result = Chrono::toDateTimeFromInterface($dateTime);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:45', $result->format('Y-m-d H:i:s'));
        $this->assertNotSame($dateTime, $result, 'Should return a clone of the DateTime object');
        
        // Test avec un objet DateTimeImmutable
        $dateTimeImmutable = new DateTimeImmutable('2023-07-01 10:15:30');
        $result = Chrono::toDateTimeFromInterface($dateTimeImmutable);
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-07-01 10:15:30', $result->format('Y-m-d H:i:s'));
        
        // Test avec null
        $this->assertNull(Chrono::toDateTimeFromInterface(null));
    }
}
