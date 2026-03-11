<?php
namespace Views\InterfaceMail;

use Couchbase\View;

/**
 * Class InterfaceMailView
 *
 * Gère l'affichage de l'interface de messagerie en injectant des données dynamiques
 * dans un template HTML séparé.
 *
 * @package Views\InterfaceMail
 */
class InterfaceMailView {

    /**
     * Chemin absolu vers le fichier de template HTML.
     * @var string
     */
    private const TEMPLATE_HTML = __DIR__ . '/interface-mail.html';

    /**
     * Récupère le chemin du template HTML.
     *
     * @return string Le chemin absolu du fichier.
     */
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    /**
     * Point d'entrée pour afficher la vue.
     *
     * Cette méthode appelle la logique de rendu principale.
     *
     * @param array $emails Tableau associatif contenant les données des emails.
     * @return void
     */
    function render(array $emails = []){
        $this->renderBody($emails);
    }

    /**
     * Charge le template, injecte les données et affiche le résultat final.
     *
     * Cette méthode remplace les placeholders {{ EMAIL_LIST }} et {{ READING_PANE_CONTENT }}
     * par le HTML généré dynamiquement.
     *
     * @param array $emails Liste des emails à afficher.
     * @return void
     */
    function renderBody(array $emails = []): void
    {
        $templatePath = $this->templatePath();

        if (file_exists($templatePath)) {
            $template = file_get_contents($templatePath);

            // Crée la liste des e-mails en HTML à partir des données PHP
            $emailListHTML = $this->generateEmailListHTML($emails);

            // Remplace l'endroit où la liste des e-mails doit apparaître
            $template = str_replace('{{ EMAIL_LIST }}', $emailListHTML, $template);

            // Déterminer quel e-mail afficher par défaut dans le panneau de lecture
            $defaultEmailContent = $emails[0] ?? null;
            $template = str_replace('{{ READING_PANE_CONTENT }}', $this->generateReadingPaneHTML($defaultEmailContent), $template);

            echo $template;
        } else {
            error_log("Erreur critique [InterfaceMailView] : Le fichier template est introuvable au chemin : " . $templatePath);

            echo "<div class='error-template-missing'>
                    <strong>Erreur système :</strong> Impossible de charger l'interface de messagerie (Template manquant).
                    Veuillez contacter l'administrateur.
                  </div>";
        }
    }

    /**
     * Génère la liste HTML des emails pour la barre latérale.
     *
     * Chaque élément <li> contient un attribut data-email avec l'objet JSON complet
     * pour l'interaction JavaScript.
     *
     * @param array $emails Liste des tableaux associatifs d'emails (clés: sender, time, subject, snippet).
     * @return string Le code HTML de la liste (<li>...</li>).
     */
    private function generateEmailListHTML(array $emails): string
    {
        $html = '';
        foreach ($emails as $index => $email) {
            $isSelected = ($index === 0) ? 'selected' : '';
            $isRead = ($index !== 0 && $index !== 1) ? 'read' : ''; // Simuler le non lu pour le deuxième

            // Encodage JSON des données pour que le JS puisse les récupérer lors du clic
            $emailDataJson = htmlspecialchars(json_encode($email), ENT_QUOTES, 'UTF-8');

            $html .= "
                <li class='email-item $isSelected $isRead' data-email='{$emailDataJson}'>
                    <div class='email-header'>
                        <span class='sender'>{$email['sender']}</span>
                        <span class='time'>{$email['time']}</span>
                    </div>
                    <div class='subject'>{$email['subject']}</div>
                    <div class='snippet'>{$email['snippet']}</div>
                </li>
            ";
        }
        return $html;
    }

    /**
     * Génère le contenu HTML du panneau de lecture.
     *
     * Affiche le détail d'un email ou un message par défaut si aucun email n'est fourni.
     *
     * @param array|null $email Les données de l'email à afficher ou null.
     * @return string Le code HTML du panneau de lecture.
     */
    private function generateReadingPaneHTML(?array $email): string
    {
        if (!$email) {
            return '<div class="placeholder">Sélectionnez un message à lire.</div>';
        }

        return "
            <h2>{$email['subject']}</h2>
            <div class='recipient-info'>
                <strong>De :</strong> {$email['sender']} <br>
                <strong>À :</strong> noname@noname.com
            </div>
            <div class='email-content'>
                {$email['content']}
            </div>
        ";
    }
}