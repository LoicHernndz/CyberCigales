<?php

namespace Views\Code\Permutation;

use Views\AbstractView;

class PermutationDecryptToolView extends AbstractView
{
    // Chemin du fichier HTML associé à la vue
    private const TEMPLATE_HTML = __DIR__ . '/permutation-decrypt-tool.html';

    // Méthode qui renvoie le chemin du template HTML à utiliser pour cette vue
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    // Méthode qui définit les variables à injecter dans le template HTML
    public function templateKeys(): array
    {
        return [];
    }
}

