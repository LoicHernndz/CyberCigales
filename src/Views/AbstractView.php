<?php
namespace Views;

/**
 * Class abstract contenant les methodes communes a toutes views, par exemple pour avoir le meme footer et header
 */
abstract class AbstractView
{
    /**
     * Recupere le contenu du fichier html associe a la vue pour l'afficher.
     * Dans le fichier html, toutes les parties entre accolades (ex : {FOO}) seront remplaces par de vrais elements html passe à travers la methode templateKeys().
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());

        // Fusionner les clés URL communes avec les clés spécifiques à la vue
        $allKeys = array_merge($this->commonUrlKeys(), $this->templateKeys());

        foreach ($allKeys as $key => $value) {
            // Convertir en string pour éviter les problèmes de type
            $value = (string) $value;
            // Remplacer toutes les occurrences de la clé
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        // Nettoyer les accolades orphelines qui pourraient rester (sécurité)
        $template = preg_replace('/\{\{[A-Z_]+\}\}/', '', $template);

        echo $template;
    }

    /**
     * Renvoie le chemin du fichier html template associe a la view
     */
    abstract function templatePath(): string;

    /**
     * Renvoie une liste des elements dynamiques a ajouter au fichier statique html (exemple : nom d'utilisateur dans la hompage apres s'etre connecte)
     */
    abstract function templateKeys(): array;

    /**
     * Clés URL communes disponibles dans tous les templates HTML.
     */
    protected function commonUrlKeys(): array
    {
        return [
            'URL_HOME'              => url('homepage'),
            'URL_DASHBOARD'         => url('dashboard'),
            'URL_LOGIN'             => url('user_login'),
            'URL_SIGNUP'            => url('user_signup'),
            'URL_LOGOUT'            => url('user_logout'),
            'URL_PROFIL'            => url('user_profil'),
            'URL_EDIT'              => url('user_edit'),
            'URL_RESET_PASSWORD'    => url('user_reset_password'),
            'URL_LECON'             => url('lecon_index'),
            'URL_OUTILS'            => url('outils'),
            'URL_MINIGAMES'         => url('minigames'),
            'URL_INSTAGRAM'         => url('instagram'),
            'URL_MACOS'             => url('macos'),
            'URL_MENTIONS'          => url('mentions'),
            'URL_PLAN'              => url('plan'),
            'URL_LECON_CESAR'       => url('lecon_cesar'),
            'URL_LECON_HIST_MDP'    => url('lecon_hist_mdp'),
            'URL_LECON_VIGENERE'    => url('lecon_vigenere'),
            'URL_LECON_PERMUTATION' => url('lecon_permutation'),
            'URL_LECON_RGPD'        => url('lecon_rgpd'),
            'URL_GAME_HAMMING'      => url('game_hamming'),
            'URL_GAME_FREQUENCY'    => url('game_frequency'),
            'URL_GAME_PHISHING'     => url('game_phishing'),
            'URL_GAME_RESET'        => url('game_reset_game'),
            'URL_CODE_CHIFFREMENT_CESAR'       => url('code_chiffrement_cesar'),
            'URL_CODE_CHIFFREMENT_VIGENERE'    => url('code_chiffrement_vigenere'),
            'URL_CODE_CHIFFREMENT_PERMUTATION' => url('code_chiffrement_permutation'),
            'URL_CODE_OUTIL_PERMUTATION'       => url('code_outil_permutation'),
            'URL_CODE_DECHIFFREMENT_CESAR'       => url('code_dechiffrement_cesar'),
            'URL_CODE_DECHIFFREMENT_VIGENERE'    => url('code_dechiffrement_vigenere'),
            'URL_CODE_DECHIFFREMENT_PERMUTATION' => url('code_dechiffrement_permutation'),
        ];
    }

    /**
     * Affiche la page dans son entierete, footer + contenu (fichier html) + header
     *
     * @return void
     */
    function render()
    {
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Affiche le header depuis le template HTML
     */
    function renderHeader(): void
    {
        $templateDir = __DIR__ . '/templates/';
        $header = file_get_contents($templateDir . 'header.html');

        $navFile = isset($_SESSION['user_id']) ? 'nav-logged.html' : 'nav-guest.html';
        $navContent = file_get_contents($templateDir . $navFile);

        $keys = array_merge(
            [
                'NAV_LINKS'  => $navContent,
                'LOGO_HREF'  => isset($_SESSION['user_id']) ? url('dashboard') : url('homepage'),
                'EXTRA_HEAD' => $this->extraHeadContent(),
            ],
            $this->commonUrlKeys()
        );

        foreach ($keys as $key => $value) {
            $header = str_replace('{{' . $key . '}}', (string)$value, $header);
        }

        echo $header;
    }

    /**
     * Contenu supplémentaire à injecter dans le <head>.
     * Les vues enfants peuvent surcharger cette méthode.
     */
    protected function extraHeadContent(): string
    {
        return '';
    }

    /**
     * Affiche le footer depuis le template HTML
     */
    function renderFooter(): void
    {
        $footer = file_get_contents(__DIR__ . '/templates/footer.html');

        $keys = array_merge(
            ['YEAR' => date('Y')],
            $this->commonUrlKeys()
        );

        foreach ($keys as $key => $value) {
            $footer = str_replace('{{' . $key . '}}', (string)$value, $footer);
        }

        echo $footer;
    }

}
