<?php

namespace helpers;

class GenerateAnswer
{
    public function control()
    {
        $name = $_REQUEST["name"];
        $step = $_REQUEST["step"];
        $message = $_REQUEST["message"];

        $this->generate($name, $step, $message);
    }
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