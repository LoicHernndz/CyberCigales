<?php

namespace Services;

/**
 * Service de génération de bruit visuel pour les CAPTCHA SVG
 */
class CaptchaService
{
    /**
     * Génère des lignes de bruit aléatoires au format SVG
     *
     * @param int $count  Nombre de lignes à générer
     * @param int $width  Largeur de la zone SVG
     * @param int $height Hauteur de la zone SVG
     * @return string Markup SVG des lignes de bruit
     */
    public function generateNoiseLines(int $count, int $width, int $height): string
    {
        $lines = '';
        for ($i = 0; $i < $count; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $lines .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="#d0d7de" stroke-width="1" />';
        }
        return $lines;
    }
}
