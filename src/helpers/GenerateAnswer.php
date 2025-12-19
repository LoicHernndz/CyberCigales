<?php

namespace helpers;

class GenerateAnswer
{
    public function control()
    {
        $name = $_REQUEST["name"];
        $message = $_REQUEST["message"];

        $this->generate($name, $message);
    }
    private function generate($name, $message): void
    {

        $path = __DIR__ . '/../config/answers.json';

        $string = file_get_contents($path);
        $json_a = json_decode($string);

        if (str_contains($message, $json_a->$name)) {
            echo "tru";
        }else{
            echo "???";
        }

    }

}