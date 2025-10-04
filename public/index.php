<?php
include "Autoloader.php";
include "../src/helpers/session_helper.php";

use Controllers\User\CreateNewPassword;
use Controllers\User\Login;
use Controllers\User\LoginPost;
use Controllers\User\ResetPassword;
use Controllers\User\ResetPasswordPost;
use Controllers\User\Signup;
use Controllers\User\SignupPost;
use Controllers\User\Logout;
use Controllers\Homepage;

// LISTE MANUELLE DES CONTROLLERS DISPONIBLES
$controller = [new Signup(), new SignupPost(), new Login(), new LoginPost(), new Logout(),
    new ResetPassword(), new ResetPasswordPost(), new CreateNewPassword(), new Homepage()];

//  AFFICHAGE DU SITE SELON URI
foreach ($controller as $key => $value) {
    if($value::support($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])){
        $value->control();  //  Execute l'action du controller
        exit();
    }
}

//  Securite : Si l'url ne correspond a aucune page / methode implemente -> ERREUR 404
echo "ERREUR 404";
    exit();
