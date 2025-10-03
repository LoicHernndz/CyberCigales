<?php

namespace Views\User;
use Models\User\User;
use Views\AbstractView;

class LoginView extends AbstractView {

    private const FLASH_KEY = 'FLASH';
    private const TEMPLATE_HTML = __DIR__ . '/login.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        return [self::FLASH_KEY => flash('Login')];
    }
}