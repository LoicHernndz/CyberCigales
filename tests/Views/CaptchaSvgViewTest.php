<?php
/**
 * Tests unitaires standalone pour CaptchaSvgView
 *
 * Exécuter : php tests/Views/CaptchaSvgViewTest.php
 */

// Autoload : charger la classe depuis src/
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../../src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Views\Captcha\CaptchaSvgView;

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

echo "=== CaptchaSvgViewTest ===\n\n";

// Test 1 : Le rendu contient les balises SVG
$view = new CaptchaSvgView('ABC12');
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'render() contient <svg> et </svg>',
    str_contains($output, '<svg') && str_contains($output, '</svg>')
);

// Test 2 : Le code est présent dans la sortie
$code = 'XY789';
$view = new CaptchaSvgView($code);
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'Le code CAPTCHA est visible dans le SVG',
    str_contains($output, $code)
);

// Test 3 : Dimensions par défaut 140x48
$view = new CaptchaSvgView('TEST1');
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'Dimensions par défaut : 140x48',
    str_contains($output, 'width="140"') && str_contains($output, 'height="48"')
);

// Test 4 : Dimensions personnalisées
$view = new CaptchaSvgView('TEST2', 200, 60);
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'Dimensions personnalisées : 200x60',
    str_contains($output, 'width="200"') && str_contains($output, 'height="60"')
);

// Test 5 : Protection XSS (caractères spéciaux échappés)
$malicious = '<script>';
$view = new CaptchaSvgView($malicious);
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'Protection XSS : <script> est échappé',
    !str_contains($output, '<script>') && str_contains($output, '&lt;script&gt;')
);

// Test 6 : Lignes de bruit dans le SVG
$view = new CaptchaSvgView('NOISE');
ob_start();
@$view->render();
$output = ob_get_clean();
assert_test(
    'Le SVG contient des lignes de bruit (<line>)',
    str_contains($output, '<line')
);

echo "\n--- Résultat : $passed ✅ / " . ($passed + $failed) . " tests ---\n";
exit($failed > 0 ? 1 : 0);
