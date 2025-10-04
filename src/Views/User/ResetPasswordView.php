<?php

namespace Views\User;

use Views\AbstractView;

class ResetPasswordView extends AbstractView {

    private const FLASH_KEY = 'FLASH';
    private const TEMPLATE_HTML = __DIR__ . '/reset-password.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return [self::FLASH_KEY => flash('reset')];
    }
}