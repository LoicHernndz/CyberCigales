<?php

namespace helpers\Code;

/**
 * Classe abstraite pour les algorithmes de chiffrement
 * 
 * Fournit des méthodes communes pour le chiffrement, le déchiffrement et la vérification.
 * Les classes enfants doivent implémenter les algorithmes spécifiques (César, Vigenère, Permutation).
 */
abstract class AbstractCode
{
    /**
     * Normalise le texte pour le chiffrement
     * 
     * Convertit en minuscules, remplace les accents par des lettres simples,
     * et supprime tous les caractères non alphanumériques.
     * 
     * @param string $text Le texte à normaliser
     * @return string Le texte normalisé (minuscules, sans accents, alphanumériques uniquement)
     */
    protected static function cleanText($text){
        $text = \mb_strtolower($text);
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
    
    /**
     * Chiffre un texte avec l'algorithme spécifique
     * 
     * @param string $text Le texte à chiffrer
     * @return string Le texte chiffré
     */
    abstract public static function encrypt($text);
    
    /**
     * Déchiffre un texte avec l'algorithme spécifique
     * 
     * @param string $text Le texte à déchiffrer
     * @return string Le texte déchiffré
     */
    abstract public static function decrypt($text);
    
    /**
     * Vérifie si un texte chiffré correspond au résultat attendu
     * 
     * @param string $text Le texte original
     * @param string $texte_chiffre_a_verifier Le texte chiffré à vérifier
     * @param string $method La méthode à utiliser ('encrypt' ou 'decrypt')
     * @return bool True si la vérification réussit, false sinon
     */
    abstract public static function verification($text, $texte_chiffre_a_verifier, $method);

}