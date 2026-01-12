<?php
namespace Controllers;

use Views\MacOSView\MacOSView;

class MacOSController
{
    function control(){
        if ($_SERVER['REQUEST_METHOD'] === "GET"){
            $this->getMethod();
        } else if ($_SERVER['REQUEST_METHOD']){
            $this->postMethod();
        }
    }

    public function getMethod()
    {
        // DEBUG: Vérifier l'état de la session
        // Décommenter pour débugger
        // echo "Session: "; var_dump($_SESSION); exit;
        
        // Vérifier si l'utilisateur est connecté
        if (!MacOSLogin::isLoggedIn()) {
            // Rediriger vers la page de connexion
            header('Location: /macos-login');
            exit;
        }

        $view = new MacOSView();
        $view->render();
    }

    function postMethod(){
        echo 'ERREUR 404';
    }

}
