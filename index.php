<?php
include "Autoloader.php";

use Controllers\User\Register;
use Controllers\User\RegisterPost;
use Controllers\User\Login;
use Controllers\Homepage;

// LISTE MANUELLE DES CONTROLLERS DISPONIBLES
$controller = [new Register(), new RegisterPost(), new Login(), new Homepage()];

//  AFFICHAGE DU SITE SELON URI
foreach ($controller as $key => $value) {
    if($value::support($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])){
        $value->control();
        exit();
    }
}

//  Securite : Si l'url ne correspond a aucune page / methode implemente -> ERREUR 404
echo "ERREUR 404 ";
    exit();
