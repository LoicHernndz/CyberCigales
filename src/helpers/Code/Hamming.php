<?php

namespace helpers\Code;

/**
 * Classe pour gérer le carré de Hamming (4,9)
 * 
 * Le carré de Hamming est un code correcteur d'erreur qui permet de détecter
 * et corriger une erreur sur 9 bits. Il est composé de :
 * - 4 bits de données (positions [0,0], [0,1], [1,0], [1,1])
 * - 5 bits de parité calculés pour vérifier la cohérence
 * 
 * Structure du carré 3x3 :
 * [d1, d2, p1]
 * [d3, d4, p2]
 * [p3, p4, p5]
 */
class Hamming
{

    private const key = "
    MON NOM EST ALEXANDRE SCHMIDT. JSP CE QU'IL SE PASSE. TOUT EST CHIFFRÉ, ILS ME DEMANDENT DE L'ARGENT. J'AI UNE CLE A ENVOYER MAIS JE DOIS PAYER POUR LA DECRYPTER : AAAAAAA";
    /**
     * Génère un carré de Hamming valide pour 4 bits de données donnés
     * 
     * @param array $dataBits Les 4 bits de données [bit1, bit2, bit3, bit4]
     * @return array Carré 3x3 avec les bits de parité calculés
     *               Format : [[d1, d2, p1], [d3, d4, p2], [p3, p4, p5]]
     */
    public static function generateSquare(array $dataBits): array
    {
        // Structure du carré 3x3 :
        // [d1, d2, p1]
        // [d3, d4, p2]
        // [p3, p4, p5]
        
        // Extraire les 4 bits de données
        $d1 = $dataBits[0] ?? 0;
        $d2 = $dataBits[1] ?? 0;
        $d3 = $dataBits[2] ?? 0;
        $d4 = $dataBits[3] ?? 0;
        
        // Calculer les bits de parité
        // p1 : parité de la ligne 1 (d1 + d2)
        $p1 = ($d1 + $d2) % 2;
        
        // p2 : parité de la ligne 2 (d3 + d4)
        $p2 = ($d3 + $d4) % 2;
        
        // p3 : parité de la colonne 1 (d1 + d3)
        $p3 = ($d1 + $d3) % 2;
        
        // p4 : parité de la colonne 2 (d2 + d4)
        $p4 = ($d2 + $d4) % 2;
        
        // p5 : parité carré (d1 + d2 + d3 + d4)
        $p5 = ($p1 + $p2) % 2;
        
        return [
            [$d1, $d2, $p1],
            [$d3, $d4, $p2],
            [$p3, $p4, $p5]
        ];
    }
    
    /**
     * Génère un carré de Hamming aléatoire
     * 
     * @return array Tableau associatif contenant :
     *               - 'square' : carré 3x3 complet avec bits de parité
     *               - 'dataBits' : les 4 bits de données initiaux générés
     */
    public static function generateRandomSquare(): array
    {
        // Générer 4 bits aléatoires
        $dataBits = [
            rand(0, 1),
            rand(0, 1),
            rand(0, 1),
            rand(0, 1)
        ];
        
        $square = self::generateSquare($dataBits);
        
        return [
            'square' => $square,
            'dataBits' => $dataBits
        ];
    }
    
    /**
     * Génère un carré de Hamming avec une erreur (un bit inversé)
     * 
     * @param array|null $square Carré existant (optionnel, sinon génère un aléatoire)
     * @return array Tableau associatif contenant :
     *               - 'square' : carré 3x3 avec un bit inversé
     *               - 'errorPosition' : position de l'erreur ['row' => x, 'col' => y]
     *               - 'originalSquare' : carré original correct avant l'erreur
     */
    public static function generateSquareWithError(?array $square = null): array
    {
        // Si pas de carré fourni, en générer un aléatoire
        if ($square === null) {
            $result = self::generateRandomSquare();
            $square = $result['square'];
        }
        
        // Choisir une position aléatoire pour l'erreur (0-2 pour ligne, 0-2 pour colonne)
        $errorRow = rand(0, 2);
        $errorCol = rand(0, 2);
        
        // Copier le carré original
        $squareWithError = [
            [$square[0][0], $square[0][1], $square[0][2]],
            [$square[1][0], $square[1][1], $square[1][2]],
            [$square[2][0], $square[2][1], $square[2][2]]
        ];
        
        // Inverser le bit à la position choisie
        $squareWithError[$errorRow][$errorCol] = ($squareWithError[$errorRow][$errorCol] == 1) ? 0 : 1;
        
        return [
            'square' => $squareWithError,
            'errorPosition' => ['row' => $errorRow, 'col' => $errorCol],
            'originalSquare' => $square
        ];
    }

    public static function showKey(int $progress) {

    }
}

