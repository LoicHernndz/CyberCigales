<?php

namespace Controllers;

use Views\Code\AllCodePage\AllCodePageView;

class Outils extends AbstractController {

    public function getMethod() {
        $view = new AllCodePageView();
        $view->render();
    }
}
