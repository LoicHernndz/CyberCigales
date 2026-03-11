<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Models\Lesson\LessonProgress;
use Views\Lecon\LeconPermutation\LeconPermutationView;
use Attributes\Route;

#[Route('/lecon/permutation', name: 'lecon_permutation')]
class Permutation extends AbstractController
{
    public function getMethod(): void
    {
        $view = new LeconPermutationView();
        $view->addTemplateKey('LESSON_COMPLETE_SECTION', $this->buildCompleteSection('permutation'));
        $view->render();
    }

    private function buildCompleteSection(string $slug): string
    {
        if (!isset($_SESSION['user_id'])) {
            return '<div class="lesson-actions"><a href="' . url('lecon_index') . '" class="btn-primary">Retour aux leçons</a></div>';
        }

        $progress = new LessonProgress();
        if ($progress->isCompleted($_SESSION['user_id'], $slug)) {
            return '<div class="lesson-actions">
                <div class="lesson-done-badge"><span class="material-icons">check_circle</span> Leçon terminée</div>
                <a href="' . url('lecon_index') . '" class="btn-primary">Retour aux leçons</a>
            </div>';
        }

        return '<div class="lesson-actions">
            <form method="POST" action="' . url('lecon_complete') . '">
                <input type="hidden" name="lesson_slug" value="' . $slug . '">
                <button type="submit" class="btn-lesson-complete"><span class="material-icons">check</span> J\'ai terminé cette leçon</button>
            </form>
            <a href="' . url('lecon_index') . '" class="btn-secondary-link">Retour aux leçons</a>
        </div>';
    }
}
