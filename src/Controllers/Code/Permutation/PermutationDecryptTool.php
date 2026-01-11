<?php

namespace Controllers\Code\Permutation;

use Controllers\AbstractController;
use Views\Code\Permutation\PermutationDecryptToolView;

/**
 * Contrôleur pour l'outil de décryptage visuel par permutation.
 * Permet de réorganiser les colonnes par drag'n'drop pour décrypter un message.
 */
class PermutationDecryptTool extends AbstractController
{
    /**
     * Affiche l'interface de l'outil de décryptage.
     */
    public function getMethod()
    {
        // Créer une instance de la vue PermutationDecryptToolView
        $view = new PermutationDecryptToolView();

        // Afficher le contenu de la page
        $view->render();
    }
}

