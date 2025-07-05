<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SamyAsm\Chrono\Chrono;

// Example 1: Basic usage
$chrono = new Chrono('2023-06-15 14:30:00');
echo "=== Example 1: Basic usage ===\n";
echo "Current date: " . $chrono->format('Y-m-d H:i:s') . "\n\n";

// Example 2: Adding time
echo "=== Example 2: Adding time ===\n";
$newDate = $chrono->addDaysToDate(5);
echo "Date + 5 days: " . $newDate->format('Y-m-d') . "\n";

// Example 3: Using static methods
echo "\n=== Example 3: Static methods ===\n";
$firstDay = Chrono::getFirstDayOfMonth('2023-06-15');
echo "First day of month: " . $firstDay->format('Y-m-d') . "\n";

// Example 4: Chaining methods
echo "\n=== Example 4: Method chaining ===\n";
$result = (new Chrono('2023-06-15 14:30:00'))
    ->addDaysToDate(5)
    ->format('Y-m-d H:i:s');
echo "Chained operations: $result\n";

// Example 5: Date range
echo "\n=== Example 5: Date range ===\n";
$dates = Chrono::getDateRange('2023-06-01', '2023-06-05');
echo "Date range: " . implode(', ', $dates) . "\n";

// Example 6: Time ago
echo "\n=== Example 6: Time ago ===\n";
$timeAgo = Chrono::getTimeAgo('2023-06-10 10:00:00');
echo "Time ago: $timeAgo\n";

// Example 7: Using DateTime methods directly
echo "\n=== Example 7: DateTime methods ===\n";
$nextWeek = (new Chrono())->modify('+1 week')->format('Y-m-d');
echo "Next week: $nextWeek\n";

// Example 8: Creating from timestamp
echo "\n=== Example 8: Timestamp creation ===\n";
$yesterday = Chrono::createFromTimestamp(time() - 86400);
echo "Yesterday: " . $yesterday->format('Y-m-d') . "\n";

// Example 9: Getting date parts
echo "\n=== Example 9: Date parts ===\n";
$dayOfWeek = Chrono::getWeekday('2023-06-15');
$dayOfYear = Chrono::getDayOfYear('2023-06-15');
echo "Day of week: $dayOfWeek\n";
echo "Day of year: $dayOfYear\n";

// Example 10: Date validation
echo "\n=== Example 10: Date validation ===\n";
$isValid = Chrono::isValidDateString('2023-06-15') ? 'Yes' : 'No';
echo "Is valid date: $isValid\n";

// Example 11: Date comparison
echo "\n=== Example 11: Date comparison ===\n";
$date1 = new Chrono('2023-01-01');
$date2 = new Chrono('2023-12-31');
$diff = Chrono::getDateDayDif($date1->getDateTime(), $date2->getDateTime());
echo "Days between {$date1->format('Y-m-d')} and {$date2->format('Y-m-d')}: $diff days\n";

// Example 12: Working with intervals
echo "\n=== Example 12: Working with intervals ===\n";
$start = new Chrono('2023-01-01');
$end = new Chrono('2023-01-10');
$days = Chrono::getDaysBetween('2023-01-01', '2023-01-10');
echo "Days between 2023-01-01 and 2023-01-10: $days days\n";

// Example 13: Formatting dates
echo "\n=== Example 13: Date formatting ===\n";
$now = new Chrono();
echo "Current date (default format): " . $now->format('Y-m-d H:i:s') . "\n";
echo "Current date (custom format): " . $now->format('l, F jS Y \\a\\t g:ia') . "\n";

// Example 14: Timezone handling
echo "\n=== Example 14: Timezone handling ===\n";
// Using create method with timezone strings
$utc = Chrono::create('now', 'UTC');
$paris = Chrono::create('now', 'Europe/Paris');
echo "UTC time: " . $utc->format('Y-m-d H:i:s') . "\n";
echo "Paris time: " . $paris->format('Y-m-d H:i:s') . "\n";

// Example 15: Timezone conversion
echo "\n=== Example 15: Timezone conversion ===\n";
$date = Chrono::create('2025-01-01 12:00:00', 'America/New_York');
echo "New York time: " . $date->format('Y-m-d H:i:s T') . "\n";

// Create a new instance with Tokyo timezone
$tokyoTime = Chrono::create($date->getDateTime(), 'Asia/Tokyo');
echo "Tokyo time: " . $tokyoTime->format('Y-m-d H:i:s T') . "\n";
