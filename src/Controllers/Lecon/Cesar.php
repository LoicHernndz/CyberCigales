<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Models\Lesson\LessonProgress;
use Views\Lecon\LeconCesar\LeconCesarView;
use Attributes\Route;

#[Route('/lecon/cesar', name: 'lecon_cesar')]
class Cesar extends AbstractController
{
    public function getMethod(): void
    {
        $view = new LeconCesarView();
        $isConnected = isset($_SESSION['user_id']);
        $isCompleted = false;
        if ($isConnected) {
            $progress = new LessonProgress();
            $isCompleted = $progress->isCompleted($_SESSION['user_id'], 'cesar');
        }
        $view->setLessonCompleteState($isConnected, $isCompleted, 'cesar');
        $view->render();
    }
}
