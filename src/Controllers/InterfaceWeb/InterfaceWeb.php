<?php
namespace Controllers\InterfaceWeb;

use Views\InterfaceWeb\InterfaceWebView;
use Models\InterfaceWeb\InterfaceWebModel;
use Controllers\AbstractController;

/**
 * Class InterfaceWeb
 *
 * Contrôleur principal pour l'interface de navigation web simulée (Safari).
 * * Cette classe sert de point d'entrée pour la route '/web'. Elle orchestre les interactions
 * entre l'utilisateur (qui navigue), le modèle (qui simule le chargement des pages)
 * et la vue (qui affiche le résultat HTML).
 *
 * @package Controllers\InterfaceWeb
 */
class InterfaceWeb extends AbstractController
{
    /**
     * Gère les requêtes HTTP GET.
     *
     * Méthode appelée lors de l'accès initial à la page (ex: clic sur l'icône Safari).
     * Elle déclenche l'affichage de l'interface avec la page d'accueil par défaut.
     *
     * @return void
     */
    function getMethod()
    {
        $this->renderInterface();
    }

    /**
     * Gère les requêtes HTTP POST.
     *
     * Méthode appelée lorsque l'utilisateur valide une URL dans la barre d'adresse.
     * Elle permet de recharger l'interface avec le nouveau contenu demandé sans changer
     * de contrôleur.
     *
     * @return void
     */
    function postMethod()
    {
        $this->renderInterface();
    }

    /**
     * Logique centrale de rendu de l'interface.
     *
     * Cette méthode évite la duplication de code entre getMethod et postMethod.
     * 1. Elle instancie le Modèle pour traiter la logique (vérification d'URL, génération de contenu).
     * 2. Elle récupère les données calculées (URL nettoyée, HTML de la fausse page).
     * 3. Elle passe ces données à la Vue pour générer le code HTML final envoyé au navigateur.
     *
     * @return void
     */
    private function renderInterface()
    {
        // Appel du modèle
        $model = new InterfaceWebModel();

        // Le modèle analyse la requête (GET ou POST) et met à jour son état interne
        $model->handleRequest();

        // Récupération des résultats traités par le modèle
        $url = $model->getCurrentUrl();
        $content = $model->getDisplayContent();

        // Appel de la vue pour l'affichage final
        $view = new InterfaceWebView();
        $view->render($url, $content);
    }
}