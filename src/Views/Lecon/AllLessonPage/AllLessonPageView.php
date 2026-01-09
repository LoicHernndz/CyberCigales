<?php

namespace Views\Lecon\AllLessonPage;

use Views\AbstractView;

class AllLessonPageView extends AbstractView {

    public function templatePath(): string {
        return __DIR__ . '/all-lesson-page.html';
    }

    public function templateKeys(): array {
        return [];
    }
}
