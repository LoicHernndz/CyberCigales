<?php
/**
 * Point d'entrée de l'application CyberCigales
 * 
 * Routeur entièrement dynamique : l'URL est convertie en namespace de contrôleur.
 * Convention : /segment1/segment2 → Controllers\Segment1\Segment2 (kebab-case → PascalCase)
 * 
 * @package CyberCigales
 */

include "../src/config/Autoloader.php";
include "../src/helpers/session_helper.php";

// === OWASP A05 - Security Misconfiguration : Headers de sécurité HTTP ===
// Ces headers protègent contre les attaques côté client les plus courantes

// Empêche le navigateur de deviner le type MIME (protection contre les attaques MIME-sniffing)
header('X-Content-Type-Options: nosniff');

// Empêche l'affichage du site dans une iframe externe (protection contre le clickjacking)
// Un attaquant pourrait superposer notre site dans une iframe invisible pour piéger les clics
header('X-Frame-Options: SAMEORIGIN');

// Active le filtre XSS intégré des navigateurs (couche de défense supplémentaire)
header('X-XSS-Protection: 1; mode=block');

// Contrôle les informations envoyées dans le header Referer lors de la navigation
// strict-origin-when-cross-origin : n'envoie que l'origine (pas l'URL complète) vers d'autres sites
header('Referrer-Policy: strict-origin-when-cross-origin');

// Désactive l'accès aux APIs sensibles du navigateur (caméra, micro, géolocalisation)
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// OWASP A03 - CSP avec nonce : empêche l'exécution de scripts inline non autorisés
// Génère un nonce unique par requête (128 bits d'entropie)
// Seuls les <script nonce="..."> correspondants seront exécutés par le navigateur
$cspNonce = base64_encode(random_bytes(16));
define('CSP_NONCE', $cspNonce);
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$cspNonce}' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");

// Récupère l'URI sans les paramètres GET
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

// Cas spécial : page d'accueil
if ($uri === '/') {
    $controller = new Controllers\Homepage();
    $controller->control();
    exit();
}

/**
 * Convertit un segment d'URL kebab-case en PascalCase
 * Exemple : "chiffrement-cesar" → "ChiffrementCesar"
 */
function kebabToPascal(string $segment): string {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $segment)));
}

// Découpe l'URI en segments et convertit en PascalCase
$segments = explode('/', trim($uri, '/'));
$pascalSegments = array_map('kebabToPascal', $segments);

// Résolution : du chemin le plus long au plus court
// - i = total   → correspondance directe (/user/login → Controllers\User\Login)
// - i < total   → paramètres dynamiques  (/instagram/user/john → Controllers\Instagram\User + params)
// À chaque étape, on tente aussi la convention Index (/lecon → Controllers\Lecon\Index)
for ($i = count($pascalSegments); $i > 0; $i--) {
    $trySegments = array_slice($pascalSegments, 0, $i);
    $params = ($i < count($pascalSegments)) ? array_slice($segments, $i) : [];

    $tryClass = 'Controllers\\' . implode('\\', $trySegments);

    if (class_exists($tryClass)) {
        $_REQUEST['route_params'] = $params;
        $controller = new $tryClass();
        $controller->control();
        exit();
    }

    if (class_exists($tryClass . '\\Index')) {
        $_REQUEST['route_params'] = $params;
        $controller = new ($tryClass . '\\Index')();
        $controller->control();
        exit();
    }
}

// === Aucune correspondance → 404 ===
$controller = new Controllers\Error404\Error404();
$controller->control();
exit();
