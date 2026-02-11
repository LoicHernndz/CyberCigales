<?php
namespace Controllers;

use Controllers\AbstractController;
use Views\SitePlan\SitePlanView;

class Plan extends AbstractController
{
    function getMethod(){
        $view = new SitePlanView();
        $view->render();
    }
}
