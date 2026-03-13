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
     * @param string $action Nom de l'action (ex: 'login', 'reset_password')
     * @param int $maxAttempts Nombre maximum de tentatives autorisées dans la fenêtre
     * @param int $windowSeconds Durée de la fenêtre en secondes (ex: 300 = 5 minutes)
     * @return bool True si l'action est autorisée, false si rate-limitée
     */
    public static function check(string $action, int $maxAttempts = 5, int $windowSeconds = 300): bool
    {
        $key = 'rate_limit_' . $action;
        $now = time();

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        $_SESSION[$key] = array_filter($_SESSION[$key], function ($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });

        if (count($_SESSION[$key]) >= $maxAttempts) {
            return false;
        }

        return true;
    }

    /**
     * Enregistre une tentative pour l'action donnée
     *
     * @param string $action Nom de l'action (ex: 'login', 'reset_password')
     */
    public static function record(string $action): void
    {
        $key = 'rate_limit_' . $action;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        $_SESSION[$key][] = time();
    }

    /**
     * Calcule le temps restant avant que la plus ancienne tentative expire
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
        $oldest = min($_SESSION[$key]);
        return max(0, $windowSeconds - (time() - $oldest));
    }
}
