# Chrono

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/samuelbakon/chrono)](https://www.php.net/)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/samuelbakon/chrono/php.yml?branch=main)](https://github.com/samuelbakon/chrono/actions)
[![Codecov](https://img.shields.io/codecov/c/github/samuelbakon/chrono)](https://codecov.io/gh/samuelbakon/chrono)
[![Total Downloads](https://img.shields.io/packagist/dt/samuelbakon/chrono)](https://packagist.org/packages/samuelbakon/chrono)

If you're looking for a full-featured PHP date library, I recommend [Carbon](https://carbon.nesbot.com/).

This project is more of a utility library for recurring needs in my projects over time.

## Installation

Use Composer to install the package :

```bash
composer require samuelbakon/chrono
```

## Migration depuis DateUtil (v1.x vers v2.x)

If you're migrating from an older version that used the `DateUtil` class, here's how to update your code :

### Old method (DateUtil) :
```php
## Recommended Usage (Static Methods)

All Chrono methods are available statically, which is the recommended way to use the library:

```php
use SamBakon\Chrono\Chrono;

// Create and format dates
$date = Chrono::getDate('2023-06-15');
$formatted = Chrono::getDateAsString($date, 'Y-m-d');

// Date calculations
$newDate = Chrono::addDaysToDate($date, 5);
$diff = Chrono::getDateDayDif($date1, $date2);

// Date ranges
$dates = Chrono::getDatesFromRange('2023-06-01', '2023-06-15');
```

### Alternative (Instance Methods)

While static usage is preferred, you can also create an instance of Chrono if needed:

```php
use SamBakon\Chrono\Chrono;

$chrono = new Chrono();
$date = $chrono->getDate('2023-06-15');
$formatted = $chrono->getDateAsString($date, 'Y-m-d');
```

### Main changes :
1. Methods have been distributed into specialized classes :
   - `ChronoComputer` : Math operations on dates
   - `ChronoCalendar` : Calendar operations
   - `ChronoPeriod` : Interval management
   - `ChronoCasting` : Conversion and formatting

2. Better method name consistency and return types
3. Better error handling and edge case management
4. More complete documentation and updated examples

## Usage

### Basic usage

All functionalities are available through static methods of the `Chrono` class:

```php
use SamBakon\Chrono\Chrono;

// Create a date
$date = Chrono::getDate('2023-06-15 14:30:00');

// Add days to a date
$newDate = Chrono::addDaysToDate($date, 5);
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
echo ChronoComputer::lastSeenHelp($lastSeen); // "3 Days" (if today is 13/06/2023)
```

### Calendar operations

```php
// Get the first day of the week for a given date
$date = Chrono::getDate('2023-06-15'); // A Thursday
$monday = Chrono::getFirstDayOfTheWeekFromDate($date);
echo $monday->format('Y-m-d'); // 2023-06-12 (Monday)

// Formater une date
$formattedDate = ChronoCalendar::formatDateDay($date);
echo $formattedDate; // 15/06/2023

// Obtenir le jour de la semaine
$dayOfWeek = ChronoCalendar::getDayOfWeek($date);
echo $dayOfWeek; // "THURSDAY"

// Obtenir le nom du mois à partir de sa position
echo ChronoCalendar::getMonthFromPosition(6); // "JUN"
```

### Interval management

```php
// Get the interval of today (from midnight to 23:59:59)
$today = Chrono::getIntervalOfToday();
$start = $today['start']->format('Y-m-d H:i:s');
$end = $today['end']->format('Y-m-d H:i:s');
echo "Today from $start to $end";

// Get all dates between two dates
$startDate = '2023-06-01';
$endDate = '2023-06-03';
$dates = ChronoPeriod::getDatesFromRange($startDate, $endDate);
// Returns ['2023-06-01', '2023-06-02', '2023-06-03']

// Ajuster un intervalle de dates
$interval = ChronoPeriod::adjustFilterInterval(
    new DateTime('2023-06-01'),
    new DateTime('2023-06-15')
);
```

### Conversion and formatting

```php
// Convert a timestamp to a DateTime object
$date = Chrono::timeToDate(1686844800);
echo $date->format('Y-m-d'); // 2023-06-15

// Create a DateTime object from a string
$date = Chrono::getDate('2023-06-15');

// Format a date
$formatted = Chrono::getDateAsString($date, 'Y/m/d');
echo $formatted; // 2023/06/15

// Check if a date is valid
$isValid = ChronoCasting::isValidDate('2023-12-31'); // true

// Convert an abbreviated day to its full name
$fullDay = ChronoCasting::parseDay('Mon'); // "Monday"
```

## Main features

- **ChronoComputer**: Date calculations and mathematical operations
- **ChronoCalendar**: Calendar operations (weeks, months, years)
- **ChronoPeriod**: Interval management and date ranges
- **ChronoCasting**: Conversion between formats and typing of dates
- Compatible with native PHP DateTime objects
- Strict typing and complete documentation
- High test coverage
- Respect of PSR-12 code standards

## Configuration requirements

- PHP 7.4 or higher
- PHP DateTime extension enabled

## Licence

This project is under the MIT license. See the [LICENSE](LICENSE) file for more details.

## API Documentation

For a complete documentation of all methods, consult the source code or generate PHPDoc documentation.

## Contribution

Contributions are welcome! Before submitting a pull request, please :

1. Create an issue to discuss the proposed change
2. Create a branch for your feature (`feature/my-new-feature`)
3. Run the tests and make sure they pass
   ```bash
   composer test
   ```
4. Check the code quality :
   ```bash
   # Check code style
   composer check-style
   
   # Run static analysis
   composer static-analysis
   
   # Check code coverage (must be > 80%)
   composer test-coverage
   ```
5. Update the documentation if necessary
6. Submit a pull request

### Development environment

To configure your development environment :

1. Clone the repository :
   ```bash
   git clone https://github.com/yourusername/Chrono.git
   cd Chrono
   ```

2. Install dependencies :
   ```bash
   composer install
   ```

3. Run tests :
   ```bash
   composer test
   ```

## Changelog

Consult the [CHANGELOG.md](CHANGELOG.md) for a list of recent changes.

## Development

### Run tests

```bash
composer test
```

### Check code quality

```bash
composer check-style
composer static-analysis
```

## Author

- [Samuel Bakon (Samy)](https://samuel-bakon.com)

---

Developed with ❤️ And 🤖
