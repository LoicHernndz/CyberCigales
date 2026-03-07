<?php

namespace Controllers;

use Models\InterfaceWeb\InterfaceWebModel;
use Views\InterfaceWeb\InterfaceWebView;
use Attributes\Route;

/**
 * Contrôleur de l'interface Web simulée
 *
 * Gère l'affichage et les interactions avec le navigateur web simulé.
 * Les requêtes GET et POST sont traitées de manière identique.
 */
#[Route('/web', name: 'web')]
class Web extends AbstractController
{
    /**
     * Affiche l'interface web simulée (GET)
     *
     * @return void
     */
    public function getMethod(): void
    {
        $this->renderInterface();
    }

    /**
     * Traite une interaction et affiche l'interface web simulée (POST)
     *
     * @return void
     */
    public function postMethod(): void
    {
        $this->renderInterface();
    }

    /**
     * Récupère l'URL courante et le contenu à afficher, puis rend la vue
     *
     * @return void
     */
    private function renderInterface(): void
    {
        $model = new InterfaceWebModel();
        $model->handleRequest();
        $url = $model->getCurrentUrl();
        $content = $model->getDisplayContent();
        $view = new InterfaceWebView();
        $view->render($url, $content);
    }
}
