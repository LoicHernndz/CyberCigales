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
            $view->setLessonProgress($completed);
        } else {
            $view->addTemplateKey('PROGRESS_BANNER', '');
            $view->addTemplateKey('CESAR_BADGE', '');
            $view->addTemplateKey('VIGENERE_BADGE', '');
            $view->addTemplateKey('PERMUTATION_BADGE', '');
        }

        $view->render();
    }
}
