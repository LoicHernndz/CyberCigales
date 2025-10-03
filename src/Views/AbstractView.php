<?php
namespace Views;
abstract class AbstractView {
    function renderBody(): void
    {
        $template = file_get_contents($this->templatePath());

        foreach($this->templateKeys() as $key => $value){
            $template = str_replace("{{{$key}}}", $value, $template);
        }

        echo $template ;
    }
    abstract function templatePath() : string ;
    /** 
     * @return array<string, string>
     */
    abstract function templateKeys() : array ;
    function render(){
        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    function renderHeader(): void
    {
        /**
         * Affiche le header de la page
         */
        echo '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Login System</title>
        <link rel="stylesheet" href="/../styles/main.css" type="text/css">
    </head>
    <body>
        <nav>
            <ul>
                <a href="/"><li>Accueil</li></a>
            ';
        if(!isset($_SESSION['user_id'])) :
            echo '<a href="src/Views/user/SignupView.php"><li>Inscription</li></a>
                    <a href="/user/login"><li>Connexion</li></a>';
        else :
            echo '<a href="../controllers/Users.php?q=logout"><li>Déconnexion</li></a>';
        endif;
        echo '
            </ul>
        </nav>';
    }
    function renderFooter(): void
    {
        /**
         * Affiche le footer de la page
         */
        echo '    <div id="footer">
                <p>&copy; CyberCigales';
        echo date("Y");
        echo '</p>
            </div>
            
        </body>
    </html>';
    }

}