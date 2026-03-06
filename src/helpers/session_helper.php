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
// On configure la session AVANT de la démarrer pour appliquer les protections
if (!isset($_SESSION)) {
    // Mode strict : rejette les IDs de session non générés par le serveur
    // Empêche les attaques par fixation de session (l'attaquant ne peut pas imposer un ID)
    ini_set('session.use_strict_mode', 1);

    // N'accepte les sessions QUE via les cookies (pas dans l'URL)
    // Empêche le vol de session par fuite de l'ID dans les liens ou le referrer
    ini_set('session.use_only_cookies', 1);

    // Cookie HttpOnly : le cookie de session n'est pas accessible par JavaScript
    // Protection contre le vol de session via XSS (document.cookie)
    ini_set('session.cookie_httponly', 1);

    // SameSite=Lax : le cookie n'est pas envoyé avec les requêtes cross-site POST
    // Protection supplémentaire contre les attaques CSRF
    ini_set('session.cookie_samesite', 'Lax');

    // Cookie Secure : le cookie n'est envoyé que sur HTTPS
    // On active cette protection uniquement si le site est servi en HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    // Durée de vie maximale de la session côté serveur : 30 minutes
    ini_set('session.gc_maxlifetime', 1800);

    // Démarrage de la session avec toutes les protections appliquées
    session_start();

    // === TIMEOUT DE SESSION (OWASP A07) ===
    // Si l'utilisateur est inactif depuis plus de 30 minutes, on détruit sa session
    // Cela limite la fenêtre d'exploitation en cas de vol de cookie de session
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();    // Supprime toutes les variables de session
        session_destroy();  // Détruit la session sur le serveur
        session_start();    // Redémarre une session vierge
    }
    // On met à jour le timestamp de dernière activité à chaque requête
    $_SESSION['last_activity'] = time();
}

// === FONCTIONS CSRF (OWASP A01 - Broken Access Control) ===
// Le CSRF (Cross-Site Request Forgery) consiste à forcer un utilisateur connecté
// à exécuter des actions à son insu (ex: changer son mot de passe via un lien piégé).
// Le token CSRF est un jeton unique inclus dans chaque formulaire et vérifié côté serveur.

/**
 * Génère ou récupère le token CSRF de la session courante
 *
 * Le token est généré avec random_bytes() qui est cryptographiquement sûr.
 * Un même token est réutilisé durant toute la session pour éviter les problèmes
 * de navigation (retour arrière, onglets multiples).
 *
 * @return string Le token CSRF en hexadécimal (64 caractères)
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // Génère 32 octets aléatoires cryptographiquement sûrs, convertis en hex
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Génère un champ HTML hidden contenant le token CSRF
 *
 * À inclure dans chaque formulaire POST pour le protéger contre le CSRF.
 * Exemple d'utilisation dans un template : {{{CSRF_FIELD}}}
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
 * Utilise hash_equals() pour une comparaison à temps constant,
 * empêchant les attaques par timing (mesure du temps de réponse).
 * Après vérification réussie, le token est régénéré (usage unique)
 * pour empêcher les attaques par rejeu.
 *
 * @return bool True si le token est valide, false sinon
 */
function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    // hash_equals() compare en temps constant pour éviter les timing attacks
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        return false;
    }
    // Régénère le token après vérification réussie (protection contre le rejeu)
    unset($_SESSION['csrf_token']);
    return true;
}

/**
 * Affiche ou stocke un message flash (message temporaire affiché une seule fois)
 *
 * Les messages flash sont utilisés pour afficher des notifications après une redirection
 * (succès d'inscription, erreur de connexion, etc.).
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
            // OWASP A03 - Injection : on échappe le contenu du message et la classe CSS
            // pour éviter toute injection XSS via les messages flash
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
