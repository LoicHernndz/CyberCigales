<?php

namespace Views\InterfaceWeb;

/**
 * Class InterfaceWebView
 *
 * Gère l'affichage de la vue pour l'interface de simulation de navigateur web (Safari).
 * Cette classe charge un template HTML séparé et y injecte dynamiquement l'URL
 * et le contenu de la page demandée.
 *
 * @package Views\InterfaceWeb
 */
class InterfaceWebView {

    /**
     * Chemin absolu vers le fichier de template HTML associé à cette vue.
     * @var string
     */
    private const TEMPLATE_HTML = __DIR__ . '/interface-web.html';

    /**
     * Retourne le chemin vers le fichier de template.
     *
     * @return string Le chemin complet du fichier .html
     */
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    /**
     * Méthode principale pour générer et afficher l'interface.
     *
     * Elle agit comme une façade pour la méthode renderBody.
     *
     * @param string $url     L'URL à afficher dans la barre d'adresse du navigateur simulé.
     * @param string $content Le contenu HTML (le corps de la page) à injecter dans la zone principale.
     * @return void
     */
    function render(string $url, string $content){
        $this->renderBody($url, $content);
    }

    /**
     * Charge le template, remplace les placeholders et affiche le résultat final.
     *
     * Cette méthode lit le fichier HTML défini dans TEMPLATE_HTML, cherche les balises
     * {{URL_VALUE}} et {{CONTENT_VALUE}} pour les remplacer par les vraies données,
     * puis envoie le tout au navigateur via echo.
     *
     * @param string $url     L'URL textuelle à insérer dans l'input de la barre d'adresse.
     * @param string $content Le code HTML à insérer dans la zone de contenu.
     * @return void
     */
    function renderBody(string $url, string $content): void
    {
        $templatePath = $this->templatePath();

        if (file_exists($templatePath)) {
            // Récupération du contenu brut du fichier HTML
            $template = file_get_contents($templatePath);

            // Remplacement des variables de template par les valeurs dynamiques
            $template = str_replace('{{URL_VALUE}}', $url, $template);
            $template = str_replace('{{CONTENT_VALUE}}', $content, $template);

            // Affichage du rendu final
            echo $template;
        } else {
            // Gestion d'erreur basique si le fichier n'est pas trouvé
            echo "Erreur : Le fichier template est introuvable au chemin : " . $templatePath;
        }
    }
}