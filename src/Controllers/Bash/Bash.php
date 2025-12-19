<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use Views\Bash\BashView;

class Bash extends AbstractController
{
    public function getMethod()
    {
        $view = new BashView();
        $view->render();
    }
}