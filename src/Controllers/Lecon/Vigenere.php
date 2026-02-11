<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconVigenere\LeconVigenereView;
use Controllers\AbstractController;

class Vigenere extends AbstractController
{
    function getMethod()
    {
        $view = new LeconVigenereView();
        $view->render();
    }
}
