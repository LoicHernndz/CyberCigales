<?php
echo "--- TEST FREQUENCE GAME CESAR ---\n\n";

require_once __DIR__ . '/../src/helpers/Code/AbstractCode.php';
require_once __DIR__ . '/../src/helpers/Code/Cesar.php';

use helpers\Code\Cesar;

// Test avec une phrase du jeu
$originalText = "LA SECURITE COMMENCE PAR UN BON MOT DE PASSE";

// Tester plusieurs décalages
$shifts = [3, 7, 13, 17];

foreach ($shifts as $shift) {
    $encrypted = Cesar::encrypt($originalText, $shift);
    $decrypted = Cesar::decrypt($encrypted, $shift);

    echo "Décalage k=$shift:\n";
    echo "  Original:  $originalText\n";
    echo "  Chiffré:   $encrypted\n";
    echo "  Déchiffré: $decrypted\n";
    echo "  Status:    " . ($decrypted === $originalText ? "OK ✓" : "ERREUR!") . "\n\n";
}

// Vérification de la cohérence César: toutes les lettres suivent le même décalage
echo "--- VERIFICATION COHERENCE CESAR ---\n";
$testText = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$testShift = 5;
$testEncrypted = Cesar::encrypt($testText, $testShift);
echo "Alphabet original: $testText\n";
echo "Alphabet chiffré (k=$testShift): $testEncrypted\n";
echo "Attendu: FGHIJKLMNOPQRSTUVWXYZABCDE\n";
echo "Status: " . ($testEncrypted === "FGHIJKLMNOPQRSTUVWXYZABCDE" ? "COHERENT ✓" : "INCOHERENT!") . "\n";
