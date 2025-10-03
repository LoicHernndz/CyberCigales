<?php
namespace controllers\User;
use controllers\ControllerInterface ;
use Models\User\User;
use Views\User\LoginView;

class Login implements ControllerInterface
{
    function control(){
        $view = new LoginView();
        $view->render();

    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/login" && $method === "GET";
    }
}
