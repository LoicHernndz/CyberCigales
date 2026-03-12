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

    public function setLessonProgress(array $completed): void
    {
        $lessonsCount = count(array_intersect($completed, ['cesar', 'vigenere', 'permutation']));

        foreach (['cesar', 'vigenere', 'permutation'] as $slug) {
            $key = strtoupper($slug) . '_BADGE';
            if (in_array($slug, $completed)) {
                $this->additionalKeys[$key] = '<span class="lesson-card-badge badge-done"><span class="material-icons">check_circle</span> Terminée</span>';
            } else {
                $this->additionalKeys[$key] = '<span class="lesson-card-badge badge-required"><span class="material-icons">star</span> Obligatoire</span>';
            }
        }

        $percent = round($lessonsCount / 3 * 100);
        $this->additionalKeys['PROGRESS_BANNER'] = '<div class="lesson-progress-banner">
                <span class="material-icons">info</span>
                <span>Complétez les 3 leçons obligatoires pour débloquer l\'escape game</span>
                <div class="prereq-progress">
                    <div class="prereq-progress-bar"><div class="prereq-progress-fill" style="width: ' . $percent . '%"></div></div>
                    <span class="prereq-progress-text">' . $lessonsCount . '/3</span>
                </div>
            </div>';
    }

    public function templateKeys(): array {
        return $this->additionalKeys;
    }
}
