<?php
namespace Controllers\User;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\User\SignupView;

class Signup implements ControllerInterface
{
    function control(){
        $view = new SignupView();
        $view->render();

    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/register" && $method === "GET";
    }
}