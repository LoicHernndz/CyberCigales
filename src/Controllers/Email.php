<?php

namespace Controllers;

use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;
use Attributes\Route;

/**
 * Contrôleur de la page Email
 *
 * Récupère les emails depuis le modèle et les affiche
 * via l'interface de messagerie simulée.
 */
#[Route('/email', name: 'email')]
class Email extends AbstractController
{
    /**
     * Récupère les emails et affiche l'interface de messagerie
     *
     * @return void
     */
    public function getMethod(): void
    {
        $view = new InterfaceMailView();
        $model = new InterfaceMailModel();
        $emails = $model->getemail();
        $view->render($emails);
    }
}
