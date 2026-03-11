<?php

namespace Controllers\Lecon;

use Controllers\AbstractController;
use Models\Lesson\LessonProgress;
use Attributes\Route;

#[Route('/lecon/complete', name: 'lecon_complete')]
class Complete extends AbstractController
{
    private const VALID_SLUGS = ['cesar', 'vigenere', 'permutation'];

    public function getMethod(): void
    {
        redirect(url('lecon_index'));
    }

    public function postMethod(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('user_login'));
            return;
        }

        $slug = $_POST['lesson_slug'] ?? '';

        if (!in_array($slug, self::VALID_SLUGS)) {
            redirect(url('lecon_index'));
            return;
        }

        $progress = new LessonProgress();
        $progress->markCompleted($_SESSION['user_id'], $slug);

        flash('lecon', 'Leçon terminée avec succès !', 'form-message form-message-green');
        redirect(url('lecon_index'));
    }
}
