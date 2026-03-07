<?php

namespace Controllers\DataBreach;

use Controllers\AbstractController;
use Views\DataBreach\DataBreachCheckView;
use Attributes\Route;

/**
 * Contrôleur de vérification des fuites de données
 *
 * Affiche l'interface de vérification permettant à l'utilisateur
 * de savoir si ses données ont été compromises (Have I Been Pwned).
 */
#[Route('/data-breach/check', name: 'data_breach_check')]
class Check extends AbstractController
{
    /**
     * Affiche le formulaire de vérification de fuite de données
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new DataBreachCheckView();
        $view->render();
    }
}
