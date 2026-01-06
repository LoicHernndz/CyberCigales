<?php
namespace Views\Game\Hamming;

use Views\AbstractView;

/**
 * Vue pour le mini-jeu Hamming (avec Ajax)
 */
class HammingView extends AbstractView
{
    private array $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    function templatePath(): string
    {
        return __DIR__ . '/hamming.html';
    }
    
    function templateKeys(): array
    {
        $square = $this->data['square'] ?? [[0,0,0],[0,0,0],[0,0,0]];
        $streak = $this->data['streak'] ?? 0;
        $target = $this->data['target'] ?? 5;
        
        $squareJson = json_encode($square);
        $gameData = json_encode([
            'streak' => $streak,
            'target' => $target
        ]);
        
        return [
            'SQUARE_JSON' => $squareJson,
            'GAME_DATA' => $gameData
        ];
    }
    
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());
        
        foreach($this->templateKeys() as $key => $value){
            $value = (string)$value;
            $template = str_replace("{{{" . $key . "}}}", $value, $template);
        }
        
        echo $template;
    }
    
    function renderHeader(): void
    {
        $logoHref = isset($_SESSION['user_id']) ? '/dashboard' : '/';
        
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hamming Rush - CyberCigales</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="/styles/main.css?v=5" type="text/css">
        <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    </head>
    <body>';
    }
    
    function renderFooter(): void
    {
        echo '
    </body>
</html>';
    }
}
