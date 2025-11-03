<?php

namespace helpers\Code;

abstract class AbstractCode
{
    protected static function cleanText($text){
        $text = mb_strtolower($text);
        $remplacements = [
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'à' => 'a',
            'ù' => 'u',
            'ç' => 'c',
            'î' => 'i',
            'ô' => 'o',
        ];
        $text = strtr($text, $remplacements);
        $text = preg_replace('/[^a-z0-9]/', '',  $text);
        return $text;
    }
    abstract public static function encrypt($text);
    abstract public static function decrypt($text);
    abstract public static function verification($text, $texte_chiffre_a_verifier, $method);

}