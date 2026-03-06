<?php
namespace helpers;

/**
 * Logger de sécurité structuré
 *
 * OWASP A09 - Security Logging and Monitoring Failures :
 * Sans journalisation des événements de sécurité, il est impossible de :
 * - Détecter les tentatives d'intrusion (brute force, vol de compte)
 * - Enquêter après un incident de sécurité (qui a fait quoi, quand)
 * - Alerter en temps réel sur les comportements suspects
 *
 * Ce logger enregistre les événements de sécurité critiques dans le error_log PHP
 * au format JSON structuré, facilitant l'analyse et le parsing automatique.
 *
 * Événements enregistrés :
 * - LOGIN_SUCCESS : connexion réussie (pour détecter les connexions suspectes)
 * - LOGIN_FAILED : tentative de connexion échouée (détection brute force)
 * - PASSWORD_RESET_REQUESTED : demande de réinitialisation (détection abuse)
 * - PASSWORD_CHANGED : mot de passe modifié (audit trail)
 * - ACCOUNT_DELETED : suppression de compte (audit trail)
 *
 * Exemple de sortie :
 * [SECURITY] {"timestamp":"2026-03-06 14:30:00","event":"LOGIN_FAILED",
 *             "ip":"192.168.1.100","user_agent":"Mozilla/5.0...","user_id":null,
 *             "input":"admin@test.com"}
 */
class SecurityLogger
{
    /**
     * Enregistre un événement de sécurité dans les logs serveur
     *
     * Chaque entrée contient automatiquement :
     * - Le timestamp (pour la chronologie)
     * - L'adresse IP (pour identifier la source)
     * - Le User-Agent (pour identifier le navigateur/bot)
     * - L'ID utilisateur en session (si connecté)
     * - Le contexte spécifique à l'événement
     *
     * @param string $event Type d'événement (ex: 'LOGIN_SUCCESS', 'LOGIN_FAILED')
     * @param array $context Données supplémentaires spécifiques à l'événement
     */
    public static function log(string $event, array $context = []): void
    {
        // Construction de l'entrée de log avec les métadonnées automatiques
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            // L'IP permet d'identifier des patterns (beaucoup d'échecs depuis la même IP = attaque)
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            // Le User-Agent permet de distinguer les vrais navigateurs des bots
            // On tronque à 200 caractères pour éviter les logs trop longs
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200),
            // L'ID utilisateur en session (null si non connecté)
            'user_id' => $_SESSION['user_id'] ?? null,
        ];

        // Fusion avec le contexte spécifique à l'événement
        $entry = array_merge($entry, $context);

        // Écriture dans le error_log PHP au format JSON
        // JSON_UNESCAPED_UNICODE préserve les caractères accentués français
        error_log('[SECURITY] ' . json_encode($entry, JSON_UNESCAPED_UNICODE));
    }
}
