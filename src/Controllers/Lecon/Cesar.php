<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconCesar\LeconCesarView;
use Controllers\AbstractController;

class Cesar extends AbstractController {
    function getMethod(){
        $view = new LeconCesarView();
        $view->render();
    }
}
