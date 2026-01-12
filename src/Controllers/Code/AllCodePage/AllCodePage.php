<?php

namespace Controllers\Code\AllCodePage;

use Views\Code\AllCodePage\AllCodePageView;
use Controllers\AbstractController;

/**
 * Contrôleur de la page des outils de chiffrement
 * 
 * Affiche la liste des outils de chiffrement/déchiffrement disponibles
 * (César, Vigenère, Permutation).
 */
class AllCodePage extends AbstractController {

    /**
     * Affiche la page récapitulative des outils de chiffrement
     * 
     * @return void
     */
    public function getMethod() {
        $view = new AllCodePageView();
        $view->render();
    }
}
