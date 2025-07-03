# SuperDate

Une bibliothèque PHP utilitaire pour la manipulation et le formatage des dates.

## Installation

Utilisez Composer pour installer le package :

```bash
composer require samy/superdate
```

## Utilisation

```php
use Samy\SuperDate\DateUtil;

// Créer une instance de DateUtil
$dateUtil = new DateUtil();

// Exemple d'utilisation
$now = $dateUtil->now();
$formattedDate = $now->format('Y-m-d H:i:s');
```

## Fonctionnalités

- Manipulation facile des dates
- Formatage personnalisé
- Calculs de périodes
- Gestion des fuseaux horaires

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
