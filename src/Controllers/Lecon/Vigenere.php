<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Models\Lesson\LessonProgress;
use Views\Lecon\LeconVigenere\LeconVigenereView;
use Attributes\Route;

#[Route('/lecon/vigenere', name: 'lecon_vigenere')]
class Vigenere extends AbstractController
{
    public function getMethod(): void
    {
        $view = new LeconVigenereView();
        $isConnected = isset($_SESSION['user_id']);
        $isCompleted = false;
        if ($isConnected) {
            $progress = new LessonProgress();
            $isCompleted = $progress->isCompleted($_SESSION['user_id'], 'vigenere');
        }
        $view->setLessonCompleteState($isConnected, $isCompleted, 'vigenere');
        $view->render();
    }
}
