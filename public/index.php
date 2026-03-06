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

// Récupère l'URI sans les paramètres GET
// Normalisation en lowercase : /MACOS, /MacOS → /macos (routing insensible à la casse)
$uri = strtolower(parse_url($_SERVER['REQUEST_URI'])['path']);

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
