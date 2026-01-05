<?php
namespace Controllers;

/**
 * Génère et renvoie une image CAPTCHA.
 * - Stocke le code dans la session sous la clé 'captcha'.
 * - Préfère un rendu PNG via GD si disponible.
 * - Sinon, renvoie un SVG sans dépendance (fallback).
 */
class Captcha extends AbstractController
{
    /**
     * Point d'entrée GET appelé par le routeur: crée un code,
     * le place en session puis envoie l'image (PNG ou SVG).
     */
    function getMethod(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Génère un code alphanumérique de 5 caractères
        // Alphabet volontairement réduit pour éviter les confusions visuelles (0/O, 1/I)
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        // Mémorise le code côté serveur pour la vérification ultérieure
        $_SESSION['captcha'] = $code;

        // Si GD est disponible, on génère un PNG; sinon, on renvoie un SVG (fallback sans GD)
        if (function_exists('imagecreatetruecolor')) {
            $width = 140;
            $height = 48;
            $img = imagecreatetruecolor($width, $height);

            // Palette
            $bg = imagecolorallocate($img, 245, 246, 250);
            $border = imagecolorallocate($img, 200, 200, 200);
            $text = imagecolorallocate($img, 30, 30, 30);
            $noise1 = imagecolorallocate($img, 220, 220, 220);
            $noise2 = imagecolorallocate($img, 200, 210, 220);

            // Fond et bruit (lignes + points) pour compliquer l'OCR
            imagefilledrectangle($img, 0, 0, $width, $height, $bg);
            for ($i = 0; $i < 6; $i++) {
                imageline(
                    $img,
                    random_int(0, $width),
                    random_int(0, $height),
                    random_int(0, $width),
                    random_int(0, $height),
                    $noise1
                );
            }
            for ($i = 0; $i < 200; $i++) {
                imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $noise2);
            }

            $font = 5; // police intégrée GD
            $textWidth = imagefontwidth($font) * strlen($code);
            $textHeight = imagefontheight($font);
            $x = (int)(($width - $textWidth) / 2);
            $y = (int)(($height - $textHeight) / 2);
            imagestring($img, $font, $x, $y, $code, $text);

            imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);

            // En-têtes HTTP + anti-cache pour forcer le rafraîchissement du code
            header('Content-Type: image/png');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            imagepng($img);
            imagedestroy($img);
            exit;
        } else {
            // Fallback SVG, aucune dépendance
            $width = 140;
            $height = 48;
            // En-têtes HTTP + anti-cache
            header('Content-Type: image/svg+xml');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            $lines = '';
            for ($i = 0; $i < 6; $i++) {
                $x1 = random_int(0, $width);
                $y1 = random_int(0, $height);
                $x2 = random_int(0, $width);
                $y2 = random_int(0, $height);
                $lines .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#d0d7de" stroke-width="1" />';
            }

            echo '<?xml version="1.0" encoding="UTF-8"?>'
                .'<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">'
                .'<rect width="100%" height="100%" fill="#f5f6fa"/>'
                .$lines
                .'<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"'
                .' font-family="monospace" font-size="22" fill="#1e1e1e" letter-spacing="3">'
                .htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
                .'</text>'
                .'<rect x="0" y="0" width="'.($width-1).'" height="'.($height-1).'" fill="none" stroke="#c8c8c8"/>'
                .'</svg>';
            exit;
        }
    }
}

//
