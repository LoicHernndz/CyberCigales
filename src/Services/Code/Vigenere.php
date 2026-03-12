<?php

namespace Services\Code;

/**
 * Algorithme de chiffrement Vigenère
 * 
 * Le chiffrement Vigenère utilise une clé alphabétique répétée pour chiffrer le texte.
 * Chaque lettre de la clé détermine le décalage à appliquer à la lettre correspondante du texte.
 */
class Vigenere extends AbstractCode
{
    /**
     * Chiffre un texte avec l'algorithme de Vigenère
     * 
     * Utilise une clé alphabétique qui se répète pour déterminer le décalage de chaque lettre.
     * Les chiffres sont préservés, les lettres sont converties en majuscules dans le résultat.
     * 
     * @param string $text Le texte à chiffrer
     * @param string $key La clé de chiffrement (alphabétique)
     * @return string Le texte chiffré en majuscules
     */
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

    /**
     * Déchiffre un texte chiffré avec l'algorithme de Vigenère
     * 
     * Applique le processus inverse du chiffrement en soustrayant les décalages
     * déterminés par la clé répétée.
     * 
     * @param string $text Le texte à déchiffrer
     * @param string $key La clé de déchiffrement (même que celle utilisée pour le chiffrement)
     * @return string Le texte déchiffré en majuscules
     */
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

    /**
     * Vérifie si le chiffrement/déchiffrement Vigenère est correct
     * 
     * Compare le résultat avec la réponse attendue et affiche un message de feedback.
     * 
     * @param string $texte Le texte original
     * @param string $texte_chiffre_a_verifier Le résultat attendu à vérifier
     * @param string $method La méthode à utiliser ('encrypt' ou 'decrypt')
     * @param string $key La clé de chiffrement/déchiffrement
     * @return bool True si le résultat est correct, false sinon
     */
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