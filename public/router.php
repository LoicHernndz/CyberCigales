<?php
/**
 * Routeur pour le serveur PHP intégré (php -S)
 *
 * Usage : php -S localhost:8080 router.php
 *
 * Le serveur PHP intégré ne supporte pas .htaccess,
 * ce fichier reproduit le même comportement de réécriture.
 */

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$file = __DIR__ . $uri;

// Si c'est un fichier statique existant (CSS, JS, images...), le servir directement
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Sinon, tout passe par index.php (comme le fait .htaccess avec RewriteRule)
require __DIR__ . '/index.php';
