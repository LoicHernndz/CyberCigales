<?php
namespace Controllers;

use Views\MacOSView\MacOSView;

/**
 * Contrôleur de l'interface MacOS simulée
 * 
 * Affiche une interface de bureau MacOS pour les activités pédagogiques.
 */
class MacOSController
{
    /**
     * Point d'entrée du contrôleur
     * 
     * @return void
     */
    function control()
    {
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            $this->getMethod();
        } else if ($_SERVER['REQUEST_METHOD']) {
            $this->postMethod();
        }
    }

    /**
     * Affiche l'interface MacOS
     * 
     * @return void
     */
    public function getMethod()
    {
        $view = new MacOSView();
        $view->render();
    }

    /**
     * Gère les requêtes POST (non implémenté)
     * 
     * @return void
     */
    function postMethod()
    {
        echo 'ERREUR 404';
    }

}
