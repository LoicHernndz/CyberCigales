<?php
namespace Views\InterfaceAgenda;

use Couchbase\View;

/**
 * Classe InterfaceAgendaView
 *
 * Gère l'affichage de la vue pour l'interface de l'agenda.
 * Cette classe est responsable du chargement et de l'affichage du template HTML associé.
 *
 * @package Views\InterfaceAgenda
 */
class InterfaceAgendaView {

    /**
     * Chemin absolu vers le fichier de template HTML.
     * @var string
     */
    private const TEMPLATE_HTML = __DIR__ . '/interface-agenda.html';

    /**
     * Récupère le chemin du fichier template.
     *
     * @return string Le chemin absolu vers le fichier template.
     */
    public function templatePath() : string {
        return self::TEMPLATE_HTML;
    }

    /**
     * Point d'entrée pour effectuer le rendu de la vue.
     *
     * Cette méthode délègue l'affichage à renderBody().
     *
     * @return void
     */
    function render(){
        $this->renderBody();
    }

    /**
     * Charge et affiche le contenu du template HTML.
     *
     * Vérifie l'existence du fichier avant de l'inclure.
     * Si le fichier est introuvable :
     * 1. Une erreur est enregistrée dans les logs (error_log).
     * 2. Un message d'erreur visuel est affiché à l'utilisateur.
     *
     * @return void
     */
    function renderBody(): void
    {
        $templatePath = $this->templatePath();

        if (file_exists($templatePath)) {
            $template = file_get_contents($templatePath);

            echo $template;
        } else {
            error_log("Erreur critique [InterfaceAgendaView] : Le fichier template est introuvable au chemin : " . $templatePath);

            echo "<div style='color: red; padding: 20px; border: 1px solid red; background: #ffeeee;'>
                    <strong>Erreur système :</strong> Impossible de charger l'interface agenda (Template manquant). 
                    Veuillez contacter l'administrateur.
                  </div>";
        }
    }
}