<?php
namespace Controllers;

use Views\Homepage\HomepageView;

class Dashboard extends AbstractController
{
    function getMethod(){
        // Redirige vers login si non connecte
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }

        $view = new HomepageView();
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/dashboard" && $method === "GET";
    }
}


