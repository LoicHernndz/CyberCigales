<?php
namespace Views\Captcha;

/**
 * Vue pour le rendu du CAPTCHA en SVG (fallback quand GD n'est pas disponible)
 */
class CaptchaSvgView
{
    private string $code;
    private int $width;
    private int $height;

    /**
     * @param string $code   Code CAPTCHA à afficher dans le SVG
     * @param int    $width  Largeur de l'image SVG en pixels
     * @param int    $height Hauteur de l'image SVG en pixels
     */
    public function __construct(string $code, int $width = 140, int $height = 48)
    {
        $this->code = $code;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Génère et renvoie le SVG du captcha
     */
    public function render(): void
    {
        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        $service = new \Services\CaptchaService();
        $lines = $service->generateNoiseLines(6, $this->width, $this->height);

        $escapedCode = htmlspecialchars($this->code, ENT_QUOTES, 'UTF-8');
        $w = $this->width;
        $h = $this->height;

        echo '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '">'
            . '<rect width="100%" height="100%" fill="#f5f6fa"/>'
            . $lines
            . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"'
            . ' font-family="monospace" font-size="22" fill="#1e1e1e" letter-spacing="3">'
            . $escapedCode
            . '</text>'
            . '<rect x="0" y="0" width="' . ($w - 1) . '" height="' . ($h - 1) . '" fill="none" stroke="#c8c8c8"/>'
            . '</svg>';
    }
}
