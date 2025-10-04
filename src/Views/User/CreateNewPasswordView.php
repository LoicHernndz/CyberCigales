<?php

namespace Views\User;

use Views\AbstractView;

class CreateNewPasswordView extends AbstractView
{
    private const FLASH_KEY = 'FLASH';
    private const SELECTOR_KEY = 'SELECTOR';
    private const VALIDATOR_KEY = 'VALIDATOR';
    private const TEMPLATE_HTML = __DIR__ . '/create-new-password.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return [self::FLASH_KEY => flash('new-password'), self::SELECTOR_KEY => trim($_GET['selector']), self::VALIDATOR_KEY => trim($_GET['validator'])];
    }
}