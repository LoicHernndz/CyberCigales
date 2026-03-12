<?php

namespace Services\Code;

/**
 * Algorithme de chiffrement César
 * 
 * Le chiffrement César décale chaque lettre de l'alphabet d'un nombre fixe de positions.
 * Par exemple, avec un décalage de 3, A devient D, B devient E, etc.
 */
class Cesar extends AbstractCode
{

    /**
     * Chiffre un texte avec l'algorithme César
     * 
     * Applique un décalage alphabétique à chaque lettre du texte.
     * Les chiffres et caractères spéciaux sont préservés.
     * 
     * @param string $text Le texte à chiffrer
     * @param int $decalage Le nombre de positions de décalage (peut être négatif)
     * @return string Le texte chiffré
     */
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

    /**
     * Déchiffre un texte chiffré avec l'algorithme César
     * 
     * Applique le décalage inverse pour retrouver le texte original.
     * 
     * @param string $text Le texte à déchiffrer
     * @param int $decalage Le décalage utilisé pour le chiffrement
     * @return string Le texte déchiffré
     */
    public static function decrypt($text, $decalage=0) {
        return self::encrypt($text, -$decalage);
    }

    /**
     * Vérifie si le chiffrement César est correct
     * 
     * Compare le résultat du chiffrement/déchiffrement avec la réponse attendue
     * et affiche un message de feedback via flash.
     * 
     * @param string $text Le texte original
     * @param string $texte_chiffre_a_verifier Le résultat attendu à vérifier
     * @param string $method La méthode à utiliser ('encrypt' ou 'decrypt')
     * @param int $decalage Le décalage à appliquer
     * @return bool True si le résultat est correct, false sinon
     */
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