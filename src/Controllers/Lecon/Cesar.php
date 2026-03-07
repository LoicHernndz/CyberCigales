<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\LeconCesar\LeconCesarView;
use Attributes\Route;

/**
 * Contrôleur de la leçon sur le chiffrement César
 *
 * Affiche le contenu pédagogique expliquant le fonctionnement
 * du chiffrement de César.
 */
#[Route('/lecon/cesar', name: 'lecon_cesar')]
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
