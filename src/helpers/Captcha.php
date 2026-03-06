<?php
// helpers/Captcha.php
namespace helpers;

/**
 * Générateur de CAPTCHA SVG renforcé
 *
 * OWASP A07 - Identification and Authentication Failures :
 * Le CAPTCHA empêche les bots automatisés de soumettre des formulaires
 * (inscription massive, brute force de login, spam de réinitialisation).
 *
 * Améliorations de sécurité par rapport à l'ancien CAPTCHA :
 * - 12 caractères alphanumériques au lieu de 5 chiffres (espace de recherche bien plus grand)
 * - Caractères ambigus exclus (0/O, 1/I/L) pour éviter les erreurs de lecture
 * - Bruit visuel (lignes aléatoires) pour perturber les OCR automatiques
 * - Rotation aléatoire des caractères pour compliquer la reconnaissance automatique
 * - random_int() au lieu de rand() pour une meilleure qualité aléatoire (cryptographiquement sûr)
 */
class Captcha
{
    /**
     * Génère et affiche un CAPTCHA renforcé au format SVG
     *
     * Le code est stocké en session et comparé lors de la soumission du formulaire.
     * L'image SVG contient du bruit visuel et des transformations pour résister
     * aux attaques par reconnaissance optique de caractères (OCR).
     *
     * @return void Affiche directement l'image SVG et termine le script
     */
    public function captchaGeneration()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Caractères alphanumériques sans ambiguïté (pas de 0/O, 1/I/L)
        // On enlève ces caractères car ils se ressemblent trop et provoquent
        // des erreurs de saisie frustrantes pour les utilisateurs légitimes
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        // Génération d'un code de 12 caractères avec random_int()
        // random_int() est cryptographiquement sûr, contrairement à rand()
        // qui utilise un générateur pseudo-aléatoire prédictible
        // 12 caractères = 30^12 ≈ 531 milliards de combinaisons possibles
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // Stockage du code en session pour vérification ultérieure
        $_SESSION['captcha'] = $code;

        // Headers HTTP pour empêcher la mise en cache de l'image
        // Sans ces headers, le navigateur pourrait afficher un ancien CAPTCHA
        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // === Génération du bruit visuel (lignes aléatoires) ===
        // Ces lignes parasites perturbent les algorithmes de reconnaissance
        // optique de caractères (OCR) utilisés par les bots pour lire le CAPTCHA
        $noise = '';
        for ($i = 0; $i < 8; $i++) {
            $x1 = random_int(0, 360);
            $y1 = random_int(0, 50);
            $x2 = random_int(0, 360);
            $y2 = random_int(0, 50);
            // Couleur grise variable pour que les lignes soient visibles mais pas trop
            $gray = random_int(80, 180);
            $noise .= "<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' stroke='rgb($gray,$gray,$gray)' stroke-width='1'/>";
        }

        // === Génération des caractères avec rotation aléatoire ===
        // Chaque caractère est placé à une position différente avec un angle
        // de rotation aléatoire, ce qui empêche les OCR de segmenter facilement le texte
        $textElements = '';
        for ($i = 0; $i < 12; $i++) {
            $x = 10 + ($i * 28);       // Espacement horizontal entre les caractères
            $y = random_int(25, 38);     // Position verticale légèrement variable
            $rotate = random_int(-20, 20); // Rotation aléatoire entre -20° et +20°
            // htmlspecialchars() pour éviter toute injection dans le SVG
            $char = htmlspecialchars($code[$i], ENT_QUOTES, 'UTF-8');
            $fontSize = random_int(18, 24); // Taille de police variable
            $textElements .= "<text x='$x' y='$y' font-size='$fontSize' fill='#fff' font-family='monospace' transform='rotate($rotate,$x,$y)'>$char</text>";
        }

        // Rendu de l'image SVG avec fond noir, bruit et texte
        // La largeur est adaptée à 12 caractères (360px)
        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="360" height="50">
  <rect width="100%" height="100%" fill="#111"/>
  $noise
  $textElements
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
