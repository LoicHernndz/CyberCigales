<?php
/**
 * Tests unitaires standalone pour Captcha::generateCode()
 *
 * Exécuter : php tests/Controllers/CaptchaTest.php
 */

// Autoload : charger les classes depuis src/
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../../src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Charger session_helper pour avoir la fonction redirect() et flash()
// mais sans démarrer de session réelle en CLI
if (!function_exists('flash')) {
    function flash($name = '', $message = '', $class = ''): string
    {
        return '';
    }
}
if (!function_exists('redirect')) {
    function redirect($location): void
    { /* no-op en test */
    }
}

use Controllers\Captcha;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void
{
    global $passed, $failed;
    if ($condition) {
        echo "  ✅ PASS : $name\n";
        $passed++;
    } else {
        echo "  ❌ FAIL : $name" . ($detail ? " — $detail" : "") . "\n";
        $failed++;
    }
}

echo "=== CaptchaTest ===\n\n";

$captcha = new Captcha();
$ref = new ReflectionMethod($captcha, 'generateCode');
$ref->setAccessible(true);

// Test 1 : Le code fait 5 caractères
$code = $ref->invoke($captcha);
assert_test(
    'generateCode() retourne 5 caractères',
    is_string($code) && strlen($code) === 5,
    "Obtenu : '$code' (longueur " . strlen($code) . ")"
);

// Test 2 : Seuls les caractères autorisés sont utilisés
$allowedChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$allValid = true;
$badChar = '';
for ($i = 0; $i < 50; $i++) {
    $code = $ref->invoke($captcha);
    for ($j = 0; $j < strlen($code); $j++) {
        if (!str_contains($allowedChars, $code[$j])) {
            $allValid = false;
            $badChar = $code[$j];
            break 2;
        }
    }
}
assert_test(
    'Tous les caractères sont dans l\'alphabet autorisé (50 essais)',
    $allValid,
    $badChar ? "Caractère interdit : '$badChar'" : ""
);

// Test 3 : Pas de caractères ambigus (0, O, 1, I)
$forbidden = ['0', 'O', '1', 'I'];
$noAmbiguous = true;
$foundChar = '';
for ($i = 0; $i < 100; $i++) {
    $code = $ref->invoke($captcha);
    foreach ($forbidden as $char) {
        if (str_contains($code, $char)) {
            $noAmbiguous = false;
            $foundChar = $char;
            break 2;
        }
    }
}
assert_test(
    'Aucun caractère ambigu (0/O/1/I) sur 100 essais',
    $noAmbiguous,
    $foundChar ? "Trouvé : '$foundChar'" : ""
);

// Test 4 : Deux appels produisent des codes différents
$codes = [];
for ($i = 0; $i < 10; $i++) {
    $codes[] = $ref->invoke($captcha);
}
$uniqueCodes = array_unique($codes);
assert_test(
    'Les codes sont variés (>1 unique sur 10 appels)',
    count($uniqueCodes) > 1,
    "Unique : " . count($uniqueCodes) . "/10"
);

echo "\n--- Résultat : $passed ✅ / " . ($passed + $failed) . " tests ---\n";
exit($failed > 0 ? 1 : 0);
