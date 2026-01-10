<?php

namespace Controllers\Code\Permutation;

use Controllers\AbstractController;

/**
 * Contrôleur pour l'outil de Permutation dans l'interface MacOS.
 */
class PermutationTool extends AbstractController
{
    /**
     * Affiche l'interface de l'outil.
     */
    public function getMethod()
    {
        $templatePath = __DIR__ . '/../../../Views/Code/Permutation/permutation-tool.html';
        
        if (file_exists($templatePath)) {
            echo file_get_contents($templatePath);
        } else {
            echo "Erreur : Template introuvable.";
        }
    }

    /**
     * Traite les requêtes AJAX de chiffrement/déchiffrement.
     */
    public function postMethod()
    {
        // Nettoyer le buffer de sortie
        if (ob_get_level()) ob_clean();
        
        header('Content-Type: application/json; charset=utf-8');

        try {
            $action = $_POST['action'] ?? '';
            $text = $_POST['text'] ?? '';
            $key = $_POST['key'] ?? '';
            $spaceChar = $_POST['space_char'] ?? '_';

            // Validation
            if (empty($text)) {
                echo json_encode(['success' => false, 'message' => 'Le texte est requis.']);
                exit;
            }

            if (empty($key)) {
                echo json_encode(['success' => false, 'message' => 'La clé est requise.']);
                exit;
            }

            // Traitement
            if ($action === 'encrypt') {
                $result = $this->encrypt($text, $key, $spaceChar);
                echo json_encode(['success' => true, 'result' => $result]);
            } elseif ($action === 'decrypt') {
                $result = $this->decrypt($text, $key, $spaceChar);
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Action invalide.']);
            }
            
            exit;
            
        } catch (\Throwable $e) {
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            exit;
        }
    }

    /**
     * Chiffre un texte par permutation.
     */
    private function encrypt($text, $key, $spaceChar)
    {
        $text = trim($text);
        $text = str_replace(" ", $spaceChar, $text);
        $text = $this->cleanText($text);
        $key = $this->cleanText($key);

        $nColumns = strlen($key);
        $nRows = ceil(strlen($text) / $nColumns);
        
        // Padding
        $text .= str_repeat(strtolower($spaceChar), $nColumns * $nRows - strlen($text));
        
        $order = $this->getOrderFromKey($key);

        $result = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $currentPos = ($i % $nRows) * $nColumns + $order[floor($i / $nRows)];
            $result .= $text[$currentPos];
        }

        return $result;
    }

    /**
     * Déchiffre un texte par permutation.
     */
    private function decrypt($text, $key, $spaceChar)
    {
        $text = $this->cleanText($text);
        $key = $this->cleanText($key);

        $nColumns = strlen($key);
        $nRows = ceil(strlen($text) / $nColumns);
        
        $order = $this->getOrderFromKey($key);
        $order = array_flip($order);

        $result = "";
        for ($i = 0; $i < strlen($text); ++$i) {
            $currentPos = $order[$i % $nColumns] * $nRows + floor($i / $nColumns);
            $result .= $text[$currentPos];
        }

        return $result;
    }

    /**
     * Nettoie le texte (minuscules, sans accents, alphanumérique uniquement).
     */
    private function cleanText($text)
    {
        $text = strtolower($text);
        
        $accents = [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o'
        ];
        
        $text = strtr($text, $accents);
        $text = preg_replace('/[^a-z0-9]/', '', $text);
        
        return $text;
    }

    /**
     * Calcule l'ordre des colonnes à partir de la clé.
     */
    private function getOrderFromKey($key)
    {
        $sorted = str_split($key);
        sort($sorted);
        $sorted = implode('', $sorted);

        $order = [];
        for ($i = 0; $i < strlen($key); ++$i) {
            $order[$i] = strpos($sorted, $key[$i]);
        }

        return $order;
    }
}

