<?php

namespace Controllers\Qcm;

use Controllers\AbstractController;
use Views\Qcm\RgpdPresentation\RgpdPresentationView;

/**
 * Contrôleur pour afficher la page de présentation du RGPD
 * Route: GET /qcm/rgpd/presentation
 */
class RgpdPresentation extends AbstractController
{
    /**
     * Affiche la page de présentation du RGPD
     */
    function getMethod()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            flash('qcm', 'Vous devez être connecté pour accéder au contenu RGPD.', 'form-message form-message-red');
            redirect('/user/login');
        }
        
        // Vérifier si l'utilisateur veut accéder directement au QCM
        if (isset($_GET['direct']) && $_GET['direct'] === '1') {
            redirect('/qcm/rgpd');
            return;
        }

        // Afficher la vue
        $view = new RgpdPresentationView();
        $view->render();
    }
}

