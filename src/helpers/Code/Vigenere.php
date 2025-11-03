<?php

namespace helpers\Code;

class Vigenere extends AbstractCode
{
    public static function encrypt($text, $key=""){
        $text = self::cleanText($text);
        $key = self::cleanText($key);
        $code = "";
        for ($i = 0; $i < strlen($text); ++$i){
            if (!is_numeric($text[$i])) {
                $position = ord($text[$i]) - ord('A');                          // position ASCII du char a traiter
                $decalage = ord($key[$i % strlen($key)]) - ord('A');            // decalage par rapport au char actuel de la cle
                $code .= chr(($position + $decalage)%26 + ord('A'));   // ajout du nouveau caractere code
            }
            else {
                $code .= $text[$i];
            }
        }
        return $code;
    }

    public static function decrypt($text, $key=""){
        $text = self::cleanText($text);
        $key = self::cleanText($key);
        $code = "";
        for ($i = 0; $i < strlen($text); ++$i){
            if (!is_numeric($text[$i])) {
                $position = ord($text[$i]) - ord('A');                          // position ASCII du char a traiter
                $decalage = ord($key[$i % strlen($key)]) - ord('A');            // decalage par rapport au char actuel de la cle
                if ($decalage < 0)
                    $code .= chr(($position - $decalage + 26)%26 + ord('A'));   // ajout du nouveau caractere code
                else
                    $code .= chr(($position - $decalage)%26 + ord('A'));
            }
            else {
                $code .= $text[$i];
            }
        }
        return $code;
    }

    public static function verification($texte, $texte_chiffre_a_verifier, $method, $key="") {
        $texte_chiffre_a_verifier = self::cleanText($texte_chiffre_a_verifier);
        if (self::$method($texte, $key) == strtoupper($texte_chiffre_a_verifier)) {
            $resultat = "Le chiffrement est correct.";
            flash('Vigenere', $resultat, 'form-message form-message-green');
            return true;
        } else {
            $resultat = "Le chiffrement est incorrect. Le bon résultat est : " . self::$method($texte, $key);
            flash('Vigenere', $resultat);
            return false;
        }
    }
}