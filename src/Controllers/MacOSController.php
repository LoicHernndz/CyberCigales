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
        $view = new MacOSView();
        $view->render();
    }

    function postMethod(){
        echo 'ERREUR 404';
    }

}
