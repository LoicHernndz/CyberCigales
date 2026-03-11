<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Models\Lesson\LessonProgress;
use Views\Lecon\AllLessonPage\AllLessonPageView;
use Attributes\Route;

#[Route('/lecon', name: 'lecon_index')]
class Index extends AbstractController
{
    public function getMethod(): void
    {
        $view = new AllLessonPageView();
        $view->addTemplateKey('FLASH_MESSAGE', flash('lecon'));

        if (isset($_SESSION['user_id'])) {
            $progress = new LessonProgress();
            $completed = $progress->getCompletedLessons($_SESSION['user_id']);
            $lessonsCount = count(array_intersect($completed, ['cesar', 'vigenere', 'permutation']));

            $view->addTemplateKey('LESSONS_DONE_COUNT', $lessonsCount);
            $view->addTemplateKey('LESSONS_PERCENT', round($lessonsCount / 3 * 100));
            $view->addTemplateKey('CESAR_BADGE', in_array('cesar', $completed) ? '<span class="lesson-card-badge badge-done"><span class="material-icons">check_circle</span> Terminée</span>' : '<span class="lesson-card-badge badge-required"><span class="material-icons">star</span> Obligatoire</span>');
            $view->addTemplateKey('VIGENERE_BADGE', in_array('vigenere', $completed) ? '<span class="lesson-card-badge badge-done"><span class="material-icons">check_circle</span> Terminée</span>' : '<span class="lesson-card-badge badge-required"><span class="material-icons">star</span> Obligatoire</span>');
            $view->addTemplateKey('PERMUTATION_BADGE', in_array('permutation', $completed) ? '<span class="lesson-card-badge badge-done"><span class="material-icons">check_circle</span> Terminée</span>' : '<span class="lesson-card-badge badge-required"><span class="material-icons">star</span> Obligatoire</span>');
            $view->addTemplateKey('PROGRESS_BANNER', '<div class="lesson-progress-banner">
                <span class="material-icons">info</span>
                <span>Complétez les 3 leçons obligatoires pour débloquer l\'escape game</span>
                <div class="prereq-progress">
                    <div class="prereq-progress-bar"><div class="prereq-progress-fill" style="width: ' . round($lessonsCount / 3 * 100) . '%"></div></div>
                    <span class="prereq-progress-text">' . $lessonsCount . '/3</span>
                </div>
            </div>');
        } else {
            $view->addTemplateKey('PROGRESS_BANNER', '');
            $view->addTemplateKey('CESAR_BADGE', '');
            $view->addTemplateKey('VIGENERE_BADGE', '');
            $view->addTemplateKey('PERMUTATION_BADGE', '');
        }

        $view->render();
    }
}
