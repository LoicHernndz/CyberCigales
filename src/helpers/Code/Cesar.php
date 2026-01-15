<?php

namespace helpers\Code;

/**
 * Algorithme de chiffrement César (ROT13 par défaut)
 * Corrected Version : Case sensitive, preserves special chars.
 */
class Cesar extends AbstractCode
{
    /**
     * Chiffre un texte avec l'algorithme César
     * 
     * @param string $text Le texte à chiffrer
     * @param int $decalage Le décalage (défaut 13)
     * @return string Le texte chiffré
     */
    public static function encrypt($text, $decalage = 13)
    {
        $text_chiffre = '';

        // Normalisation du décalage pour qu'il soit toujours positif entre 0 et 25
        $decalage = $decalage % 26;
        if ($decalage < 0)
            $decalage += 26;

        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];

            if (ctype_alpha($char)) {
                // Détection de la casse (Majuscule ou Minuscule)
                $isUpper = ctype_upper($char);
                $asciiStart = $isUpper ? 65 : 97; // 65 for 'A', 97 for 'a'

                // Formule : (Position + decalage) % 26 + ASCII de base
                // ord($char) - $asciiStart donne la position de 0 à 25
                $newCharAscii = ((ord($char) - $asciiStart + $decalage) % 26) + $asciiStart;
                $text_chiffre .= chr($newCharAscii);
            } else {
                // Si ce n'est pas une lettre, on garde le caractère tel quel
                $text_chiffre .= $char;
            }
        }

        return $text_chiffre;
    }

    /**
     * Déchiffre un texte (Inverse de encrypt)
     */
    public static function decrypt($text, $decalage = 13)
    {
        return self::encrypt($text, -$decalage);
    }

    /**
     * Vérifie si le chiffrement/déchiffrement correspont à l'attendu.
     * Note: Cette vérification est stricte sur la casse et les caractères.
     */
    public static function verification($text, $texte_chiffre_a_verifier, $method, $decalage = 13)
    {
        $resultat_calcule = self::$method($text, $decalage);

        if ($resultat_calcule === $texte_chiffre_a_verifier) {
            flash('Cesar', "Le chiffrement est correct.", 'form-message form-message-green');
            return true;
        } else {
            flash('Cesar', "Résultat incorrect. Attendu : " . $resultat_calcule);
            return false;
        }
    }

    /**
     * Exécute les tests unitaires demandés
     * @return array Résultat des tests
     */
    public static function runTests()
    {
        $tests = [
            ['input' => 'ABC', 'k' => 13, 'expected' => 'NOP'],
            ['input' => 'NOP', 'k' => 13, 'expected' => 'ABC'], // car ROT13 est son propre inverse
            ['input' => 'RQQR', 'k' => 13, 'expected' => 'EDDE'],
            ['input' => "J'aime le Php !", 'k' => 13, 'expected' => "W'nvzr yr Cuc !"], // Test majuscules + ponctuation
            ['input' => 'CyberCigales', 'k' => 1, 'expected' => 'DzcfsDjhbmft'] // Test décalage 1
        ];

        $results = [];
        foreach ($tests as $test) {
            $output = self::encrypt($test['input'], $test['k']);
            $pass = ($output === $test['expected']);
            $results[] = [
                'input' => $test['input'],
                'k' => $test['k'],
                'output' => $output,
                'expected' => $test['expected'],
                'status' => $pass ? 'PASS' : 'FAIL'
            ];
        }
        return $results;
    }
}
