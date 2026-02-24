<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconHistMdp\LeconHistMdpView;
use Controllers\AbstractController;

class HistMdp extends AbstractController
{
    function getMethod()
    {
        $view = new LeconHistMdpView();
        $view->render();
    }
}
