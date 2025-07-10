# Changelog

Tous les changements notables apportés à ce projet seront documentés dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère à [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Ajouté
- Configuration de Codecov pour le suivi de la couverture de code
- Documentation complète de l'API dans le README.md
- Instructions détaillées pour les contributeurs
- Fichier de configuration PHPStan personnalisé (phpstan.neon)
- Fichier de règles PHP_CodeSniffer personnalisé (phpcs.xml)
- Tests unitaires supplémentaires pour améliorer la couverture de code
- Badges dans le README pour la licence, la version PHP, les tests et la couverture
- Support amélioré pour les objets DateTimeInterface dans toutes les méthodes
- Détection automatique des arguments inversés dans les méthodes de formatage
- Meilleure gestion des fuseaux horaires dans le constructeur de Chrono
- Consolidation de toutes les fonctionnalités dans la classe principale `Chrono`
- Documentation mise à jour pour refléter la nouvelle structure unifiée

### Modifié
- Amélioration de la documentation des méthodes avec des exemples détaillés
- Refonte complète de la classe Chrono pour étendre DateTime et implémenter DateTimeInterface
- Mise à jour des méthodes de formatage pour une meilleure compatibilité
- Correction des signatures de méthodes pour correspondre aux interfaces PHP natives
- Amélioration de la gestion des erreurs et des exceptions
- Optimisation des performances des opérations courantes
- Correction des tests unitaires pour refléter le comportement réel des méthodes
- Mise à jour du workflow GitHub Actions pour inclure l'upload des résultats de couverture
- Amélioration de la configuration des outils d'analyse statique
- Optimisation des scripts Composer pour le développement
- Refactorisation majeure : suppression de la classe DateUtil au profit de classes spécialisées
  - `ChronoComputer` : Calculs et opérations sur les dates
  - `ChronoCalendar` : Opérations de calendrier (semaines, mois, années)
  - `ChronoPeriod` : Gestion des intervalles et plages de dates
  - `ChronoCasting` : Conversion entre formats et typage des dates
- Mise à jour complète de la documentation pour refléter la nouvelle structure unifiée
- Correction des problèmes de formatage de code selon les standards PSR-12
- Unification de l'API : toutes les fonctionnalités sont maintenant accessibles directement via la classe `Chrono`
- Suppression de l'accès direct aux classes spécialisées (`ChronoComputer`, `ChronoCalendar`, etc.)
- Mise à jour des exemples de code dans la documentation pour utiliser la nouvelle API unifiée
- Amélioration de la cohérence des noms de méthodes et des signatures

## [1.0.0] - 2025-07-03

### Ajouté
- Première version stable du package
- Support de PHP 7.4 et versions ultérieures
- Méthodes de base pour la manipulation des dates
- Documentation complète avec exemples

### Modifié
- Structure du projet pour respecter les standards PSR-4
- Amélioration de la documentation du code

## [0.1.0] - 2025-07-03

### Ajouté
- Version initiale du projet
- Structure de base des fichiers
- Première implémentation des fonctionnalités principales
