<?php

namespace Controllers\Bash;

use Controllers\AbstractController;
use Views\Bash\BashView;

/**
 * Contrôleur de la page du terminal Bash simulé
 */
class Bash extends AbstractController
{
    /**
     * Affiche le terminal Bash
     * 
     * @return void
     */
    public function getMethod(): void
    {
        $view = new BashView();
        $view->render();
    }
}