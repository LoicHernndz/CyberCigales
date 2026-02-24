<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\AllLessonPage\AllLessonPageView;

/**
 * Contrôleur de la page d'accueil des leçons
 *
 * Affiche la liste de toutes les leçons disponibles.
 * Utilise la convention Index pour le routeur dynamique (/lecon → Lecon\Index).
 */
class Index extends AbstractController
{
    /**
     * Affiche la page listant toutes les leçons disponibles
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new AllLessonPageView();
        $view->render();
    }
}
