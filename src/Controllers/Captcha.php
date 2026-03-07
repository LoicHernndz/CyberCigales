<?php
namespace Controllers;

use Views\Captcha\CaptchaSvgView;
use Attributes\Route;

/**
 * Génère et renvoie une image CAPTCHA.
 * - Stocke le code dans la session sous la clé 'captcha'.
 * - Préfère un rendu PNG via GD si disponible.
 * - Sinon, délègue le rendu SVG à CaptchaSvgView (fallback).
 */
#[Route('/captcha', name: 'captcha')]
class Captcha extends AbstractController
{
    /**
     * Point d'entrée GET : crée un code,
     * le place en session puis envoie l'image (PNG ou SVG).
     */
    function getMethod(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $code = $this->generateCode();
        $_SESSION['captcha'] = $code;

        if (function_exists('imagecreatetruecolor')) {
            $this->renderPng($code);
        } else {
            $view = new CaptchaSvgView($code);
            $view->render();
        }
        exit;
    }

    /**
     * Génère un code alphanumérique de 5 caractères
     * (alphabet réduit pour éviter les confusions visuelles 0/O, 1/I)
     */
    private function generateCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $code;
    }

    /**
     * Rendu PNG via la bibliothèque GD
     */
    private function renderPng(string $code): void
    {
        $width = 140;
        $height = 48;
        $img = imagecreatetruecolor($width, $height);

        $bg     = imagecolorallocate($img, 245, 246, 250);
        $border = imagecolorallocate($img, 200, 200, 200);
        $text   = imagecolorallocate($img, 30, 30, 30);
        $noise1 = imagecolorallocate($img, 220, 220, 220);
        $noise2 = imagecolorallocate($img, 200, 210, 220);

        imagefilledrectangle($img, 0, 0, $width, $height, $bg);
        for ($i = 0; $i < 6; $i++) {
            imageline($img, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $noise1);
        }
        for ($i = 0; $i < 200; $i++) {
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $noise2);
        }

        $font = 5;
        $textWidth  = imagefontwidth($font) * strlen($code);
        $textHeight = imagefontheight($font);
        $x = (int)(($width - $textWidth) / 2);
        $y = (int)(($height - $textHeight) / 2);
        imagestring($img, $font, $x, $y, $code, $text);
        imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        imagepng($img);
        imagedestroy($img);
    }
}
