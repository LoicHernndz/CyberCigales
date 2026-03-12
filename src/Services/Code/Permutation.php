<?php

namespace Services\Code;

/**
 * Algorithme de chiffrement par permutation (chiffrement par colonnes)
 * 
 * Le texte est écrit en lignes selon la longueur de la clé, puis lu colonne par colonne
 * dans l'ordre déterminé par le tri alphabétique de la clé.
 */
class Permutation extends AbstractCode
{
    /**
     * Chiffre un texte avec l'algorithme de permutation par colonnes
     * 
     * Le texte est organisé en grille avec autant de colonnes que la longueur de la clé.
     * Les colonnes sont ensuite lues dans l'ordre déterminé par le tri alphabétique de la clé.
     * 
     * @param string $text Le texte à chiffrer
     * @param string $key La clé déterminant l'ordre des colonnes
     * @param string $space_char Le caractère utilisé pour remplacer les espaces
     * @return string Le texte chiffré
     */
    public static function encrypt($text, $key=null, $space_char=null){

        $n_columns = strlen($key);

        $text = trim($text);
        $text = str_replace(" ", $space_char, $text);
        $text = self::cleanText($text);

        $n_rows = (int) ceil(strlen($text) / $n_columns);
        $text .= str_repeat(\mb_strtolower($space_char), $n_columns * $n_rows - strlen($text));
        $order = self::getOrderFromKey($key);

        $code = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $current_pos = ($i % $n_rows) * $n_columns + $order[(int) floor($i/$n_rows)];   // Formule pour lire de haut en bas plutôt que de gauche à droite
            $code .= $text[$current_pos];
        }

        return $code;

    }

    /**
     * Déchiffre un texte chiffré par permutation
     * 
     * Inverse le processus de chiffrement en réorganisant les colonnes selon la clé.
     * 
     * @param string $text Le texte à déchiffrer
     * @param string $key La clé utilisée pour le chiffrement
     * @param string $space_char Le caractère utilisé pour les espaces
     * @return string Le texte déchiffré
     */
    public static function decrypt($text, $key=null, $space_char=null)
    {

        $text = strtolower($text);
        $n_columns = strlen($key);

        $n_rows = (int) ceil(strlen($text) / $n_columns);
        $order = self::getOrderFromKey($key);
        $order = array_flip($order);

        $code = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $current_pos = $order[$i % $n_columns] * $n_rows + (int) floor($i / $n_columns);   // Formule pour lire de haut en bas plutôt que de gauche à droite
            $code .= $text[$current_pos];
        }

        return $code;

    }

    /**
     * Vérifie si le chiffrement/déchiffrement par permutation est correct
     * 
     * Compare le résultat avec la réponse attendue et affiche un message de feedback.
     * 
     * @param string $text Le texte original
     * @param string $texte_chiffre_a_verifier Le résultat attendu à vérifier
     * @param string $method La méthode à utiliser ('encrypt' ou 'decrypt')
     * @param string $key La clé de chiffrement/déchiffrement
     * @param string $space_char Le caractère utilisé pour les espaces
     * @return bool True si le résultat est correct, false sinon
     */
    public static function verification($text, $texte_chiffre_a_verifier, $method, $key=null, $space_char=null)
    {
        $texte_chiffre_a_verifier = self::cleanText($texte_chiffre_a_verifier);
        if (self::$method($text, $key, $space_char) == strtolower($texte_chiffre_a_verifier)) {
            $resultat = "Le chiffrement est correct.";
            flash('Permutation', $resultat, 'form-message form-message-green');
            return true;
        } else {
            $resultat = "Le chiffrement est incorrect. Le bon résultat est : " . self::$method($text, $key, $space_char);
            flash('Permutation', $resultat);
            return false;
        }
    }

    /**
     * Calcule l'ordre de lecture des colonnes à partir de la clé
     * 
     * Trie alphabétiquement les lettres de la clé pour déterminer dans quel ordre
     * les colonnes doivent être lues lors du chiffrement.
     * 
     * @param string $key La clé de chiffrement
     * @return array Tableau associatif indiquant l'ordre de lecture de chaque colonne
     */
    private static function getOrderFromKey($key)
    {
        $key = self::cleanText($key);
        $ordered = str_split($key);
        sort($ordered);
        $ordered = implode('', $ordered);


        $order = [];
        for ($i = 0; $i < strlen($key); ++$i) {
            $order[$i] = strpos($ordered, $key[$i]);
        }

        return $order;
    }
}