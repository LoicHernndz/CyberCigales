<?php
namespace Controllers\User;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\User\RegisterView;

class Register implements ControllerInterface
{
    function control(){
        $view = new RegisterView();
        $view->render();

    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/register" && $method === "GET";
    }
}