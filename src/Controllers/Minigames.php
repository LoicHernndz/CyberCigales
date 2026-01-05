<?php

namespace Controllers;

use Views\Minigames\MinigamesView;

class Minigames extends AbstractController {

    public function getMethod() {
        $view = new MinigamesView();
        $view->render();
    }
}
