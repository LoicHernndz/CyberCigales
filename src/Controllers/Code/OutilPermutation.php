<?php

namespace Controllers\Code;

use Controllers\AbstractController;
use Views\Code\Permutation\PermutationDecryptToolView;

class OutilPermutation extends AbstractController
{
    public function getMethod()
    {
        $view = new PermutationDecryptToolView();
        $view->render();
    }
}
