<?php
/**
 * Helpers de gestion de session
 *
 * Fonctions utilitaires pour les messages flash, redirections,
 * protection CSRF et configuration sécurisée des sessions.
 *
 * OWASP A01 - Broken Access Control : Protection CSRF
 * OWASP A07 - Identification and Authentication Failures : Session hardening
 */

use JetBrains\PhpStorm\NoReturn;

// === CONFIGURATION SÉCURISÉE DE LA SESSION (OWASP A07) ===
if (!isset($_SESSION)) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    ini_set('session.gc_maxlifetime', 1800);

    session_start();

    // Timeout de session : 30 minutes d'inactivité
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

// === FONCTIONS CSRF (OWASP A01) ===

/**
 * Génère ou récupère le token CSRF de la session courante
 *
 * @return string Le token CSRF en hexadécimal (64 caractères)
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Génère un champ HTML hidden contenant le token CSRF
 *
 * @return string Balise <input type="hidden"> avec le token CSRF
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Vérifie que le token CSRF soumis correspond à celui de la session
 *
 * Utilise hash_equals() pour une comparaison à temps constant.
 * Après vérification réussie, le token est régénéré (usage unique).
 *
 * @return bool True si le token est valide, false sinon
 */
function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        return false;
    }
    unset($_SESSION['csrf_token']);
    return true;
}

/**
 * Affiche ou stocke un message flash (message temporaire affiché une seule fois)
 *
 * @param string $name Identifiant du message (ex: "login", "signup")
 * @param string $message Texte du message à stocker (vide pour afficher)
 * @param string $class Classes CSS pour le style
 * @return string HTML du message ou chaîne vide
 */
function flash($name = '', $message = '', $class = 'form-message form-message-red'): string
{
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } else if (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : $class;
            // OWASP A03 - XSS : on échappe le contenu du message et la classe CSS
            $output = '<div class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">'
                    . htmlspecialchars($_SESSION[$name], ENT_QUOTES, 'UTF-8') . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
            return $output;
        }
    }
    return '';
}

/**
 * Redirige l'utilisateur vers une autre page
 *
 * @param string $location URL de destination
 * @return never
 */
#[NoReturn]
function redirect($location): void
{
    header("location: " . $location);
    exit();
}
