<?php
namespace Controllers\User;
use Controllers\ControllerInterface ;
use Models\User\User;
use Views\User\RegisterView;
use Views\User\UserView;


class RegisterPost implements ControllerInterface
{
    function control(){
        $user = new User($_POST[RegisterView::USERNAME], $_POST[RegisterView::PASSWORD], $_POST[RegisterView::EMAIL]);
        $view = new UserView($user);
        $view->render();
    }

    static function support(string $chemin, string $method) : bool{
        return $chemin === "/user/register" && $method === "POST";
    }
}