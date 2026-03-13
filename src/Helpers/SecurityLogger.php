<?php
namespace helpers;

/**
 * Logger de sécurité structuré
 *
 * OWASP A09 - Security Logging and Monitoring Failures :
 * Enregistre les événements de sécurité critiques dans le error_log PHP
 * au format JSON structuré, facilitant l'analyse et le parsing automatique.
 *
 * Événements enregistrés :
 * - LOGIN_SUCCESS, LOGIN_FAILED, PASSWORD_RESET_REQUESTED,
 *   PASSWORD_CHANGED, ACCOUNT_DELETED
 */
class SecurityLogger
{
    /**
     * Enregistre un événement de sécurité dans les logs serveur
     *
     * @param string $event Type d'événement (ex: 'LOGIN_SUCCESS', 'LOGIN_FAILED')
     * @param array $context Données supplémentaires spécifiques à l'événement
     */
    public static function log(string $event, array $context = []): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200),
            'user_id' => $_SESSION['user_id'] ?? null,
        ];

        $entry = array_merge($entry, $context);

        error_log('[SECURITY] ' . json_encode($entry, JSON_UNESCAPED_UNICODE));
    }
}
