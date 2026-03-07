<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Views\Lecon\RgpdPresentation\RgpdPresentationView;
use Attributes\Route;

/**
 * Contrôleur de la leçon sur le RGPD
 *
 * Affiche le contenu pédagogique sur le Règlement Général sur
 * la Protection des Données. Nécessite une session utilisateur active.
 */
#[Route('/lecon/rgpd', name: 'lecon_rgpd')]
class Rgpd extends AbstractController
{
    /**
     * Affiche la leçon sur le RGPD
     *
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            flash('qcm', 'Vous devez être connecté pour accéder au contenu RGPD.', 'form-message form-message-red');
            redirect(url('user_login'));
        }
        $view = new RgpdPresentationView();
        $view->render();
    }
}
