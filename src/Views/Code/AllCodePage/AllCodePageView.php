<?php

namespace Views\Code\AllCodePage;

use Views\AbstractView;

class AllCodePageView extends AbstractView {

    public function templatePath(): string {
        return __DIR__ . '/all-code-page.html';
    }

    public function templateKeys(): array {
        return [];
    }
}
