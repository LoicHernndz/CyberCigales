<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\AllLessonPage\AllLessonPageView;

class AllLessonPage extends AbstractController {

    public function getMethod() {
        $view = new AllLessonPageView();
        $view->render();
    }
}
