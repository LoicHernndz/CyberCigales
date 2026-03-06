<?php
namespace helpers;

/**
 * Rate Limiter basé sur la session
 *
 * OWASP A07 - Identification and Authentication Failures :
 * Le rate limiting protège contre les attaques par force brute.
 * Sans rate limiting, un attaquant peut tester des milliers de mots de passe par minute
 * ou spammer les demandes de réinitialisation de mot de passe.
 *
 * Cette implémentation utilise la session PHP (pas de dépendance externe comme Redis).
 * Les tentatives sont stockées avec leur timestamp et nettoyées automatiquement
 * quand elles sortent de la fenêtre de temps.
 *
 * Exemple d'utilisation :
 *   if (!RateLimiter::check('login', 5, 300)) {
 *       // L'utilisateur a fait trop de tentatives (5 en 5 minutes)
 *       echo "Trop de tentatives, réessayez plus tard.";
 *   }
 *   RateLimiter::record('login'); // Enregistre la tentative
 */
class RateLimiter
{
    /**
     * Vérifie si l'action est encore autorisée (pas rate-limitée)
     *
     * Fonctionnement : on garde en session un tableau de timestamps des tentatives.
     * On supprime les tentatives qui sont hors de la fenêtre de temps,
     * puis on vérifie si le nombre restant dépasse le maximum autorisé.
     *
     * @param string $action Nom de l'action (ex: 'login', 'reset_password')
     * @param int $maxAttempts Nombre maximum de tentatives autorisées dans la fenêtre
     * @param int $windowSeconds Durée de la fenêtre en secondes (ex: 300 = 5 minutes)
     * @return bool True si l'action est autorisée, false si rate-limitée
     */
    public static function check(string $action, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $key = 'rate_limit_' . $action;
        $now = time();

        // Initialise le tableau de tentatives s'il n'existe pas encore
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        // Nettoie les tentatives expirées (hors de la fenêtre de temps)
        // Seules les tentatives récentes sont conservées
        $_SESSION[$key] = array_filter($_SESSION[$key], function ($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });

        // Vérifie si le maximum de tentatives est atteint
        if (count($_SESSION[$key]) >= $maxAttempts) {
            return false; // Rate limité : trop de tentatives dans la fenêtre
        }

        return true; // Autorisé : il reste des tentatives disponibles
    }

    /**
     * Enregistre une tentative pour l'action donnée
     *
     * Appelé APRÈS chaque tentative échouée (login raté, etc.)
     * pour incrémenter le compteur de tentatives.
     *
     * @param string $action Nom de l'action (ex: 'login', 'reset_password')
     */
    public static function record(string $action): void
    {
        $key = 'rate_limit_' . $action;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        // Ajoute le timestamp actuel comme nouvelle tentative
        $_SESSION[$key][] = time();
    }

    /**
     * Calcule le temps restant avant que la plus ancienne tentative expire
     *
     * Utile pour informer l'utilisateur combien de temps il doit attendre
     * avant de pouvoir réessayer.
     *
     * @param string $action Nom de l'action
     * @param int $windowSeconds Durée de la fenêtre en secondes
     * @return int Nombre de secondes restantes avant expiration
     */
    public static function retryAfter(string $action, int $windowSeconds = 300): int
    {
        $key = 'rate_limit_' . $action;
        if (empty($_SESSION[$key])) {
            return 0;
        }
        // La plus ancienne tentative détermine quand le compteur se réinitialise
        $oldest = min($_SESSION[$key]);
        return max(0, $windowSeconds - (time() - $oldest));
    }
}
