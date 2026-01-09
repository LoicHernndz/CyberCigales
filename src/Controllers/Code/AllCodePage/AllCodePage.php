<?php

namespace Controllers\Code\AllCodePage;

use Views\Code\AllCodePage\AllCodePageView;
use Controllers\AbstractController;

class AllCodePage extends AbstractController {

    public function getMethod() {
        $view = new AllCodePageView();
        $view->render();
    }
}
