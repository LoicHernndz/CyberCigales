<?php

include "../src/config/Autoloader.php";
include "../src/helpers/session_helper.php";

use config\Routes;

// Recupere la requete actuelle en ignorant les parametres passes
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

// ========================================
// ROUTES DYNAMIQUES INSTAGRAM
// ========================================
// Routes pour les profils utilisateurs Instagram: /instagram/user/{username}
if (preg_match('#^/instagram/user/([^/]+)/chat$#', $uri)) {
    $controller = new Controllers\Instagram\UserChat();
    $controller->control();
    exit();
}

if (preg_match('#^/instagram/user/([^/]+)$#', $uri)) {
    $controller = new Controllers\Instagram\UserProfile();
    $controller->control();
    exit();
}

// ========================================
// ROUTES STATIQUES
// ========================================
//  On cherche a quel controleur correspond la requete actuelle
foreach (Routes::$routes as $key => $value) {
    if($key == $uri){
        $controller = new $value();
        $controller->control();  //  Execute l'action du controller (afficher une page et/ou actions)
        exit();
    }
}

//  Securite : Si l'url ne correspond a aucune page / methode implemente -> ERREUR 404
    $controller = new Controllers\Error404\Error404();
    $controller->control();;
    exit();
