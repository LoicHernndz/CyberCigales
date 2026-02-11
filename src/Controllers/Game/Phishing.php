<?php

namespace Controllers\Game;

use Controllers\AbstractController;

class Phishing extends AbstractController
{
    public function getMethod()
    {
        header('Location: /email?mode=game');
        exit;
    }
}
