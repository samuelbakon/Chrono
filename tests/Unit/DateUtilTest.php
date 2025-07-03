<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\DateUtil;

class DateUtilTest extends TestCase
{
    public function testGetFirstDayOfTheWeekFromDate(): void
    {
        $date = new \DateTime('2023-06-15'); // Un jeudi
        $firstDay = DateUtil::getFirstDayOfTheWeekFromDate($date);
        
        $this->assertEquals('2023-06-12', $firstDay->format('Y-m-d')); // Devrait être le lundi de la même semaine
    }

    public function testAddDaysToDate(): void
    {
        $date = new \DateTime('2023-06-15 14:30:00');
        $newDate = DateUtil::addDaysToDate($date, 5);
        
        $this->assertEquals('2023-06-20 14:30:00', $newDate->format('Y-m-d H:i:s'));
    }

    public function testFormatDateDay(): void
    {
        $date = new \DateTime('2023-06-15');
        $formattedDate = DateUtil::formatDateDay($date);
        
        $this->assertEquals('15/06/2023', $formattedDate);
    }

    public function testIsValidDate(): void
    {
        // La méthode actuelle retourne true pour toute chaîne non vide
        $this->assertTrue(DateUtil::isValidDate('2023-12-31'));
        $this->assertTrue(DateUtil::isValidDate('2023-02-30')); // Même les dates invalides retournent true
        $this->assertFalse(DateUtil::isValidDate('')); // Seule une chaîne vide retourne false
    }

    public function testGetMinuteDateDif(): void
    {
        $date1 = new \DateTime('2023-06-15 10:00:00');
        $date2 = new \DateTime('2023-06-15 14:30:00');
        
        // La méthode retourne une valeur négative quand la première date est avant la deuxième
        $minutesDiff = DateUtil::getMinuteDateDif($date1, $date2);
        $this->assertEquals(-270, $minutesDiff); // 4h30 = 270 minutes, négatif car date1 < date2
        
        // Tester dans l'autre sens
        $minutesDiff = DateUtil::getMinuteDateDif($date2, $date1);
        $this->assertEquals(270, $minutesDiff); // 4h30 = 270 minutes, positif car date2 > date1
    }

    public function testGetDateFromZero(): void
    {
        // Tester avec une chaîne de date
        $dateFromZero = DateUtil::getDateFromZero('2023-06-15 14:30:45');
        $this->assertEquals('2023-06-15 00:00', $dateFromZero->format('Y-m-d H:i'));
    }

    public function testGetDateAtEnd(): void
    {
        // Tester avec une chaîne de date
        $dateAtEnd = DateUtil::getDateAtEnd('2023-06-15 14:30:45');
        $this->assertEquals('2023-06-16 00:00', $dateAtEnd->format('Y-m-d H:i'));
    }

    public function testGetDayOfWeek(): void
    {
        // Tester avec un objet DateTime
        $date = new \DateTime('2023-06-15'); // Un jeudi
        $dayOfWeek = DateUtil::getDayOfWeek($date);
        $this->assertEquals('THURSDAY', $dayOfWeek);
        
        // Tester un autre jour de la semaine
        $date = new \DateTime('2023-06-16'); // Un vendredi
        $dayOfWeek = DateUtil::getDayOfWeek($date);
        $this->assertEquals('FRIDAY', $dayOfWeek);
    }

    public function testGetWeekDayOfDate(): void
    {
        // La méthode utilise format('D') qui retourne le jour en 3 lettres en anglais
        // Vérifions que la conversion fonctionne pour tous les jours
        $days = [
            '2023-06-11' => 'SUNDAY',
            '2023-06-12' => 'MONDAY',
            '2023-06-13' => 'TUESDAY',
            '2023-06-14' => 'WEDNESDAY',
            '2023-06-15' => 'THURSDAY',
            '2023-06-16' => 'FRIDAY',
            '2023-06-17' => 'SATURDAY',
        ];
        
        foreach ($days as $dateStr => $expectedDay) {
            $date = new \DateTime($dateStr);
            $this->assertEquals($expectedDay, DateUtil::getWeekDayOfDate($date));
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
            $this->assertEquals($expected, DateUtil::getMonthFromPosition($position));
        }
        
        // Tester une position invalide
        $this->assertEquals('UNK', DateUtil::getMonthFromPosition(13));
    }

    public function testGetMonthday(): void
    {
        // Tester avec une date (mode 0 pour obtenir le numéro du mois)
        $date = new \DateTime('2023-06-15');
        $this->assertEquals(6, DateUtil::getMonthday($date->format('Y-m-d'), 0));
        
        // Tester avec une chaîne de date (mode 0 pour obtenir le numéro du mois)
        $this->assertEquals(12, DateUtil::getMonthday('2023-12-25', 0));
        
        // Tester avec mode 1 pour obtenir le nom du mois
        $date = new \DateTime('2023-06-15');
        $this->assertEquals('JUN', DateUtil::getMonthday($date->format('Y-m-d'), 1));
        
        // Tester avec une chaîne de date et mode 1
        $this->assertEquals('DEC', DateUtil::getMonthday('2023-12-25', 1));
    }
}
