<?php

namespace Controllers\User;

use Controllers\ControllerInterface;
use Models\User\User;

class Logout implements ControllerInterface {

    function control(){
        // Je supprime la variable de session qui contient l'id de l'utilisateur
        unset($_SESSION['user_id']);
        // Je supprime la variable de session qui contient l'email de l'utilisateur
        unset($_SESSION['user_email']);
        // Je supprime la variable de session qui contient le pseudo de l'utilisateur
        unset($_SESSION['user_pseudo']);
        // Je détruis complètement la session (toutes les variables de session sont supprimées)
        session_destroy();
        // Je redirige l'utilisateur vers la page d'accueil
        redirect("/");
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/logout" && $method === "GET";
    }
}