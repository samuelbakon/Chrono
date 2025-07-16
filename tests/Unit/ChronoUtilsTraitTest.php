<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamBakon\Chrono\Chrono;
use SamBakon\Chrono\Traits\ChronoUtilsTrait;
use SamBakon\Chrono\Traits\ChronoCastingTrait;

#[CoversClass(ChronoUtilsTrait::class)]
#[CoversClass(ChronoCastingTrait::class)]
class ChronoUtilsTraitTest extends TestCase
{
    public function testIsValidDate(): void
    {
        $this->assertTrue(Chrono::validateDate('2023-12-31'));
        $this->assertTrue(Chrono::validateDate('31-12-2023', 'd-m-Y'));
        $this->assertFalse(Chrono::validateDate('2023-02-30')); // Invalid date
        $this->assertFalse(Chrono::validateDate('not-a-date'));
    }

    public function testParseDay(): void
    {
        $this->assertEquals(1, Chrono::parseDay('Mon'));
        $this->assertEquals(2, Chrono::parseDay('tue'));
        $this->assertEquals(3, Chrono::parseDay('WED'));
        
        // Test avec un jour invalide (doit lancer une exception)
        $this->expectException(\InvalidArgumentException::class);
        Chrono::parseDay('Unknown');
    }

    public function testSetTimeFromString(): void
    {
        $date = new \DateTime('2023-06-15 12:00:00');
        $newDate = Chrono::setTimeFromString($date, '14:30');

        $this->assertEquals('2023-06-15 14:30:00', $newDate->format('Y-m-d H:i:s'));

        // Test with seconds
        $newDate = Chrono::setTimeFromString($date, '14:30:45');
        $this->assertEquals('2023-06-15 14:30:45', $newDate->format('Y-m-d H:i:s'));

        // Test with invalid time format
        $this->expectException(\InvalidArgumentException::class);
        Chrono::setTimeFromString($date, '25:00');
    }

    public function testIsValidDateString(): void
    {
        // La méthode vérifie si la chaîne correspond au format spécifié
        $this->assertTrue(Chrono::isValidDateString('2023-12-31')); // Date valide
        $this->assertFalse(Chrono::isValidDateString('2023-02-30')); // Date invalide (février n'a pas 30 jours)
        $this->assertFalse(Chrono::isValidDateString('')); // Chaîne vide
        $this->assertFalse(Chrono::isValidDateString('not-a-date')); // Format invalide
    }

    public function testIsValidTimeString(): void
    {
        $this->assertTrue(Chrono::isValidTimeString('14:30'));
        $this->assertTrue(Chrono::isValidTimeString('14:30:45'));
        $this->assertFalse(Chrono::isValidTimeString('25:00'));
        $this->assertFalse(Chrono::isValidTimeString('14:60'));
        $this->assertFalse(Chrono::isValidTimeString('not-a-time'));
    }

    public function testGetMonthNameFromPosition(): void
    {
        // Test des mois valides
        $this->assertEquals('JAN', Chrono::getMonthNameFromPosition(1));
        $this->assertEquals('FEB', Chrono::getMonthNameFromPosition(2));
        $this->assertEquals('MAR', Chrono::getMonthNameFromPosition(3));
        $this->assertEquals('APR', Chrono::getMonthNameFromPosition(4));
        $this->assertEquals('MAY', Chrono::getMonthNameFromPosition(5));
        $this->assertEquals('JUN', Chrono::getMonthNameFromPosition(6));
        $this->assertEquals('JUL', Chrono::getMonthNameFromPosition(7));
        $this->assertEquals('AUG', Chrono::getMonthNameFromPosition(8));
        $this->assertEquals('SEP', Chrono::getMonthNameFromPosition(9));
        $this->assertEquals('OCT', Chrono::getMonthNameFromPosition(10));
        $this->assertEquals('NOV', Chrono::getMonthNameFromPosition(11));
        $this->assertEquals('DEC', Chrono::getMonthNameFromPosition(12));
        
        // Test des positions invalides
        $this->assertEquals('UNK', Chrono::getMonthNameFromPosition(0));
        $this->assertEquals('UNK', Chrono::getMonthNameFromPosition(13));
        $this->assertEquals('UNK', Chrono::getMonthNameFromPosition(-1));
    }

    public function testGetMonthFromPosition(): void
    {
        // Vérifie que getMonthFromPosition est un alias de getMonthNameFromPosition
        $this->assertEquals(Chrono::getMonthNameFromPosition(1), Chrono::getMonthFromPosition(1));
        $this->assertEquals(Chrono::getMonthNameFromPosition(12), Chrono::getMonthFromPosition(12));
        $this->assertEquals(Chrono::getMonthNameFromPosition(0), Chrono::getMonthFromPosition(0));
    }

    public function testGetDaysInMonth(): void
    {
        // Test avec une année non bissextile
        $this->assertEquals(31, Chrono::getDaysInMonth(1)); // Janvier
        $this->assertEquals(28, Chrono::getDaysInMonth(2)); // Février (non bissextile)
        $this->assertEquals(31, Chrono::getDaysInMonth(3)); // Mars
        $this->assertEquals(30, Chrono::getDaysInMonth(4)); // Avril
        $this->assertEquals(31, Chrono::getDaysInMonth(5)); // Mai
        $this->assertEquals(30, Chrono::getDaysInMonth(6)); // Juin
        $this->assertEquals(31, Chrono::getDaysInMonth(7)); // Juillet
        $this->assertEquals(31, Chrono::getDaysInMonth(8)); // Août
        $this->assertEquals(30, Chrono::getDaysInMonth(9)); // Septembre
        $this->assertEquals(31, Chrono::getDaysInMonth(10)); // Octobre
        $this->assertEquals(30, Chrono::getDaysInMonth(11)); // Novembre
        $this->assertEquals(31, Chrono::getDaysInMonth(12)); // Décembre
        
        // Test avec une année bissextile (2024)
        $this->assertEquals(29, Chrono::getDaysInMonth(2, 2024)); // Février bissextile
        
        // Test avec l'année courante
        $currentYear = (int) date('Y');
        $expectedDays = cal_days_in_month(CAL_GREGORIAN, 2, $currentYear);
        $this->assertEquals($expectedDays, Chrono::getDaysInMonth(2, $currentYear));
    }
}
