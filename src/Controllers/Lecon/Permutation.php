<?php

namespace Controllers\Lecon;

use Views\Lecon\LeconPermutation\LeconPermutationView;
use Controllers\AbstractController;

class Permutation extends AbstractController
{
    function getMethod()
    {
        $view = new LeconPermutationView();
        $view->render();
    }
}
