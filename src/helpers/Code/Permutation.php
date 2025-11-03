<?php

namespace helpers\Code;

class Permutation extends AbstractCode
{
    public static function encrypt($text, $key=null, $space_char=null){

        $n_columns = strlen($key);

        $text = trim($text);
        $text = str_replace(" ", $space_char, $text);
        $text = self::cleanText($text);

        $n_rows = ceil(strlen($text) / $n_columns);
        $text .= str_repeat(mb_strtolower($space_char), $n_columns * $n_rows - strlen($text));
        $order = self::getOrderFromKey($key);

        $code = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $current_pos = ($i % $n_rows) * $n_columns + $order[floor($i/$n_rows)];   // Formule pour lire de haut en bas plutôt que de gauche à droite
            $code .= $text[$current_pos];
        }

        return $code;

    }

    public static function decrypt($text, $key=null, $space_char=null)
    {

        $text = strtolower($text);
        $n_columns = strlen($key);

        $n_rows = ceil(strlen($text) / $n_columns);
        $order = self::getOrderFromKey($key);
        $order = array_flip($order);

        $code = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $current_pos = $order[$i%$n_columns] * $n_rows + floor($i/$n_columns);   // Formule pour lire de haut en bas plutôt que de gauche à droite
            $code .= $text[$current_pos];
        }
        var_dump($code, $text);
        return $code;

    }

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