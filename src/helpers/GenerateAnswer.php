<?php

namespace helpers;

/**
 * Générateur de réponses contextuelles
 * 
 * Charge les réponses depuis answers.json et retourne la réponse appropriée
 * en fonction du nom, de l'étape et du message de l'utilisateur.
 */
class GenerateAnswer
{
    /**
     * Point d'entrée pour traiter les requêtes de génération de réponses
     * 
     * Récupère les paramètres de la requête et appelle generate().
     * 
     * @return void
     */
    public function control()
    {
        $name = $_REQUEST["name"];
        $step = $_REQUEST["step"];
        $message = $_REQUEST["message"];

        $this->generate($name, $step, $message);
    }
    
    /**
     * Génère une réponse contextuelle depuis answers.json
     * 
     * Si le message est vide, retourne le message de l'étape actuelle.
     * Si le message contient la clé attendue, passe à l'étape suivante.
     * Sinon, retourne une réponse aléatoire parmi les réponses disponibles.
     * 
     * @param string $name Le nom du contexte (ex: nom d'un exercice)
     * @param string $step L'étape actuelle
     * @param string $message Le message de l'utilisateur
     * @return void Affiche la réponse suivie d'un indicateur (0 ou 1)
     */
    private function generate($name, $step, $message): void
    {
        $path = __DIR__ . '/../config/answers.json';

        $string = file_get_contents($path);
        $json_a = json_decode($string);

        if (!isset($json_a->$name)){
            echo "";
        }
        else if ($message == "") {
            echo $json_a->$name->$step->{"message"};
            echo "0";
        }
        else if (isset($json_a->$name->$step->{"key"})  && str_contains($message, $json_a->$name->$step->{"key"})) {
            $next_step = strval((int)($step + 1));
            echo $json_a->$name->$next_step->{"message"};
            echo "1";
        } else {
            $responses = $json_a->$name->$step->{"responses"};
            echo $responses[array_rand($responses)];
            echo "0";
        }

    }

}