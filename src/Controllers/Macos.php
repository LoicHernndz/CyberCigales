<?php
namespace Controllers;

use Controllers\AbstractController;
use Views\MacOSView\MacOSView;

class Macos extends AbstractController
{
    public function getMethod()
    {
        $view = new MacOSView();
        $view->render();
    }
}
