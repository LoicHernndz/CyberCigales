<?php
namespace Models\InterfaceWeb;

/**
 * Class InterfaceWebModel
 *
 * Modèle gérant la logique métier du navigateur web simulé (Safari).
 * Cette classe agit comme un mini-serveur web interne : elle reçoit une URL demandée,
 * vérifie si elle existe dans sa liste de pages connues, et renvoie le contenu HTML approprié
 * ou une page d'erreur 404 simulée.
 *
 * @package Models\InterfaceWeb
 */
class InterfaceWebModel
{
    /**
     * L'URL actuellement affichée dans la barre d'adresse du navigateur.
     * @var string
     */
    private string $currentUrl;

    /**
     * Le contenu HTML brut à afficher dans le corps (viewport) du navigateur.
     * @var string
     */
    private string $displayContent;

    /**
     * Tableau associatif simulant une base de données de sites web accessibles (DNS).
     *
     * Clé : Le nom de domaine (ex: 'apple.com').
     * Valeur : Le code HTML complet de la page à afficher.
     *
     * @var array<string, string>
     */
    private array $availablePages = [
        'cybercigales.fr' => '
            <div style="text-align: center;">
                <h1 style="font-size: 3rem;">CyberCigales</h1>
                <p style="font-size: 1.5rem;">Découvrez la cryptographie de manière ludique avec CyberCigales !</p>
                <div style="margin:20px auto; width:80%; height:300px; background:#f5f5f7; border-radius:10px;"></div>
            </div>',

        'google.com' => '
            <div style="text-align: center; margin-top: 100px;">
                <h1 style="color: #4285F4; font-size: 5rem; margin:0;">Google</h1>
                <input type="text" style="margin-top:20px; padding: 10px; width: 400px; border-radius: 20px; border: 1px solid #dfe1e5;">
            </div>'
    ];

    /**
     * Constructeur.
     * Initialise le navigateur sur la page d'accueil par défaut (Apple).
     */
    public function __construct()
    {
        $this->currentUrl = "google.com";
        $this->displayContent = $this->availablePages['google.com'];
    }

    /**
     * Traite la requête utilisateur (changement d'URL).
     *
     * Cette méthode vérifie si un formulaire POST a été soumis. Si oui :
     * 1. Elle nettoie l'entrée utilisateur (suppression de http://, www, etc.).
     * 2. Elle cherche si la page existe dans $availablePages.
     * 3. Elle met à jour $displayContent avec la page trouvée ou une erreur 404 générée.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['url_input'])) {

            // 1. On récupère ce que l'utilisateur a tapé
            $rawInput = trim($_POST['url_input']);

            // 2. On nettoie l'URL pour trouver la "clé"
            $cleanKey = str_replace(['https://', 'http://', 'www.', '/'], '', $rawInput);

            // On met à jour l'URL affichée
            $this->currentUrl = $rawInput;

            // 3. On vérifie si la clé existe dans notre tableau
            if (array_key_exists($cleanKey, $this->availablePages)) {
                // La page existe
                $this->displayContent = $this->availablePages[$cleanKey];
            } else {
                // La page n'existe pas -> On génère la fausse 404
                $this->displayContent = $this->generateFake404($rawInput);
            }
        }
    }

    /**
     * Génère le code HTML d'une fausse page d'erreur "Introuvable".
     *
     * Cette méthode crée une page qui ressemble à une erreur de navigateur,
     * mais qui est techniquement une page valide affichée par l'application.
     *
     * @param string $url L'URL invalide saisie par l'utilisateur.
     * @return string Le code HTML de la page d'erreur.
     */
    private function generateFake404(string $url): string
    {
        return '
            <div style="text-align: center; color: #333; margin-top: 50px;">
                <h1 style="font-size: 40px; color: #555; margin-bottom:10px;">Introuvable</h1>
                <p style="font-size: 18px; color: #888;">Safari ne parvient pas à ouvrir la page.</p>
                
                <div style="background: #fff; border: 1px solid #ccc; padding: 20px; display: inline-block; margin-top: 30px; border-radius: 6px; max-width: 500px; text-align:left;">
                    <p style="margin:0;"><strong>Erreur :</strong> Impossible de trouver le serveur associé à l\'adresse :</p>
                    <p style="color: blue; margin-top:5px;">'.htmlspecialchars($url).'</p>
                    <hr style="margin: 15px 0; border:0; border-top:1px solid #eee;">
                    <p style="font-size: 12px; color: #aaa;">Essayez de taper <strong>apple.com</strong> ou <strong>google.com</strong>.</p>
                </div>
            </div>
        ';
    }

    /**
     * Récupère l'URL courante.
     *
     * @return string
     */
    public function getCurrentUrl(): string { return $this->currentUrl; }

    /**
     * Récupère le contenu HTML à afficher.
     *
     * @return string
     */
    public function getDisplayContent(): string { return $this->displayContent; }
}