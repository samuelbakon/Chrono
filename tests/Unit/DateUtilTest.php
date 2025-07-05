<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use SamyAsm\Chrono\Traits\ChronoUtilsTrait;
use SamyAsm\Chrono\Traits\ChronoCalendarTrait;
use SamyAsm\Chrono\Traits\ChronoComputingTrait;
use SamyAsm\Chrono\Traits\ChronoFactoryTrait;
use SamyAsm\Chrono\Traits\ChronoFormatTrait;
use SamyAsm\Chrono\Traits\ChronoCastingTrait;

#[CoversClass(ChronoUtilsTrait::class)]
#[CoversClass(ChronoCalendarTrait::class)]
#[CoversClass(ChronoComputingTrait::class)]
#[CoversClass(ChronoFactoryTrait::class)]
#[CoversClass(ChronoFormatTrait::class)]
#[CoversClass(ChronoCastingTrait::class)]
class DateUtilTest extends TestCase
{
    public function testGetFirstDayOfTheWeekFromDate(): void
    {
        $date = new DateTime('2023-06-15'); // Un jeudi
        $firstDay = Chrono::getFirstDayOfWeek($date);
        
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Devrait être le lundi de la même semaine
    }

    public function testAddDaysToDate(): void
    {
        $date = new DateTime('2023-06-15 14:30:00');
        $newDate = Chrono::addDaysToDate($date, 5);
        
        $this->assertEquals('2023-06-20 14:30:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testFormatDateDay(): void
    {
        $date = new DateTime('2023-06-15');
        $formattedDate = Chrono::formatStatic($date, 'd/m/Y');
        
        $this->assertEquals('15/06/2023', $formattedDate);
    }

    public function testIsValidDate(): void
    {
        // La méthode vérifie si la chaîne correspond au format spécifié
        $this->assertTrue(Chrono::isValidDateString('2023-12-31')); // Date valide
        $this->assertFalse(Chrono::isValidDateString('2023-02-30')); // Date invalide (février n'a pas 30 jours)
        $this->assertFalse(Chrono::isValidDateString('')); // Chaîne vide
        $this->assertFalse(Chrono::isValidDateString('not-a-date')); // Format invalide
    }

    public function testGetMinuteDateDif(): void
    {
        $date1 = new DateTime('2023-06-15 10:00:00');
        $date2 = new DateTime('2023-06-15 14:30:00');
        
        // La méthode retourne une valeur négative quand la première date est avant la deuxième
        $minutesDiff = Chrono::getMinuteDateDif($date1, $date2);
        $this->assertEquals(-270, $minutesDiff); // 4h30 = 270 minutes, négatif car date1 < date2
        
        // Tester dans l'autre sens
        $minutesDiff = Chrono::getMinuteDateDif($date2, $date1);
        $this->assertEquals(270, $minutesDiff); // 4h30 = 270 minutes, positif car date2 > date1
    }

    public function testGetDateFromZero(): void
    {
        // Tester avec une chaîne de date
        $dateFromZero = Chrono::create('2023-06-15 00:00:00');
        $this->assertInstanceOf(DateTime::class, $dateFromZero);
        $this->assertEquals('2023-06-15 00:00:00', $dateFromZero->format('Y-m-d H:i:s'));
    }

    public function testGetDateAtEnd(): void
    {
        // Tester avec une chaîne de date
        $dateAtEnd = Chrono::create('2023-06-16 00:00:00');
        $this->assertInstanceOf(DateTime::class, $dateAtEnd);
        $this->assertEquals('2023-06-16 00:00:00', $dateAtEnd->format('Y-m-d H:i:s'));
    }

    public function testGetDayOfWeek(): void
    {
        // Tester avec un objet DateTime
        $date = new DateTime('2023-06-15'); // Un jeudi
        $dayOfWeek = Chrono::getWeekday($date);
        $this->assertEquals(4, $dayOfWeek); // 4 = jeudi (1 = lundi, 7 = dimanche)
        
        // Tester un autre jour de la semaine
        $date = new DateTime('2023-06-16'); // Un vendredi
        $dayOfWeek = Chrono::getWeekday($date);
        $this->assertEquals(5, $dayOfWeek); // 5 = vendredi
    }

    public function testGetWeekDayOfDate(): void
    {
        // La méthode utilise format('D') qui retourne le jour en 3 lettres en anglais
        // Vérifions que la conversion fonctionne pour tous les jours
        $days = [
            '2023-06-11' => 7, // dimanche
            '2023-06-12' => 1, // lundi
            '2023-06-13' => 2, // mardi
            '2023-06-14' => 3, // mercredi
            '2023-06-15' => 4, // jeudi
            '2023-06-16' => 5, // vendredi
            '2023-06-17' => 6, // samedi
        ];
        
        foreach ($days as $dateStr => $expectedDay) {
            $date = new DateTime($dateStr);
            $this->assertEquals($expectedDay, Chrono::getWeekday($date));
        }
    }

    public function testGetMonthFromPosition(): void
    {
        // Tester les mois valides
        $months = [
            1 => 'JAN',
            6 => 'JUN',
            12 => 'DEC'
        ];
        
        foreach ($months as $position => $expected) {
            $this->assertEquals($expected, Chrono::getMonthFromPosition($position));
        }
        
        // Tester une position invalide
        $this->assertEquals('UNK', Chrono::getMonthFromPosition(13));
    }

    public function testGetMonthday(): void
    {
        // Tester avec une date (mode 0 pour obtenir le jour du mois)
        $date = new DateTime('2023-06-15');
        $this->assertEquals(15, Chrono::getMonthday($date->format('Y-m-d'), 0));
        
        // Tester avec une chaîne de date (mode 0 pour obtenir le jour du mois)
        $this->assertEquals(25, Chrono::getMonthday('2023-12-25', 0));
        
        // Tester avec mode 1 pour obtenir le nom du mois
        $date = new DateTime('2023-06-15');
        $this->assertEquals('JUN', Chrono::getMonthName($date->format('Y-m-d')));
        
        // Tester avec une chaîne de date et mode 1
        $this->assertEquals('DEC', Chrono::getMonthName('2023-12-25'));
    }
}
