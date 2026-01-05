<?php

namespace Controllers\Game;

use Controllers\AbstractController;
use Views\Game\PhishingGame\PhishingGameView;

class PhishingGame extends AbstractController
{
    public function getMethod()
    {
        $view = new PhishingGameView();
        $view->render();
    }
}
