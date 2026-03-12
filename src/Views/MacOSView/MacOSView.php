<?php

namespace Views\MacOSView;

/**
 * Vue de l'interface MacOS simulée
 *
 * Affiche une interface desktop macOS avec dock, barre de menu et fenêtres.
 */
class MacOSView
{

    /** @var string Chemin vers le template HTML du bureau macOS */
    private const TEMPLATE_HTML = __DIR__ . '/home.html';

    /**
     * Renvoie le chemin du template HTML
     *
     * @return string
     */
    public function templatePath(): string
    {
        return self::TEMPLATE_HTML;
    }

    /**
     * Affiche la page complète du bureau macOS (header + body + footer)
     */
    function render()
    {
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    /**
     * Charge et affiche le template HTML du bureau macOS
     *
     * Si le fichier template n'existe pas, affiche un contenu par défaut.
     */
    function renderBody(): void
    {
        if (file_exists($this->templatePath())) {
            $template = file_get_contents($this->templatePath());

            $keys = method_exists($this, 'templateKeys') ? $this->templateKeys() : [];
            foreach ($keys as $key => $value) {
                $template = str_replace("{{{$key}}}", $value, $template);
            }

            echo $template;
        } else {
            echo '
            <div class="absolute inset-0 z-0 flex items-center justify-center text-4xl font-bold text-white/50 pointer-events-none">
                Bureau macOS Simulé
            </div>
            ';
        }
    }

    /**
     * Renvoie les clés de template (vide par défaut)
     *
     * @return array<string, string>
     */
    function templateKeys(): array
    {
        return [];
    }

    /**
     * Affiche le header HTML depuis le template
     */
    function renderHeader(): void
    {
        echo file_get_contents(__DIR__ . '/macos-header.html');
    }

    /**
     * Affiche le footer HTML depuis le template
     */
    function renderFooter(): void
    {
        echo file_get_contents(__DIR__ . '/macos-footer.html');
    }
}
