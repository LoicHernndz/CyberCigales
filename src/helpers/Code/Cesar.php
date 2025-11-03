<?php

namespace helpers\Code;

class Cesar extends AbstractCode
{

    // Fonction de chiffrement César
    public static function encrypt($text, $decalage=0) {

        $text = self::cleanText($text);
        $text_chiffre = '';
        if ($decalage < 0) {
            $decalage = $decalage % 26 + 26;
        } else {
            $decalage = $decalage % 26;
        }

        for ($i = 0; $i < strlen($text); $i++) {
            $caractere = $text[$i];

            if (ctype_alpha($caractere)) {
                $base = ctype_lower($caractere) ? 'a' : 'A';
                $caractere = chr((ord($caractere) - ord($base) + $decalage) % 26 + ord($base));
            }

            $text_chiffre .= $caractere;
        }

        return $text_chiffre;
    }

    public static function decrypt($text, $decalage=0) {
        return self::encrypt($text, -$decalage);
    }

    // Fonction de vérification du chiffrement
    public static function verification($text, $texte_chiffre_a_verifier, $method, $decalage=0) {
        $texte_chiffre_a_verifier = self::cleanText($texte_chiffre_a_verifier);
        if (self::$method($text, $decalage) == strtolower($texte_chiffre_a_verifier)) {
            $resultat = "Le chiffrement est correct.";
            flash('Cesar', $resultat, 'form-message form-message-green');
            return true;
        } else {
            $resultat = "Le chiffrement est incorrect. Le bon résultat est : " . self::$method($text, $decalage);
            flash('Cesar', $resultat);
            return false;
        }
    }
}