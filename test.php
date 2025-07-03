<?php

require_once __DIR__ . '/vendor/autoload.php';

use SamyAsm\Chrono\ChronoCalendar;
use SamyAsm\Chrono\ChronoCasting;
use SamyAsm\Chrono\ChronoComputer;

// Créer une date de test
$date = new DateTime('2023-06-15 14:30:00');

// Exemple 1: Obtenir le premier jour de la semaine pour une date donnée
$firstDayOfWeek = ChronoCalendar::getFirstDayOfTheWeekFromDate($date);
echo "Premier jour de la semaine: " . $firstDayOfWeek->format('Y-m-d') . "\n";

// Exemple 2: Ajouter des jours à une date
$newDate = ChronoComputer::addDaysToDate($date, 5);
echo "Date + 5 jours: " . $newDate->format('Y-m-d H:i:s') . "\n";

// Exemple 3: Formater une date
$formattedDate = ChronoCalendar::formatDateDay($date);
echo "Date formatée: " . $formattedDate . "\n";

// Exemple 4: Vérifier si une date est valide
$isValid = ChronoCasting::isValidDate('2023-12-31') ? 'valide' : 'invalide';
echo "La date 2023-12-31 est $isValid\n";
