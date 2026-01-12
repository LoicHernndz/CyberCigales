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
                <input type="text" placeholder="Rechercher..." style="margin-top:20px; padding: 10px; width: 400px; border-radius: 20px; border: 1px solid #dfe1e5;">
                <div style="margin-top: 20px;">
                    <button style="padding: 10px 20px; border: 1px solid #f8f9fa; background: #f8f9fa; border-radius: 4px; cursor: pointer;">Recherche Google</button>
                    <!-- INDICE INSPECTER : TODO: Désactiver l\'accès temporaire vers dev.cybercigales.fr -->
                </div>
            </div>',

        'dev.cybercigales.fr' => '
            <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; font-family: sans-serif; background: white;">
                <h2 style="text-align: center; color: #d32f2f;">PANNEAU D\'ADMINISTRATION</h2>
                <p style="font-size: 14px; color: #666; text-align: center;">Accès réservé au personnel de maintenance.</p>
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px;">Utilisateur</label>
                    <input type="text" value="admin" disabled style="width: 100%; padding: 8px; box-sizing: border-box; background: #eee;">
                </div>
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Code d\'accès</label>
                    <input type="password" value="********" disabled style="width: 100%; padding: 8px; box-sizing: border-box; background: #eee;">
                </div>
                <div style="margin-top: 25px;">
                    <button id="admin-unlock-btn" disabled style="width: 100%; padding: 12px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: not-allowed; opacity: 0.6; font-weight: bold;">
                        DÉVERROUILLER LE SYSTÈME
                    </button>
                    <p id="unlock-message" style="display:none; color: green; font-weight: bold; margin-top: 10px; text-align: center;">Accès déverrouillé ! Regardez votre console (F12) pour le code final.</p>
                </div>
                <script>
                    (function() {
                        const btn = document.getElementById("admin-unlock-btn");
                        const msg = document.getElementById("unlock-message");
                        
                        btn.addEventListener("click", function() {
                            if (!btn.hasAttribute("disabled")) {
                                msg.style.display = "block";
                                console.log("%c [ADMIN] Accès accordé ! ", "background: #222; color: #bada55; font-size: 20px;");
                                console.log("Le mot de passe secret pour le terminal est : BASH_MASTER_2026");
                                alert("Système déverrouillé. Le code a été envoyé dans les logs administrateur (Console).");
                            } else {
                                alert("Bouton désactivé. Seul un administrateur peut modifier les attributs de la page.");
                            }
                        });
                    })();
                </script>
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