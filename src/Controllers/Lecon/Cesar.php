<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconCesar\LeconCesarView;

/**
 * Contrôleur de la leçon sur le chiffrement César
 *
 * Affiche le contenu pédagogique expliquant le fonctionnement
 * du chiffrement de César.
 */
class Cesar extends AbstractController
{
    /**
     * Affiche la leçon sur le chiffrement César
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new LeconCesarView();
        $view->render();
    }
}
