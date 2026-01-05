<?php


namespace helpers;
use Models\Bash\Bash;

class BashRequest
{

    public function control()
    {
        $env = new Bash();

        $input = $_REQUEST["input"];
        $path = $_REQUEST["path"];

        $args = explode(" ", $input);

        $allowed = ["pwd", "ls"];
        $command = $args[0];
        if (in_array($command, $allowed)) {
            self::$command($env, $args, $path);
        }
    }

    public function ls($env, $args, $path)
    {
        $file = $env->find($path);
        return $file->getChildren();
    }

    public function cd($env, $args, $path)
    {

    }

    public function pwd($env, $args, $path)
    {
        echo '{"path":"' . $path . '","output":"' . $path . '"}';
    }
}