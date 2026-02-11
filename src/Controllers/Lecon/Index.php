<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\AllLessonPage\AllLessonPageView;

class Index extends AbstractController
{
    public function getMethod()
    {
        $view = new AllLessonPageView();
        $view->render();
    }
}
