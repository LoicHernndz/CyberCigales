<?php

function url(string $name): string
{
    return \config\RouteRegistry::url($name);
}
