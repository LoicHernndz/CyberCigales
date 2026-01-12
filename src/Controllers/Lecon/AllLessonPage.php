<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\AllLessonPage\AllLessonPageView;

/**
 * Contrôleur de la page récapitulative des leçons
 * 
 * Affiche la liste de toutes les leçons disponibles.
 */
class AllLessonPage extends AbstractController
{

    /**
     * Affiche la page des leçons
     * 
     * @return void
     */
    public function getMethod()
    {
        $view = new AllLessonPageView();
        $view->render();
    }
}
