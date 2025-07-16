<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamBakon\Chrono\Chrono;
use SamBakon\Chrono\Traits\ChronoFormatTrait;
use SamBakon\Chrono\Traits\ChronoUtilsTrait;
use SamBakon\Chrono\Traits\ChronoCalendarTrait;

#[CoversClass(ChronoFormatTrait::class)]
#[CoversClass(ChronoUtilsTrait::class)]
#[CoversClass(ChronoCalendarTrait::class)]
class ChronoFormatTraitTest extends TestCase
{
    public function testFormatDateDay(): void
    {
        $formattedDate = Chrono::formatDate('2023-06-15', 'd/m/Y');
        $this->assertEquals('15/06/2023', $formattedDate);
    }

    public function testGetMonthName(): void
    {
        // Test valid months using getMonthName with a date
        $months = [
            1 => 'JAN',
            6 => 'JUN',
            12 => 'DEC'
        ];

        foreach ($months as $position => $expected) {
            $monthName = Chrono::getMonthName("2023-$position-01");
            $this->assertEquals($expected, $monthName);
        }
        
        // Test getMonthName with a month number directly
        $this->assertEquals('JAN', Chrono::getMonthName(1));
        $this->assertEquals('JUN', Chrono::getMonthName(6));
        $this->assertEquals('DEC', Chrono::getMonthName(12));
        
        // Test an invalid position
        $this->assertEquals('UNK', Chrono::getMonthName(13));
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

    public function testGetMonthdayWithMonthName(): void
    {
        // Tester avec mode 1 pour obtenir le nom du mois
        $date = new \DateTime('2023-06-15');
        $this->assertEquals('JUN', Chrono::getMonthName($date->format('Y-m-d')));
        
        // Tester avec une chaîne de date et mode 1
        $this->assertEquals('DEC', Chrono::getMonthName('2023-12-25'));
    }

    public function testToString(): void
    {
        // Test avec un objet DateTime
        $date = new \DateTime('2023-06-15 14:30:45');
        $this->assertEquals('2023-06-15 14:30:45', Chrono::toString($date));
        
        // Test avec une chaîne de date
        $this->assertEquals('2023-12-25 00:00:00', Chrono::toString('2023-12-25'));
        
        // Test avec un timestamp sous forme de chaîne
        $timestamp = '2023-06-15 14:30:45';
        $this->assertEquals($timestamp, Chrono::toString($timestamp));
    }

    public function testFormatStatic(): void
    {
        // Test avec un objet DateTime
        $date = new \DateTime('2023-06-15 14:30:45');
        $this->assertEquals('15/06/2023', Chrono::formatStatic($date, 'd/m/Y'));
        
        // Test avec une chaîne de date
        $this->assertEquals('25/12/2023', Chrono::formatStatic('2023-12-25', 'd/m/Y'));
        
        // Test avec les arguments inversés (format et date)
        $this->assertEquals('25/12/2023', Chrono::formatStatic('d/m/Y', '2023-12-25'));
        
        // Test avec un format personnalisé
        $this->assertEquals('15-06-2023 02:30 PM', Chrono::formatStatic($date, 'd-m-Y h:i A'));
        
        // Test avec une date invalide
        $this->expectException(\InvalidArgumentException::class);
        Chrono::formatStatic('invalid-date', 'Y-m-d');
    }

    public function testFormatStaticWithDifferentFormats(): void
    {
        $date = '2023-06-15 14:30:45';
        
        // Test avec différents formats
        $this->assertEquals('15/06/2023', Chrono::formatStatic($date, 'd/m/Y'));
        $this->assertEquals('2023-06-15', Chrono::formatStatic($date, 'Y-m-d'));
        $this->assertEquals('Thursday, June 15, 2023', Chrono::formatStatic($date, 'l, F j, Y'));
        $this->assertEquals('14:30', Chrono::formatStatic($date, 'H:i'));
        $this->assertEquals('Jun 15, 2023, 2:30 PM', Chrono::formatStatic($date, 'M j, Y, g:i A'));
    }
}
