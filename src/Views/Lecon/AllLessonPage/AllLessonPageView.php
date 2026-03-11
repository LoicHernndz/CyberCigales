<?php

namespace Views\Lecon\AllLessonPage;

use Views\AbstractView;

class AllLessonPageView extends AbstractView {

    private array $additionalKeys = [];

    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    public function templatePath(): string {
        return __DIR__ . '/all-lesson-page.html';
    }

    public function templateKeys(): array {
        return $this->additionalKeys;
    }
}
