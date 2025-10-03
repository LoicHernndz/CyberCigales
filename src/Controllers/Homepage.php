<?php
namespace Controllers;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\HomepageView;

class Homepage implements ControllerInterface
{
    function control(){
        $view = new HomepageView();
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/" && $method === "GET";
    }
}
