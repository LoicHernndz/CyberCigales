<?php

namespace Controllers;

use Views\MacOSView\MacOSView;
use Views\MacOSLock\MacOSLockView;
use Models\Lesson\LessonProgress;
use Attributes\Route;

/**
 * Contrôleur de la page MacOS
 *
 * Affiche l'interface simulée de MacOS via la vue correspondante.
 * L'accès est protégé : l'utilisateur doit être connecté à son compte CyberCigales.
 */
#[Route('/macos', name: 'macos')]
class Macos extends AbstractController
{
    /**
     * Affiche l'interface MacOS simulée (si connecté)
     * ou l'écran de verrouillage MacOS (si non connecté)
     *
     * @return void
     */
    public function getMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $view = new MacOSLockView();
            $view->render();
            return;
        }

        $progress = new LessonProgress();
        if (!$progress->areRequiredLessonsCompleted($_SESSION['user_id'])) {
            $missing = $progress->getMissingLessons($_SESSION['user_id']);
            flash('lecon', 'Vous devez d\'abord terminer les leçons suivantes avant d\'accéder à l\'escape game : ' . implode(', ', $missing), 'form-message form-message-red');
            redirect(url('lecon_index'));
            return;
        }

        $view = new MacOSView();
        $view->render();
    }
}
