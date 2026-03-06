<?php

namespace Views\Code\Hamming;

use Views\AbstractView;

class HammingView extends AbstractView
{

    private const FLASH_KEY = 'FLASH';
    private const GAME_KEY = 'GAME';
    private const SCRIPTS_KEY = 'GAME_SCRIPT';

    /** @var array Données du jeu (square, streak, target) */
    private array $data;

    /**
     * @param array $data Données du jeu :
     *                    - 'square' : array 3x3 du carré de Hamming
     *                    - 'streak' : int série de victoires actuelle
     *                    - 'target' : int objectif de victoires (défaut 5)
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return string Chemin vers hamming.html
     */
    function templatePath(): string
    {
        return __DIR__ . '/hamming.html';
    }

    /**
     * Génère les clés de template pour le HTML.
     *
     * @return array [
     *     'SQUARE_JSON' => string JSON du carré 3x3,
     *     'GAME_DATA' => string JSON {streak, target}
     * ]
     */
    function templateKeys(): array
    {

        if (!isset($this->data['square'])) {
            return [
                self::FLASH_KEY => flash('hamming'),
                self::GAME_KEY => '',
                self::SCRIPTS_KEY => ''
            ];
        }

        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        $streak = $this->data['streak'] ?? 0;
        $target = $this->data['target'] ?? 5;

        return [
            self::FLASH_KEY => flash('hamming'),
            self::GAME_KEY => file_get_contents(__DIR__ . '/game-area.html'),
            self::SCRIPTS_KEY =>
                '<script type="application/json" id="square-data">'.json_encode($square).'</script>
                <script type="application/json" id="game-data">'.json_encode(['streak' => $streak, 'target' => $target]).'</script>
                <script src="/assets/code/js/hamming.js"></script>'
        ];
    }

    /**
     * Charge et affiche le template avec remplacement des clés.
     */
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());

        foreach($this->templateKeys() as $key => $value){
            $template = str_replace("{{{" . $key . "}}}", (string)$value, $template);
        }

        echo $template;
    }

}