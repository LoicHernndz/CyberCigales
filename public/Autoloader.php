<?php

namespace public;
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}

Autoloader::register();