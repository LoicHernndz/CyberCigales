<?php
/**
 * Tests d'existence et de structure des contrôleurs
 *
 * Vérifie que chaque contrôleur attendu existe en tant que classe,
 * hérite de AbstractController et implémente la méthode getMethod().
 *
 * Lancer : php tests/Controllers/ControllerExistenceTest.php
 */

// Se placer dans le répertoire public/ pour que l'autoloader fonctionne
chdir(__DIR__ . '/../../public');
include __DIR__ . '/../../src/config/Autoloader.php';

// ================================================
// FRAMEWORK DE TEST
// ================================================

$passed = 0;
$failed = 0;
$total = 0;

/**
 * Exécute un test unitaire et affiche le résultat
 *
 * @param string $name Nom descriptif du test
 * @param bool $condition Résultat du test (true = succès)
 * @return void
 */
function test(string $name, bool $condition): void
{
    global $passed, $failed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "  [OK] $name\n";
    } else {
        $failed++;
        echo "  [FAIL] $name\n";
    }
}

// ================================================
// LISTE DES CONTRÔLEURS ATTENDUS
// ================================================

/**
 * Liste de tous les contrôleurs ajoutés sur la branche routes-dynamiques
 *
 * @var array<int, string>
 */
$expectedControllers = [
    // Standalone
    'Controllers\\Agenda',
    'Controllers\\Email',
    'Controllers\\Macos',
    'Controllers\\Outils',
    'Controllers\\Plan',
    'Controllers\\Web',

    // Bash
    'Controllers\\Bash\\Exec',
    'Controllers\\Bash\\Index',

    // Code
    'Controllers\\Code\\ChiffrementCesar',
    'Controllers\\Code\\ChiffrementPermutation',
    'Controllers\\Code\\ChiffrementVigenere',
    'Controllers\\Code\\DechiffrementCesar',
    'Controllers\\Code\\DechiffrementPermutation',
    'Controllers\\Code\\DechiffrementVigenere',
    'Controllers\\Code\\OutilPermutation',

    // DataBreach
    'Controllers\\DataBreach\\Check',

    // Game
    'Controllers\\Game\\CypherRush',
    'Controllers\\Game\\Frequency',
    'Controllers\\Game\\Hamming',
    'Controllers\\Game\\Phishing',

    // Instagram
    'Controllers\\Instagram\\Chat\\Response',
    'Controllers\\Instagram\\Index',
    'Controllers\\Instagram\\User',

    // Lecon
    'Controllers\\Lecon\\Cesar',
    'Controllers\\Lecon\\HistMdp',
    'Controllers\\Lecon\\Index',
    'Controllers\\Lecon\\Permutation',
    'Controllers\\Lecon\\Rgpd',
    'Controllers\\Lecon\\Vigenere',

    // Minigames
    'Controllers\\Minigames\\Index',

    // User
    'Controllers\\User\\Delete',
    'Controllers\\User\\Edit',
    'Controllers\\User\\NewPassword',
];

// ================================================
// TESTS D'EXISTENCE
// ================================================

echo "\n=== Tests d'existence des contrôleurs ===\n\n";

foreach ($expectedControllers as $controller) {
    test("$controller existe", class_exists($controller));
}

// ================================================
// TESTS D'HÉRITAGE
// ================================================

echo "\n=== Tests d'héritage (extends AbstractController) ===\n\n";

foreach ($expectedControllers as $controller) {
    if (class_exists($controller)) {
        $isChild = is_subclass_of($controller, 'Controllers\\AbstractController');
        test("$controller étend AbstractController", $isChild);
    }
}

// ================================================
// TESTS DE MÉTHODE getMethod()
// ================================================

echo "\n=== Tests d'implémentation de getMethod() ===\n\n";

foreach ($expectedControllers as $controller) {
    if (class_exists($controller)) {
        $hasMethod = method_exists($controller, 'getMethod');
        test("$controller implémente getMethod()", $hasMethod);
    }
}

// ================================================
// TESTS DE VISIBILITÉ (getMethod doit être public)
// ================================================

echo "\n=== Tests de visibilité (getMethod doit être public) ===\n\n";

foreach ($expectedControllers as $controller) {
    if (class_exists($controller) && method_exists($controller, 'getMethod')) {
        $reflection = new ReflectionMethod($controller, 'getMethod');
        test("$controller::getMethod() est public", $reflection->isPublic());
    }
}

// ================================================
// RÉSULTAT FINAL
// ================================================
echo "\n" . str_repeat('=', 50) . "\n";
echo "Résultat : $passed/$total tests passés";
if ($failed > 0) {
    echo " ($failed échoué(s))";
}
echo "\n" . str_repeat('=', 50) . "\n\n";

exit($failed > 0 ? 1 : 0);
