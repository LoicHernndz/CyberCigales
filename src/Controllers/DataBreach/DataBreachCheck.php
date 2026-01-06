<?php

namespace Controllers\DataBreach;

use Controllers\AbstractController;
use Views\DataBreach\DataBreachCheckView;

class DataBreachCheck extends AbstractController
{
    function getMethod()
    {
        $view = new DataBreachCheckView();
        $view->render();
    }
}
