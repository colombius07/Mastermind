# Jeu de Mastermind en PHP

## Description

Ce projet implémente un jeu de Mastermind en PHP où l'utilisateur tente de deviner une combinaison secrète de 4 à 6 chiffres. Les chiffres vont de 1 à 6. Le jeu donne un feedback en utilisant les couleurs suivantes pour indiquer la précision de la proposition :
- **⬜** : Le chiffre est correct et bien placé.
- **🔴** : Le chiffre est correct mais mal placé.

## Fichiers du Projet

### `index.php`

Ce fichier est la page principale du jeu où l'utilisateur peut entrer des propositions et voir l'historique des propositions précédentes ainsi que le feedback.

#### Fonctionnalités :
- **Formulaire de Proposition** : Permet à l'utilisateur de soumettre une combinaison de 4 à 6 chiffres.
- **Formulaire de Changement de Taille** : Permet de changer la taille du plateau de jeu.
- **Formulaire de Réinitialisation** : Permet de réinitialiser le jeu.
- **Historique des Propositions** : Affiche les propositions précédentes et leur feedback.
- **Chargement de Partie** : Permet de charger l'état précédent du jeu.

### `mastermind.php`

Ce fichier gère la logique du jeu Mastermind. Il évalue les propositions, met à jour l'état du jeu, et gère les redirections.

#### Fonctionnalités :
- **Génération de Combinaison Secrète** : Crée une combinaison secrète aléatoire au début du jeu.
- **Évaluation des Propositions** : Compare les propositions avec la combinaison secrète et génère le feedback approprié.
- **Gestion des Essais** : Compte le nombre d'essais et vérifie si la proposition est correcte.
- **Réinitialisation du Jeu** : Réinitialise le jeu si demandé.
- **Sauvegarde de Partie** : Sauvegarde l'état actuel du jeu.
- **Chargement de Partie** : Charge l'état précédemment sauvegardé du jeu.

## Choix Techniques

### Sessions

- **Pourquoi utiliser les sessions ?**
  - Les sessions permettent de stocker des données utilisateur persistantes entre les requêtes HTTP. Dans ce jeu, les sessions sont utilisées pour conserver l'état du jeu, y compris la combinaison secrète, les propositions, et les messages.
  - Elles permettent également de gérer facilement la réinitialisation et le changement de la taille du plateau de jeu sans perdre l'état actuel.

### Fichiers

- **Pourquoi utiliser des fichiers pour la sauvegarde ?**
  - Utiliser des fichiers pour la sauvegarde permet de persister l'état du jeu au-delà de la session actuelle. Cela signifie que les utilisateurs peuvent charger leur partie précédente même après avoir fermé le navigateur ou redémarré le serveur.
  - C'est une méthode simple et efficace pour ce type de projet sans nécessiter de base de données.

### Structure du Code

- **Séparation des responsabilités :**
  - `index.php` gère l'interface utilisateur et les formulaires.
  - `mastermind.php` contient la logique principale du jeu. Cette séparation permet de maintenir un code propre et facile à maintenir.

### Problèmes persistants

- **Sauvegarde de Partie** : La sauvegarde automatique ne prend pas en compte le premier tour.

## Temps Passé

*À compléter par vous-même.*

### Exemple :
- **Analyse et planification :** 2 heures
- **Développement initial :** 5 heures
- **Tests et débogage :** 3 heures
- **Documentation :** 1 heure

Total : *11 heures*
