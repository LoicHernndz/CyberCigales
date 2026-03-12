<?php

namespace Views\Lecon\LeconCesar;

use Views\AbstractView;

class LeconCesarView extends AbstractView {

    private const TEMPLATE_HTML = __DIR__ . '/lecon-cesar.html';
    private array $additionalKeys = [];

    public function addTemplateKey(string $key, $value): void
    {
        $this->additionalKeys[$key] = $value;
    }

    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    public function setLessonCompleteState(bool $isConnected, bool $isCompleted, string $slug): void
    {
        if (!$isConnected) {
            $html = '<div class="lesson-actions"><a href="' . url('lecon_index') . '" class="btn-primary">Retour aux leçons</a></div>';
        } elseif ($isCompleted) {
            $html = '<div class="lesson-actions">
                <div class="lesson-done-badge"><span class="material-icons">check_circle</span> Leçon terminée</div>
                <a href="' . url('lecon_index') . '" class="btn-primary">Retour aux leçons</a>
            </div>';
        } else {
            $html = '<div class="lesson-actions">
                <form method="POST" action="' . url('lecon_complete') . '">
                    <input type="hidden" name="lesson_slug" value="' . htmlspecialchars($slug) . '">
                    <button type="submit" class="btn-lesson-complete"><span class="material-icons">check</span> J\'ai terminé cette leçon</button>
                </form>
                <a href="' . url('lecon_index') . '" class="btn-secondary-link">Retour aux leçons</a>
            </div>';
        }
        $this->additionalKeys['LESSON_COMPLETE_SECTION'] = $html;
    }

    public function templateKeys() : array {
        return $this->additionalKeys;
    }
}
