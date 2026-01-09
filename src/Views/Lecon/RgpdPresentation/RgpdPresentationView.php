<?php

namespace Views\Lecon\RgpdPresentation;

use Views\AbstractView;

/**
 * Vue pour la page de présentation du RGPD
 */
class RgpdPresentationView extends AbstractView
{
    function templatePath(): string
    {
        return __DIR__ . '/rgpd-presentation.html';
    }

    function templateKeys(): array
    {
        return [
            'FLASH_MESSAGE' => flash('qcm')
        ];
    }
}

