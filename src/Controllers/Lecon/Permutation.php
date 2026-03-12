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
        $isConnected = isset($_SESSION['user_id']);
        $isCompleted = false;
        if ($isConnected) {
            $progress = new LessonProgress();
            $isCompleted = $progress->isCompleted($_SESSION['user_id'], 'permutation');
        }
        $view->setLessonCompleteState($isConnected, $isCompleted, 'permutation');
        $view->render();
    }
}
