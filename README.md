# Chrono

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/samyasm/chrono)](https://www.php.net/)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/samyasm/chrono/php.yml?branch=main)](https://github.com/samyasm/chrono/actions)
[![Codecov](https://img.shields.io/codecov/c/github/samyasm/chrono)](https://codecov.io/gh/samyasm/chrono)
[![Total Downloads](https://img.shields.io/packagist/dt/samyasm/chrono)](https://packagist.org/packages/samyasm/chrono)

Une bibliothèque PHP moderne pour la manipulation et le formatage des dates, organisée en classes spécialisées pour une meilleure maintenabilité et une meilleure organisation du code.

## Installation

Utilisez Composer pour installer le package :

```bash
composer require samyasm/chrono
```

## Migration depuis DateUtil (v1.x vers v2.x)

Si vous migrez depuis une version précédente qui utilisait la classe `DateUtil`, voici comment mettre à jour votre code :

### Ancienne méthode (DateUtil) :
```php
use SamyAsm\Chrono\DateUtil;

// Opérations de base
$date = DateUtil::getDate('2023-06-15');
$newDate = DateUtil::addDaysToDate($date, 5);
$diff = DateUtil::getDateDayDif($date1, $date2);

// Formatage
$formatted = DateUtil::getDateAsString($date, 'Y-m-d');

// Périodes
$dates = DateUtil::getDatesFromRange('2023-06-01', '2023-06-15');
```

### Nouvelle méthode (Chrono* classes) :
```php
use SamyAsm\Chrono\ChronoCasting;
use SamyAsm\Chrono\ChronoComputer;
use SamyAsm\Chrono\ChronoPeriod;

// Conversion et formatage (remplace DateUtil::getDate, getDateAsString, etc.)
$date = ChronoCasting::getDate('2023-06-15');
$formatted = ChronoCasting::getDateAsString($date, 'Y-m-d');

// Opérations mathématiques (remplace addDaysToDate, getDateDayDif, etc.)
$newDate = ChronoComputer::addDaysToDate($date, 5);
$diff = ChronoComputer::getDateDayDif($date1, $date2);

// Périodes (remplace getDatesFromRange, etc.)
$dates = ChronoPeriod::getDatesFromRange('2023-06-01', '2023-06-15');
```

### Principaux changements :
1. Les méthodes ont été réparties dans des classes spécialisées :
   - `ChronoComputer` : Opérations mathématiques sur les dates
   - `ChronoCalendar` : Opérations de calendrier
   - `ChronoPeriod` : Gestion des intervalles
   - `ChronoCasting` : Conversion et formatage

2. Meilleure cohérence des noms de méthodes et des types de retour
3. Meilleure gestion des erreurs et des cas limites
4. Documentation plus complète et exemples mis à jour

## Classes et Utilisation

### 1. ChronoComputer - Calculs de dates

Gère les calculs et les opérations mathématiques sur les dates.

```php
use SamyAsm\Chrono\ChronoComputer;

// Ajouter des jours à une date
$date = new DateTime('2023-06-15 14:30:00');
$newDate = ChronoComputer::addDaysToDate($date, 5);
echo $newDate->format('Y-m-d H:i:s'); // 2023-06-20 14:30:00

// Calculer la différence en minutes entre deux dates
$date1 = new DateTime('2023-06-15 10:00:00');
$date2 = new DateTime('2023-06-15 14:30:00');
$minutesDiff = ChronoComputer::getMinuteDateDif($date1, $date2);
echo "Différence: $minutesDiff minutes"; // 270 minutes

// Convertir des unités de temps
$hours = ChronoComputer::convertMinutesToHours(90); // 1.5
$minutes = ChronoComputer::convertHoursToMinutes(2); // 120

// Obtenir le temps écoulé depuis une date (format lisible)
$lastSeen = new DateTime('2023-06-10 14:30:00');
echo ChronoComputer::lastSeenHelp($lastSeen); // "3 Days" (si aujourd'hui est le 13/06/2023)
```

### 2. ChronoCalendar - Opérations de calendrier

Gère les opérations liées au calendrier (jours, semaines, mois, années).

```php
use SamyAsm\Chrono\ChronoCalendar;

// Obtenir le premier jour de la semaine pour une date donnée
$date = new DateTime('2023-06-15'); // Un jeudi
$monday = ChronoCalendar::getFirstDayOfTheWeekFromDate($date);
echo $monday->format('Y-m-d'); // 2023-06-12 (lundi)

// Formater une date
$formattedDate = ChronoCalendar::formatDateDay($date);
echo $formattedDate; // 15/06/2023

// Obtenir le jour de la semaine
$dayOfWeek = ChronoCalendar::getDayOfWeek($date);
echo $dayOfWeek; // "THURSDAY"

// Obtenir le nom du mois à partir de sa position
echo ChronoCalendar::getMonthFromPosition(6); // "JUN"
```

### 3. ChronoPeriod - Gestion des périodes

Gère les intervalles et les plages de dates.

```php
use SamyAsm\Chrono\ChronoPeriod;

// Obtenir l'intervalle d'aujourd'hui (de minuit à 23:59:59)
$today = ChronoPeriod::getIntervalOfToday();
$start = $today['start']->format('Y-m-d H:i:s');
$end = $today['end']->format('Y-m-d H:i:s');
echo "Aujourd'hui de $start à $end";

// Obtenir toutes les dates entre deux dates
$startDate = '2023-06-01';
$endDate = '2023-06-03';
$dates = ChronoPeriod::getDatesFromRange($startDate, $endDate);
// Retourne ['2023-06-01', '2023-06-02', '2023-06-03']

// Ajuster un intervalle de dates
$interval = ChronoPeriod::adjustFilterInterval(
    new DateTime('2023-06-01'),
    new DateTime('2023-06-15')
);
```

### 4. ChronoCasting - Conversion et formatage

Gère la conversion entre différents formats de date et le typage.

```php
use SamyAsm\Chrono\ChronoCasting;

// Convertir un timestamp en objet DateTime
$date = ChronoCasting::timeToDate(1686844800);
echo $date->format('Y-m-d'); // 2023-06-15

// Créer un objet DateTime à partir d'une chaîne
$date = ChronoCasting::getDate('2023-06-15');

// Formater une date
$formatted = ChronoCasting::getDateAsString($date, 'Y/m/d');
echo $formatted; // 2023/06/15

// Vérifier si une date est valide
$isValid = ChronoCasting::isValidDate('2023-12-31'); // true

// Convertir un jour abrégé en nom complet
$fullDay = ChronoCasting::parseDay('Mon'); // "Monday"
```

## Fonctionnalités principales

- **ChronoComputer**: Calculs de dates et opérations mathématiques
- **ChronoCalendar**: Opérations de calendrier (semaines, mois, années)
- **ChronoPeriod**: Gestion des intervalles et plages de dates
- **ChronoCasting**: Conversion entre formats et typage des dates
- Compatible avec les objets DateTime natifs de PHP
- Typage strict et documentation complète
- Couverture de tests élevée
- Respect des standards de code PSR-12

## Configuration requise

- PHP 7.4 ou supérieur
- Extension PHP DateTime activée

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## Documentation de l'API

### ChronoComputer

#### `addDaysToDate(DateTimeInterface $dateTime, int $days = 1): ?DateTime`
Ajoute un nombre de jours à une date.

#### `getMinuteDateDif(DateTimeInterface $dateTime1, DateTimeInterface $dateTime2): int`
Calcule la différence en minutes entre deux dates.

#### `convertMinutesToHours(int $minutes = 1): float`
Convertit des minutes en heures.

#### `lastSeenHelp(string|DateTimeInterface $date): string`
Retourne une chaîne lisible du temps écoulé depuis une date.

### ChronoCalendar

#### `getFirstDayOfTheWeekFromDate(DateTimeInterface $dateTime): ?DateTime`
Retourne le premier jour (lundi) de la semaine pour une date donnée.

#### `formatDateDay(DateTimeInterface $date): string`
Formate une date au format jj/mm/aaaa.

#### `getDayOfWeek(DateTimeInterface $date): string`
Retourne le jour de la semaine en lettres majuscules.

#### `getMonthFromPosition(int $position): string`
Retourne le nom du mois en majuscules (JAN, FEB, etc.).

### ChronoPeriod

#### `getIntervalOfToday(): array`
Retourne l'intervalle d'aujourd'hui (de minuit à 23:59:59).

#### `getDatesFromRange(string $start, string $end, string $format = 'd-m-Y'): array`
Retourne un tableau de toutes les dates entre deux dates.

#### `adjustFilterInterval(?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): array`
Ajuste un intervalle de dates (inverse les dates si nécessaire).

### ChronoCasting

#### `timeToDate(int $timestamp): ?DateTime`
Convertit un timestamp Unix en objet DateTime.

#### `getDate($date = 'now'): ?DateTime`
Crée un objet DateTime à partir d'une chaîne ou d'un objet DateTimeInterface.

#### `getDateAsString(string|DateTimeInterface $date = 'now', string $format = 'd-m-Y'): string`
Formate une date selon le format spécifié.

#### `isValidDate(string $date, string $format = 'd-m-Y'): bool`
Vérifie si une chaîne représente une date valide selon le format donné.

Pour une documentation complète de toutes les méthodes, consultez le code source ou générez la documentation PHPDoc.

## Contribution

Les contributions sont les bienvenues ! Avant de soumettre une pull request, merci de :

1. Créer une issue pour discuter du changement proposé
2. Créer une branche pour votre fonctionnalité (`feature/ma-nouvelle-fonctionnalité`)
3. Exécuter les tests et vous assurer qu'ils passent tous
   ```bash
   composer test
   ```
4. Vérifier la qualité du code :
   ```bash
   # Vérifier le style de code
   composer check-style
   
   # Exécuter l'analyse statique
   composer static-analysis
   
   # Vérifier la couverture de code (doit être > 80%)
   composer test-coverage
   ```
5. Mettre à jour la documentation si nécessaire
6. Soumettre une pull request

### Environnement de développement

Pour configurer votre environnement de développement :

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/yourusername/Chrono.git
   cd Chrono
   ```

2. Installer les dépendances :
   ```bash
   composer install
   ```

3. Exécuter les tests :
   ```bash
   composer test
   ```

## Changelog

Consultez le [CHANGELOG.md](CHANGELOG.md) pour une liste des changements récents.

## Développement

### Exécuter les tests

```bash
composer test
```

### Vérifier la qualité du code

```bash
composer check-style
composer static-analysis
```

## Auteur

- [Samy](https://samuel-bakon.com)

---

Développé avec ❤️
