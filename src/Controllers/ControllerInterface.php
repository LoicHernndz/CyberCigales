<?php
namespace controllers;

interface ControllerInterface {
    function control() ; 
    static function support(string $chemin, string $method) : bool; 
}