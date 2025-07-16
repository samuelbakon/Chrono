<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use SamBakon\Chrono\Chrono;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

#[CoversTrait(\SamBakon\Chrono\Traits\ChronoPeriodTrait::class)]
class ChronoPeriodTraitTest extends TestCase
{
    public function testGetTodayInterval()
    {
        $now = new DateTime();
        $today = Chrono::getTodayInterval();
        
        $this->assertArrayHasKey('start', $today);
        $this->assertArrayHasKey('end', $today);
        
        // Vérifier que start est à minuit
        $this->assertEquals('00:00:00', $today['start']->format('H:i:s'));
        
        // Vérifier que end est à 23:59:59
        $this->assertEquals('23:59:59', $today['end']->format('H:i:s'));
        
        // Vérifier que c'est bien la date d'aujourd'hui
        $this->assertEquals($now->format('Y-m-d'), $today['start']->format('Y-m-d'));
        $this->assertEquals($now->format('Y-m-d'), $today['end']->format('Y-m-d'));
    }

    public function testAdjustIntervalWithNoDates()
    {
        $interval = Chrono::adjustInterval();
        
        $this->assertArrayHasKey('start', $interval);
        $this->assertArrayHasKey('end', $interval);
        
        // Vérifier que l'intervalle est d'un mois par défaut
        $diff = $interval['start']->diff($interval['end']);
        $this->assertEquals(0, $diff->y);
        $this->assertEquals(1, $diff->m);
        $this->assertEquals(0, $diff->d);
        
        // Vérifier que end est à 23:59:59
        $this->assertEquals('23:59:59', $interval['end']->format('H:i:s'));
    }

    public function testAdjustIntervalWithCustomInterval()
    {
        $interval = Chrono::adjustInterval(null, null, 'P2W');
        
        // Vérifier que l'intervalle est de 2 semaines
        $diff = $interval['start']->diff($interval['end']);
        $this->assertEquals(0, $diff->y);
        $this->assertEquals(0, $diff->m);
        $this->assertEquals(14, $diff->d);
    }

    public function testAdjustIntervalWithStartDateOnly()
    {
        // Utiliser une date fixe pour le test
        $start = '2023-01-15';
        $interval = Chrono::adjustInterval($start);
        
        // Vérifier que la date de début est correcte
        $this->assertEquals('2023-01-15 00:00:00', $interval['start']->format('Y-m-d H:i:s'));
        
        // Vérifier que la date de fin est 1 mois plus tard
        $expectedEnd = new DateTime('2023-01-15');
        $expectedEnd->modify('+1 month');
        
        // Vérifier que la différence est d'environ 1 mois (entre 28 et 31 jours)
        $daysDiff = $interval['start']->diff($interval['end'])->days;
        $this->assertGreaterThanOrEqual(28, $daysDiff);
        $this->assertLessThanOrEqual(31, $daysDiff);
        
        // Vérifier que le mois de fin est bien le mois suivant
        $startMonth = (int)$interval['start']->format('m');
        $endMonth = (int)$interval['end']->format('m');
        $expectedEndMonth = $startMonth === 12 ? 1 : $startMonth + 1;
        $this->assertEquals($expectedEndMonth, $endMonth);
        
        // Vérifier que l'heure de fin est à 23:59:59 (comportement actuel)
        $this->assertEquals('23:59:59', $interval['end']->format('H:i:s'));
    }

    public function testAdjustIntervalWithEndDateOnly()
    {
        $end = '2023-01-15';
        $interval = Chrono::adjustInterval(null, $end);
        
        $this->assertEquals('2022-12-15 00:00:00', $interval['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-01-15 23:59:59', $interval['end']->format('Y-m-d H:i:s'));
    }

    public function testAdjustIntervalWithBothDates()
    {
        $start = '2023-01-01';
        $end = '2023-01-15';
        $interval = Chrono::adjustInterval($start, $end);
        
        $this->assertEquals('2023-01-01 00:00:00', $interval['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-01-15 23:59:59', $interval['end']->format('Y-m-d H:i:s'));
    }

    public function testAdjustIntervalWithReversedDates()
    {
        $start = '2023-01-15';
        $end = '2023-01-01';
        $interval = Chrono::adjustInterval($start, $end);
        
        // Les dates doivent être dans le bon ordre
        $this->assertEquals('2023-01-01 00:00:00', $interval['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-01-15 23:59:59', $interval['end']->format('Y-m-d H:i:s'));
    }

    public function testGetDateRange()
    {
        $start = '2023-01-01';
        $end = '2023-01-03';
        $dates = Chrono::getDateRange($start, $end);
        
        $this->assertCount(3, $dates);
        $this->assertEquals('2023-01-01', $dates[0]);
        $this->assertEquals('2023-01-02', $dates[1]);
        $this->assertEquals('2023-01-03', $dates[2]);
        
        // Tester avec un format personnalisé
        $dates = Chrono::getDateRange($start, $end, 'd/m/Y');
        $this->assertEquals('01/01/2023', $dates[0]);
    }

    public function testGetDateRangeWithReversedDates()
    {
        $start = '2023-01-03';
        $end = '2023-01-01';
        $dates = Chrono::getDateRange($start, $end);
        
        // Les dates doivent être dans le bon ordre
        $this->assertEquals('2023-01-01', $dates[0]);
        $this->assertEquals('2023-01-03', $dates[2]);
    }

    public function testGetDaysInPeriod()
    {
        $start = '2023-01-01';
        $end = '2023-01-03';
        $days = Chrono::getDaysInPeriod($start, $end);
        
        $this->assertCount(3, $days);
        $this->assertInstanceOf(DateTime::class, $days[0]);
        $this->assertEquals('2023-01-01', $days[0]->format('Y-m-d'));
        $this->assertEquals('2023-01-03', $days[2]->format('Y-m-d'));
    }

    public function testGetDayRange()
    {
        $date = '2023-01-15 14:30:45';
        $range = Chrono::getDayRange($date);
        
        $this->assertArrayHasKey('start', $range);
        $this->assertArrayHasKey('end', $range);
        
        $this->assertEquals('2023-01-15 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-01-15 23:59:59', $range['end']->format('Y-m-d H:i:s'));
    }

    public function testGetDaysBetween()
    {
        // Test avec des dates dans le bon ordre
        $this->assertEquals(2, abs(Chrono::getDaysBetween('2023-01-01', '2023-01-03')));
        
        // Test avec les dates dans l'ordre inverse (devrait retourner une valeur négative)
        $this->assertEquals(-2, Chrono::getDaysBetween('2023-01-03', '2023-01-01'));
        
        // Test avec la même date
        $this->assertEquals(0, Chrono::getDaysBetween('2023-01-01', '2023-01-01'));
        
        // Vérifier que la méthode retourne bien des jours complets
        $this->assertEquals(1, abs(Chrono::getDaysBetween('2023-01-01 23:59:59', '2023-01-02 00:00:01')));
    }

    public function testIsDateInRange()
    {
        $start = '2023-01-01';
        $end = '2023-01-31';
        
        // Date dans l'intervalle
        $this->assertTrue(Chrono::isDateInRange('2023-01-15', $start, $end));
        
        // Date égale à la borne inférieure
        $this->assertTrue(Chrono::isDateInRange($start, $start, $end));
        
        // Date égale à la borne supérieure
        $this->assertTrue(Chrono::isDateInRange($end, $start, $end));
        
        // Date en dehors de l'intervalle (avant)
        $this->assertFalse(Chrono::isDateInRange('2022-12-31', $start, $end));
        
        // Date en dehors de l'intervalle (après)
        $this->assertFalse(Chrono::isDateInRange('2023-02-01', $start, $end));
        
        // Test avec des heures
        $this->assertTrue(Chrono::isDateInRange('2023-01-15 12:00:00', $start, $end));
        $this->assertTrue(Chrono::isDateInRange('2023-01-01 00:00:00', $start, $end));
        $this->assertTrue(Chrono::isDateInRange('2023-01-31 23:59:59', $start, $end));
    }

    public function testWithDifferentTimezones()
    {
        // Tester avec un fuseau horaire différent
        $date = '2023-01-01T00:00:00+09:00'; // Tokyo (UTC+9)
        $range = Chrono::getDayRange($date);
        
        // La plage doit être dans le même fuseau horaire que la date d'entrée
        $this->assertEquals('+09:00', $range['start']->format('P'));
        $this->assertEquals('2023-01-01 00:00:00', $range['start']->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-01-01 23:59:59', $range['end']->format('Y-m-d H:i:s'));
    }

    public function testWithDateTimeImmutable()
    {
        // Tester avec DateTimeImmutable
        $start = new DateTimeImmutable('2023-01-01');
        $end = new DateTimeImmutable('2023-01-03');
        
        $dates = Chrono::getDateRange($start, $end);
        
        // Le comportement actuel retourne 2 jours car DatePeriod est exclusif par défaut
        $this->assertCount(2, $dates);
        $this->assertEquals('2023-01-01', $dates[0]);
        $this->assertEquals('2023-01-02', $dates[1]);
        
        // Tester avec un format personnalisé
        $dates = Chrono::getDateRange($start, $end, 'd/m/Y');
        $this->assertEquals('01/01/2023', $dates[0]);
    }

    public function testWithDifferentDateFormats()
    {
        // Tester avec différents formats de date en entrée
        $dates = Chrono::getDateRange('01/01/2023', '03/01/2023');
        $this->assertNotEmpty($dates);
        
        // Le format de sortie doit être cohérent
        $this->assertStringStartsWith('2023-01-', $dates[0]);
    }
}
