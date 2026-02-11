<?php

namespace Controllers\DataBreach;

use Controllers\AbstractController;
use Views\DataBreach\DataBreachCheckView;

class Check extends AbstractController
{
    function getMethod()
    {
        $view = new DataBreachCheckView();
        $view->render();
    }
}
