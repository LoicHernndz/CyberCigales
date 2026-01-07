<?php

namespace Controllers\Minigames;

use Views\Minigames\MinigamesView;
use Controllers\AbstractController;

class Minigames extends AbstractController {

    public function getMethod() {
        $view = new MinigamesView();
        $view->render();
    }
}
