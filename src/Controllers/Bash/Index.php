<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use Views\Bash\BashView;

class Index extends AbstractController
{
    public function getMethod(): void
    {
        $view = new BashView();
        $view->render();
    }
}
