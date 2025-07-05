<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use SamyAsm\Chrono\Traits\ChronoComputingTrait;
use DateTime;
use DateTimeZone;
use DateInterval;

#[CoversTrait(ChronoComputingTrait::class)]

class ChronoComputingTraitTest extends TestCase
{
    public function testAddDaysToDate()
    {
        // Test avec un objet DateTime
        $date = new DateTime('2023-01-01');
        $result = Chrono::addDaysToDate($date, 5);
        $this->assertEquals('2023-01-06', $result->format('Y-m-d'));
        
        // Test avec une chaîne de date
        $result = Chrono::addDaysToDate('2023-01-01', 5);
        $this->assertEquals('2023-01-06', $result->format('Y-m-d'));
    }

    public function testAddHoursToDate()
    {
        $date = new DateTime('2023-01-01 10:00:00');
        $result = Chrono::addHoursToDate($date, 3);
        $this->assertEquals('2023-01-01 13:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function testAddMinutesToDate()
    {
        $date = new DateTime('2023-01-01 10:00:00');
        $result = Chrono::addMinutesToDate($date, 30);
        $this->assertEquals('2023-01-01 10:30:00', $result->format('Y-m-d H:i:s'));
    }

    public function testAddSecondsToDate()
    {
        $date = new DateTime('2023-01-01 10:00:00');
        $result = Chrono::addSecondsToDate($date, 90);
        $this->assertEquals('2023-01-01 10:01:30', $result->format('Y-m-d H:i:s'));
    }

    public function testSubtractDaysFromDate()
    {
        $date = new DateTime('2023-01-10');
        $result = Chrono::subtractDaysFromDate($date, 5);
        $this->assertEquals('2023-01-05', $result->format('Y-m-d'));
    }

    public function testSubtractHoursFromDate()
    {
        $date = new DateTime('2023-01-01 10:00:00');
        $result = Chrono::subtractHoursFromDate($date, 3);
        $this->assertEquals('2023-01-01 07:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function testSubtractMinutesToDate()
    {
        $date = new DateTime('2023-01-01 10:30:00');
        $result = Chrono::subtractMinutesToDate($date, 30);
        $this->assertEquals('2023-01-01 10:00:00', $result->format('Y-m-d H:i:s'));
    }

    public function testAddTimeToDate()
    {
        $date = new DateTime('2023-01-01 10:00:00');
        // Utiliser une chaîne de temps au lieu de DateInterval
        $time = '02:30:00';
        $result = Chrono::addTimeToDate($date, $time);
        $this->assertEquals('2023-01-01 12:30:00', $result->format('Y-m-d H:i:s'));
    }

    public function testGetDateDayDif()
    {
        $date1 = new DateTime('2023-01-01');
        $date2 = new DateTime('2023-01-10');
        $result = Chrono::getDateDayDif($date1, $date2);
        $this->assertEquals(9, $result);
    }

    public function testGetMinuteDateDif()
    {
        $date1 = new DateTime('2023-01-01 10:00:00');
        $date2 = new DateTime('2023-01-01 11:30:00');
        
        // La méthode retourne une valeur négative si $date2 est après $date1
        $result = Chrono::getMinuteDateDif($date1, $date2);
        $this->assertEquals(-90, $result);
        
        // Tester dans l'autre sens
        $result = Chrono::getMinuteDateDif($date2, $date1);
        $this->assertEquals(90, $result);
    }

    public function testGetSecondsDateDif()
    {
        $date1 = new DateTime('2023-01-01 10:00:00');
        $date2 = new DateTime('2023-01-01 10:01:30');
        $result = Chrono::getSecondsDateDif($date1, $date2);
        $this->assertEquals(90, $result);
    }

    public function testGetRemainingDays()
    {
        $futureDate = new DateTime('2023-01-10');
        $currentDate = new DateTime('2023-01-01');
        
        // La méthode retourne une valeur négative pour les dates futures
        $result = Chrono::getRemainingDays($futureDate, $currentDate);
        $this->assertEquals(-9, $result);
        
        // Tester avec une date dans le passé
        $result = Chrono::getRemainingDays($currentDate, $futureDate);
        $this->assertEquals(9, $result);
    }

    public function testGetRemainingDay()
    {
        $futureDate = new DateTime('2023-01-10');
        $currentDate = new DateTime('2023-01-01');
        
        // La méthode est un alias de getRemainingDays, même comportement
        $result = Chrono::getRemainingDay($futureDate, $currentDate);
        $this->assertEquals(-9, $result);
        
        // Tester avec une date dans le passé
        $result = Chrono::getRemainingDay($currentDate, $futureDate);
        $this->assertEquals(9, $result);
    }

    public function testConvertDaysToMinutes()
    {
        $result = Chrono::convertDaysToMinutes(1);
        $this->assertEquals(1440, $result); // 24 * 60 = 1440 minutes
    }

    public function testConvertHoursToMinutes()
    {
        $result = Chrono::convertHoursToMinutes(2);
        $this->assertEquals(120, $result); // 2 * 60 = 120 minutes
    }

    public function testConvertMinutesToHours()
    {
        $result = Chrono::convertMinutesToHours(120);
        $this->assertEquals(2.0, $result); // 120 / 60 = 2.0 hours
    }

    public function testGetTimeAgo()
    {
        $now = new DateTime();
        
        // Test pour "Just now"
        $date = clone $now;
        $date->modify('-30 seconds');
        $result = Chrono::getTimeAgo($date);
        $this->assertEquals('Just now', $result);
        
        // Test pour les minutes
        $date = clone $now;
        $date->modify('-5 minutes');
        $result = Chrono::getTimeAgo($date);
        $this->assertStringContainsString('minute', $result);
        $this->assertStringContainsString('5', $result);
        
        // Test pour les heures
        $date = clone $now;
        $date->modify('-2 hours');
        $result = Chrono::getTimeAgo($date);
        $this->assertStringContainsString('hour', $result);
        $this->assertStringContainsString('2', $result);
        
        // Test pour les jours
        $date = clone $now;
        $date->modify('-3 days');
        $result = Chrono::getTimeAgo($date);
        $this->assertStringContainsString('day', $result);
        $this->assertStringContainsString('3', $result);
    }

    public function testLastSeenHelp()
    {
        $now = new DateTime();
        $date = clone $now;
        $date->modify('-5 minutes');
        
        $result = Chrono::lastSeenHelp($date);
        $this->assertStringContainsString('minute', $result);
        $this->assertStringContainsString('5', $result);
        
        // Vérifie que c'est bien un alias de getTimeAgo
        $this->assertEquals(Chrono::getTimeAgo($date), $result);
    }
}
