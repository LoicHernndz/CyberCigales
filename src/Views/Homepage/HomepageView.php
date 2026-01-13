<?php

namespace Views\Homepage;

use Views\AbstractView;

/**
 * Vue de la page d'accueil
 * 
 * Affiche le dashboard pour les utilisateurs connectés ou la landing page pour les visiteurs.
 */
class HomepageView extends AbstractView
{

    // Clé utilisée pour passer le nom d'utilisateur au template
    public const USERNAME_KEY = 'USERNAME_KEY';

    // Clés pour les statistiques
    public const MODULES_COMPLETED = 'MODULES_COMPLETED';
    public const TOTAL_POINTS = 'TOTAL_POINTS';
    public const LEARNING_TIME = 'LEARNING_TIME';
    public const RGPD_COMPLETION = 'RGPD_COMPLETION';
    public const CYPHER_COMPLETION = 'CYPHER_COMPLETION';
    public const UNLOCKED_BADGES = 'UNLOCKED_BADGES';

    // Chemins des fichiers HTML
    private const TEMPLATE_LOGGED = __DIR__ . '/homepage.html';
    private const TEMPLATE_GUEST = __DIR__ . '/homepage-guest.html';

    // Tableau des clés de template additionnelles
    private array $additionalKeys = [];

    // Méthode pour ajouter une clé de template
    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    // Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
    public function templatePath(): string
    {
        // Si l'utilisateur est connecté, on affiche le dashboard
        if (isset($_SESSION['user_id'])) {
            return self::TEMPLATE_LOGGED;
        } else {
            // Sinon on affiche la landing page
            return self::TEMPLATE_GUEST;
        }
    }

    // Méthode qui fournit les variables à injecter dans le template HTML
    public function templateKeys(): array
    {
        // Si un utilisateur est connecté (présence d'un ID dans la session)
        if (isset($_SESSION['user_id'])) {
            // On récupère le pseudo stocké dans la session et on n'affiche que le premier mot (souvent le prénom)
            $keys = [self::USERNAME_KEY => explode(" ", $_SESSION['user_pseudo'])[0]];

            // Valeurs par défaut pour les statistiques
            $defaultStats = [
                self::MODULES_COMPLETED => 0,
                self::TOTAL_POINTS => 0,
                self::LEARNING_TIME => 0,
                self::RGPD_COMPLETION => 0,
                self::CYPHER_COMPLETION => 0,
                self::UNLOCKED_BADGES => 0
            ];

            // Fusionner les clés par défaut avec les clés additionnelles
            return array_merge($defaultStats, $this->additionalKeys, $keys);
        } else {
            // Si aucun utilisateur n'est connecté, on retourne un tableau vide
            return [];
        }
    }
}
