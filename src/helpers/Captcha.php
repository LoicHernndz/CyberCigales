<?php
// helpers/Captcha.php
namespace helpers;

/**
 * Générateur de CAPTCHA SVG
 * 
 * Crée un code visuel à 5 chiffres stocké en session pour la validation de formulaires.
 */
class Captcha
{
    /**
     * Génère et affiche un CAPTCHA au format SVG
     * 
     * Crée un code aléatoire à 5 chiffres, le stocke en session,
     * et génère une image SVG affichant ce code.
     * 
     * @return void Affiche directement l'image SVG et termine le script
     */
    public function captchaGeneration()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Génère un code aléatoire à 5 chiffres
        $_SESSION['captcha'] = rand(10000, 99999);
        $captcha = $_SESSION['captcha'];

        // En-tête pour afficher l'image SVG
        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40">
  <rect width="100%" height="100%" fill="#000"/>
  <text x="20" y="25" font-size="22" fill="#fff" font-family="monospace">$captcha</text>
</svg>
SVG;
        exit;
    }
}

// Exécution directe si on accède à /captcha
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requestUri === '/captcha') {
    $c = new Captcha();
    $c->captchaGeneration();
}
