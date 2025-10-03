<?php

namespace Views;
use Models\User\User;
use Views\AbstractView;

class HomepageView extends AbstractView {
    public const USERNAME_KEY = 'USERNAME_KEY';
    private const TEMPLATE_HTML = __DIR__ . '/homepage.html';

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function templateKeys() : array {
        if(isset($_SESSION['user_id'])) {
            return [self::USERNAME_KEY => explode(" ", $_SESSION['user_pseudo'])[0]];
        }else{
            return [self::USERNAME_KEY => 'Invité'];
        }
    }
}