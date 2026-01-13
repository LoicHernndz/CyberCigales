# CyberCigales
Plateforme en ligne ludique ayant pour but d’initier des lycéennes à la cybersécurité

## Description
CyberCigales est un site web éducatif sous forme d’escape game visant à initier
les lycéennes à la cybersécurité et à l’informatique de manière ludique.

## Fonctionnalités
- Jeux type escape game
- Modules de formation intégrés
- Mini-jeux d'entraînement servant à comprendre les formations
- Progression par niveaux
- Interface web responsive

## Technologies
- PHP
- HTML / CSS / JavaScript
- MySQL
- Git / GitHub

## Dépendances

### PHPMailer
Le projet utilise **PHPMailer** pour la gestion et l’envoi d’emails (formulaire de contact, notifications, etc.).

#### Installation de PHPMailer
Via Composer :

<pre><code>composer require phpmailer/phpmailer</code></pre>

PHPMailer est ensuite chargé automatiquement grâce à l’autoload de Composer :

<pre><code>require 'vendor/autoload.php';</code></pre>

ATTENTION Le dossier <code>vendor/</code> n’est pas versionné et doit être généré via Composer.

## Documentation (PHPDoc)
Le code du projet est documenté à l’aide de **PHPDoc** afin de faciliter la compréhension du code et la maintenance.

### Prérequis
- PHP installé sur la machine
- Composer
- PHPDoc

### Installation de PHPDoc
Via Composer (recommandé) :

<pre><code>composer require --dev phpdocumentor/phpdocumentor</code></pre>

### Génération de la documentation
À la racine du projet, exécuter :

<pre><code>vendor/bin/phpdoc -d src -t docs</code></pre>

- <code>src</code> : dossier contenant le code PHP  
- <code>docs</code> : dossier de sortie de la documentation  

La documentation générée est accessible via :

<pre><code>docs/index.html</code></pre>

## Installation
1. Cloner le dépôt  
   Dans le terminal, tapez :

<pre><code>git clone https://github.com/username/cybercigales.git</code></pre>

2. Installer les dépendances :

<pre><code>composer install</code></pre>

3. Configurer la base de données MySQL selon votre environnement.

## Équipe
Projet réalisé par :
- Adam AROUSSI BENTATA
- Younes BENAHMED
- Loïc HERNANDEZ
- Riad MEGNOUCHE
- Matis ROMBI

## Contexte
Projet réalisé dans le cadre du **BUT Informatique – 2e année**  
IUT Aix-Marseille

## Licence
Projet à but pédagogique – non destiné à un usage commercial

