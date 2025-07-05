<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SamyAsm\Chrono\Chrono;
use DateTime;
use DateTimeZone;
use Exception;
use SamyAsm\Chrono\Traits\ChronoFactoryTrait;

#[CoversClass(ChronoFactoryTrait::class)]
class ChronoFactoryTraitTest extends TestCase
{
    public function testCreateFromTimestamp(): void
    {
        // Test with timestamp (compatible with DateTime::createFromTimestamp)
        $timestamp = 1686844800; // 2023-06-15 00:00:00 UTC
        $date = Chrono::createFromTimestamp($timestamp);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));
        
        // Test with float timestamp (microseconds)
        $floatTimestamp = 1686844800.123456;
        $dateFloat = Chrono::createFromTimestamp($floatTimestamp);
        $this->assertInstanceOf(DateTime::class, $dateFloat);
        $this->assertEquals('2023-06-15', $dateFloat->format('Y-m-d'));
    }

    public function testCreateFromTimestampWithTz(): void
    {
        $timestamp = 1686844800; // 2023-06-15 00:00:00 UTC
        
        // Test with timezone string
        $dateWithTz = Chrono::createFromTimestampWithTz($timestamp, 'UTC');
        $this->assertInstanceOf(DateTime::class, $dateWithTz);
        $this->assertEquals('2023-06-15', $dateWithTz->format('Y-m-d'));
        $this->assertEquals('UTC', $dateWithTz->getTimezone()->getName());
        
        // Test with DateTimeZone object
        $tz = new DateTimeZone('Europe/Paris');
        $dateWithTzObj = Chrono::createFromTimestampWithTz($timestamp, $tz);
        $this->assertEquals('Europe/Paris', $dateWithTzObj->getTimezone()->getName());
    }

    public function testCreate(): void
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
        $date = Chrono::create('2023-06-15', new DateTimeZone('Europe/Paris'));
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

    public function testCreateNow(): void
    {
        $now = Chrono::createNow();
        $this->assertInstanceOf(DateTime::class, $now);
        
        // Vérifier que la date est récente (moins de 5 secondes)
        $this->assertLessThan(5, time() - $now->getTimestamp());
    }

    public function testNow(): void
    {
        $now = Chrono::now();
        $this->assertInstanceOf(DateTime::class, $now);
        
        // Vérifier que la date est récente (moins de 5 secondes)
        $this->assertLessThan(5, time() - $now->getTimestamp());
    }

    public function testCreateDateTime(): void
    {
        // Test avec un objet DateTime
        $dateTime = new DateTime('2023-06-15 14:30:00');
        $date = Chrono::createDateTime($dateTime);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15 14:30:00', $date->format('Y-m-d H:i:s'));
        
        // Test avec un objet DateTimeImmutable (pour couvrir DateTime::createFromInterface)
        $dateTimeImmutable = new \DateTimeImmutable('2023-06-15 14:30:00');
        $date = Chrono::createDateTime($dateTimeImmutable);
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15 14:30:00', $date->format('Y-m-d H:i:s'));
        
        // Test avec une chaîne de date
        $date = Chrono::createDateTime('2023-06-15 14:30:00');
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15 14:30:00', $date->format('Y-m-d H:i:s'));
        
        // Test avec un fuseau horaire
        $date = Chrono::createDateTime('2023-06-15 14:30:00', 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $date->getTimezone()->getName());
    }

    public function testCreateFromFormat(): void
    {
        // Test avec un format standard
        $date = Chrono::createFromFormat('Y-m-d H:i:s', '2023-06-15 14:30:00');
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertEquals('2023-06-15 14:30:00', $date->format('Y-m-d H:i:s'));
        
        // Test avec un format personnalisé
        $date = Chrono::createFromFormat('d/m/Y', '15/06/2023');
        $this->assertEquals('2023-06-15', $date->format('Y-m-d'));
        
        // Test avec un fuseau horaire
        $date = Chrono::createFromFormat('Y-m-d H:i:s', '2023-06-15 14:30:00', 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $date->getTimezone()->getName());
        
        // Test avec une date invalide (doit lever une exception)
        $this->expectException(\Exception::class);
        Chrono::createFromFormat('Y-m-d', 'invalid-date');
    }

    public function testCreateDateTimeFromTimestamp(): void
    {
        $timestamp = 1686844800; // 2023-06-15 00:00:00 UTC
        
        // Sauvegarder le fuseau horaire par défaut
        $defaultTz = date_default_timezone_get();
        
        try {
            // Définir un fuseau horaire connu pour les tests
            date_default_timezone_set('UTC');
            
            // Créer une date de référence en UTC
            $utcDate = new DateTime('@' . $timestamp);
            $utcDate->setTimezone(new DateTimeZone('UTC'));
            
            // Test sans fuseau horaire (doit utiliser le fuseau par défaut)
            $date = Chrono::createDateTimeFromTimestamp($timestamp);
            $this->assertInstanceOf(DateTime::class, $date);
            $this->assertEquals($timestamp, $date->getTimestamp());
            
            // Vérifier que le fuseau horaire est soit 'UTC' soit '+00:00'
            $timezoneName = $date->getTimezone()->getName();
            $this->assertContains($timezoneName, ['UTC', '+00:00'], 'Le fuseau horaire doit être UTC ou +00:00');
            
            // Vérifier que la date est la même que la référence UTC
            $this->assertEquals($utcDate->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
            
            // Test avec un fuseau horaire en chaîne
            $date = Chrono::createDateTimeFromTimestamp($timestamp, 'UTC');
            $this->assertContains($date->getTimezone()->getName(), ['UTC', '+00:00']);
            $this->assertEquals($utcDate->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
            
            // Test avec un objet DateTimeZone
            $timezone = new DateTimeZone('Europe/Paris');
            $date = Chrono::createDateTimeFromTimestamp($timestamp, $timezone);
            $this->assertEquals('Europe/Paris', $date->getTimezone()->getName());
            
            // Vérifier que le décalage horaire est correct (2h en été)
            $parisDate = clone $utcDate;
            $parisDate->setTimezone($timezone);
            $this->assertEquals($parisDate->format('Y-m-d H:i:s'), $date->format('Y-m-d H:i:s'));
        } finally {
            // Restaurer le fuseau horaire par défaut
            date_default_timezone_set($defaultTz);
        }
    }
    
    /**
     * Test de la méthode createDateTimeInternal (méthode protégée)
     * Utilise la réflexion pour accéder à la méthode protégée
     */
    public function testCreateDateTimeInternal(): void
    {
        // Créer une classe de test pour accéder à la méthode protégée
        $reflectionClass = new \ReflectionClass(Chrono::class);
        $method = $reflectionClass->getMethod('createDateTimeInternal');
        $method->setAccessible(true);
        
        // Test avec un objet DateTime
        $dateTime = new DateTime('2023-06-15 14:30:00');
        $result = $method->invokeArgs(null, [$dateTime]);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:00', $result->format('Y-m-d H:i:s'));
        $this->assertNotSame($dateTime, $result, 'Should return a clone of the DateTime object');
        
        // Test avec un objet DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2023-06-15 14:30:00');
        $result = $method->invokeArgs(null, [$dateTimeImmutable]);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:00', $result->format('Y-m-d H:i:s'));
        
        // Test avec une chaîne de date
        $result = $method->invokeArgs(null, ['2023-06-15 14:30:00']);
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-06-15 14:30:00', $result->format('Y-m-d H:i:s'));
        
        // Test avec un fuseau horaire (chaîne)
        $result = $method->invokeArgs(null, ['2023-06-15 14:30:00', 'Europe/Paris']);
        $this->assertEquals('Europe/Paris', $result->getTimezone()->getName());
        
        // Test avec un fuseau horaire (objet DateTimeZone)
        $timezone = new DateTimeZone('America/New_York');
        $result = $method->invokeArgs(null, ['2023-06-15 14:30:00', $timezone]);
        $this->assertEquals('America/New_York', $result->getTimezone()->getName());
    }
    
    /**
     * Test de la méthode createFromFormat avec des cas plus complexes
     */
    public function testCreateFromFormatAdvanced(): void
    {
        // Test avec un format personnalisé et des valeurs limites
        $date = Chrono::createFromFormat('Y-m-d H:i:s', '2023-02-28 23:59:59');
        $this->assertEquals('2023-02-28 23:59:59', $date->format('Y-m-d H:i:s'));
        
        // Test avec un fuseau horaire
        $date = Chrono::createFromFormat('Y-m-d H:i:s', '2023-06-15 14:30:00', 'Asia/Tokyo');
        $this->assertEquals('Asia/Tokyo', $date->getTimezone()->getName());
        $this->assertEquals('2023-06-15 14:30:00', $date->format('Y-m-d H:i:s'));
        
        // Test avec un fuseau horaire UTC
        $date = Chrono::createFromFormat('Y-m-d H:i:s', '2023-06-15 14:30:00', 'UTC');
        $this->assertContains($date->getTimezone()->getName(), ['UTC', '+00:00']);
        
        // Test avec un format de date invalide (doit lever une exception)
        $this->expectException(\InvalidArgumentException::class);
        Chrono::createFromFormat('Y-m-d', 'not-a-date'); // Format de date invalide
    }
    
    /**
     * Test de la méthode create avec des cas limites
     */
    public function testCreateEdgeCases(): void
    {
        // Test avec une chaîne de date vide
        $this->expectException(\Exception::class);
        Chrono::create('');
        
        // Test avec une chaîne de date invalide
        $this->expectException(\Exception::class);
        Chrono::create('not-a-date');
    }
}
