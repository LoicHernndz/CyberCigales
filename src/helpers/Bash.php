<?php

namespace helpers;

class Bash {
    public function control() {
        $input = $_REQUEST["input"];
        $args = explode(" ", $input);

        $allowed = ["ls", "cd"];
        $command = $args[0];
        if (in_array($command, $allowed)) {
            self::$command();
        }
    }

    public static function ls() {
    }

    public static function cd() {
    }
}