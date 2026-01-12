<?php
/**
 * Helpers de gestion de session
 * 
 * Fonctions utilitaires pour les messages flash et redirections.
 */

use JetBrains\PhpStorm\NoReturn;

// Je vérifie si une session n'est pas déjà démarrée pour éviter les erreurs
if (!isset($_SESSION)) {
    session_start(); // Je démarre la session pour pouvoir stocker des messages temporaires
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
            $_SESSION[$name] = $message; // Je stocke le message
            $_SESSION[$name . '_class'] = $class; // Je stocke la classe CSS
        } else if (empty($message) && !empty($_SESSION[$name])) {
            // Si je veux AFFICHER le message stocké (pas de nouveau message fourni)
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : $class;
            $output = '<div class="' . $class . '">' . $_SESSION[$name] . '</div>'; // J'affiche le message
            unset($_SESSION[$name]); // Je supprime le message après l'avoir affiché
            unset($_SESSION[$name . '_class']); // Je supprime la classe aussi
            return $output;
        }
    }
    return '';
}

// Ma fonction pour rediriger l'utilisateur vers une autre page

/**
 * Redirige l'utilisateur vers une autre page
 * 
 * @param string $location URL de destination
 * @return never
 */
#[NoReturn]
function redirect($location): void
{
    header("location: " . $location); // Je dis au navigateur d'aller à cette adresse
    exit();
}