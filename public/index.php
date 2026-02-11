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

// === Tentative 1 : correspondance directe ===
// /user/login → Controllers\User\Login
$controllerClass = 'Controllers\\' . implode('\\', $pascalSegments);

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    $controller->control();
    exit();
}

// === Tentative 2 : convention Index (pour les répertoires) ===
// /lecon → Controllers\Lecon\Index
$indexClass = $controllerClass . '\\Index';
if (class_exists($indexClass)) {
    $controller = new $indexClass();
    $controller->control();
    exit();
}

// === Tentative 3 : recherche progressive avec paramètres ===
// /instagram/user/john/chat → Controllers\Instagram\User + params ['john', 'chat']
for ($i = count($pascalSegments) - 1; $i > 0; $i--) {
    $trySegments = array_slice($pascalSegments, 0, $i);
    $params = array_slice($segments, $i); // segments originaux (non PascalCase) comme paramètres

    $tryClass = 'Controllers\\' . implode('\\', $trySegments);

    if (class_exists($tryClass)) {
        $_REQUEST['route_params'] = $params;
        $controller = new $tryClass();
        $controller->control();
        exit();
    }

    // Essai Index dans le sous-dossier
    $tryIndex = $tryClass . '\\Index';
    if (class_exists($tryIndex)) {
        $_REQUEST['route_params'] = $params;
        $controller = new $tryIndex();
        $controller->control();
        exit();
    }
}

// === Aucune correspondance → 404 ===
$controller = new Controllers\Error404\Error404();
$controller->control();
exit();
