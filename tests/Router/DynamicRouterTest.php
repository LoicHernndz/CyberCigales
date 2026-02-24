<?php
/**
 * Tests du routeur dynamique
 * 
 * Vérifie la conversion URL → namespace de contrôleur
 * et la résolution correcte des classes.
 * 
 * Lancer : php tests/Router/DynamicRouterTest.php
 */

// Charger l'autoloader
include __DIR__ . '/../../src/config/Autoloader.php';

/**
 * Convertit un segment d'URL kebab-case en PascalCase
 * (même logique que dans index.php)
 */
function kebabToPascal(string $segment): string {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $segment)));
}

/**
 * Résout une URI en nom de classe contrôleur
 * Même logique que index.php : une seule boucle du chemin le plus long au plus court
 * Retourne [className, params] ou [null, []] si introuvable
 */
function resolveUri(string $uri): array {
    if ($uri === '/') {
        return ['Controllers\\Homepage', []];
    }

    $segments = explode('/', trim($uri, '/'));
    $pascalSegments = array_map('kebabToPascal', $segments);

    for ($i = count($pascalSegments); $i > 0; $i--) {
        $trySegments = array_slice($pascalSegments, 0, $i);
        $params = ($i < count($pascalSegments)) ? array_slice($segments, $i) : [];

        $tryClass = 'Controllers\\' . implode('\\', $trySegments);

        if (class_exists($tryClass)) {
            return [$tryClass, $params];
        }

        if (class_exists($tryClass . '\\Index')) {
            return [$tryClass . '\\Index', $params];
        }
    }

    return [null, []];
}

// ================================================
// TESTS
// ================================================

$passed = 0;
$failed = 0;
$total = 0;

function test(string $name, bool $condition): void {
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

echo "\n=== Tests de conversion kebab-case → PascalCase ===\n\n";

test("'login' → 'Login'", kebabToPascal('login') === 'Login');
test("'reset-password' → 'ResetPassword'", kebabToPascal('reset-password') === 'ResetPassword');
test("'chiffrement-cesar' → 'ChiffrementCesar'", kebabToPascal('chiffrement-cesar') === 'ChiffrementCesar');
test("'hist-mdp' → 'HistMdp'", kebabToPascal('hist-mdp') === 'HistMdp');
test("'cypher-rush' → 'CypherRush'", kebabToPascal('cypher-rush') === 'CypherRush');
test("'data-breach' → 'DataBreach'", kebabToPascal('data-breach') === 'DataBreach');
test("'new-password' → 'NewPassword'", kebabToPascal('new-password') === 'NewPassword');
test("'outil-permutation' → 'OutilPermutation'", kebabToPascal('outil-permutation') === 'OutilPermutation');

echo "\n=== Tests de résolution URL → Contrôleur (correspondance directe) ===\n\n";

test("/ → Controllers\\Homepage",
    resolveUri('/')[0] === 'Controllers\\Homepage');

test("/dashboard → Controllers\\Dashboard",
    resolveUri('/dashboard')[0] === 'Controllers\\Dashboard');

test("/mentions → Controllers\\Mentions",
    resolveUri('/mentions')[0] === 'Controllers\\Mentions');

test("/captcha → Controllers\\Captcha",
    resolveUri('/captcha')[0] === 'Controllers\\Captcha');

test("/plan → Controllers\\Plan",
    resolveUri('/plan')[0] === 'Controllers\\Plan');

test("/macos → Controllers\\Macos",
    resolveUri('/macos')[0] === 'Controllers\\Macos');

test("/outils → Controllers\\Outils",
    resolveUri('/outils')[0] === 'Controllers\\Outils');

test("/email → Controllers\\Email",
    resolveUri('/email')[0] === 'Controllers\\Email');

test("/agenda → Controllers\\Agenda",
    resolveUri('/agenda')[0] === 'Controllers\\Agenda');

test("/web → Controllers\\Web",
    resolveUri('/web')[0] === 'Controllers\\Web');

echo "\n=== Tests de résolution URL → Contrôleur (sous-dossiers) ===\n\n";

test("/user/login → Controllers\\User\\Login",
    resolveUri('/user/login')[0] === 'Controllers\\User\\Login');

test("/user/signup → Controllers\\User\\Signup",
    resolveUri('/user/signup')[0] === 'Controllers\\User\\Signup');

test("/user/reset-password → Controllers\\User\\ResetPassword",
    resolveUri('/user/reset-password')[0] === 'Controllers\\User\\ResetPassword');

test("/user/new-password → Controllers\\User\\NewPassword",
    resolveUri('/user/new-password')[0] === 'Controllers\\User\\NewPassword');

test("/user/edit → Controllers\\User\\Edit",
    resolveUri('/user/edit')[0] === 'Controllers\\User\\Edit');

test("/user/delete → Controllers\\User\\Delete",
    resolveUri('/user/delete')[0] === 'Controllers\\User\\Delete');

test("/user/logout → Controllers\\User\\Logout",
    resolveUri('/user/logout')[0] === 'Controllers\\User\\Logout');

test("/user/profil → Controllers\\User\\Profil",
    resolveUri('/user/profil')[0] === 'Controllers\\User\\Profil');

test("/game/hamming → Controllers\\Game\\Hamming",
    resolveUri('/game/hamming')[0] === 'Controllers\\Game\\Hamming');

test("/game/cypher-rush → Controllers\\Game\\CypherRush",
    resolveUri('/game/cypher-rush')[0] === 'Controllers\\Game\\CypherRush');

test("/game/frequency → Controllers\\Game\\Frequency",
    resolveUri('/game/frequency')[0] === 'Controllers\\Game\\Frequency');

test("/game/phishing → Controllers\\Game\\Phishing",
    resolveUri('/game/phishing')[0] === 'Controllers\\Game\\Phishing');

test("/data-breach/check → Controllers\\DataBreach\\Check",
    resolveUri('/data-breach/check')[0] === 'Controllers\\DataBreach\\Check');

echo "\n=== Tests de résolution URL → Contrôleur (chiffrement) ===\n\n";

test("/code/chiffrement-cesar → Controllers\\Code\\ChiffrementCesar",
    resolveUri('/code/chiffrement-cesar')[0] === 'Controllers\\Code\\ChiffrementCesar');

test("/code/dechiffrement-cesar → Controllers\\Code\\DechiffrementCesar",
    resolveUri('/code/dechiffrement-cesar')[0] === 'Controllers\\Code\\DechiffrementCesar');

test("/code/chiffrement-vigenere → Controllers\\Code\\ChiffrementVigenere",
    resolveUri('/code/chiffrement-vigenere')[0] === 'Controllers\\Code\\ChiffrementVigenere');

test("/code/dechiffrement-vigenere → Controllers\\Code\\DechiffrementVigenere",
    resolveUri('/code/dechiffrement-vigenere')[0] === 'Controllers\\Code\\DechiffrementVigenere');

test("/code/chiffrement-permutation → Controllers\\Code\\ChiffrementPermutation",
    resolveUri('/code/chiffrement-permutation')[0] === 'Controllers\\Code\\ChiffrementPermutation');

test("/code/dechiffrement-permutation → Controllers\\Code\\DechiffrementPermutation",
    resolveUri('/code/dechiffrement-permutation')[0] === 'Controllers\\Code\\DechiffrementPermutation');

test("/code/outil-permutation → Controllers\\Code\\OutilPermutation",
    resolveUri('/code/outil-permutation')[0] === 'Controllers\\Code\\OutilPermutation');

echo "\n=== Tests de résolution URL → Contrôleur (convention Index) ===\n\n";

test("/lecon → Controllers\\Lecon\\Index",
    resolveUri('/lecon')[0] === 'Controllers\\Lecon\\Index');

test("/instagram → Controllers\\Instagram\\Index",
    resolveUri('/instagram')[0] === 'Controllers\\Instagram\\Index');

test("/bash → Controllers\\Bash\\Index",
    resolveUri('/bash')[0] === 'Controllers\\Bash\\Index');

test("/minigames → Controllers\\Minigames\\Index",
    resolveUri('/minigames')[0] === 'Controllers\\Minigames\\Index');

echo "\n=== Tests de résolution URL → Contrôleur (leçons) ===\n\n";

test("/lecon/cesar → Controllers\\Lecon\\Cesar",
    resolveUri('/lecon/cesar')[0] === 'Controllers\\Lecon\\Cesar');

test("/lecon/vigenere → Controllers\\Lecon\\Vigenere",
    resolveUri('/lecon/vigenere')[0] === 'Controllers\\Lecon\\Vigenere');

test("/lecon/permutation → Controllers\\Lecon\\Permutation",
    resolveUri('/lecon/permutation')[0] === 'Controllers\\Lecon\\Permutation');

test("/lecon/hist-mdp → Controllers\\Lecon\\HistMdp",
    resolveUri('/lecon/hist-mdp')[0] === 'Controllers\\Lecon\\HistMdp');

test("/lecon/rgpd → Controllers\\Lecon\\Rgpd",
    resolveUri('/lecon/rgpd')[0] === 'Controllers\\Lecon\\Rgpd');

echo "\n=== Tests de résolution URL → Contrôleur (bash/exec et instagram/chat) ===\n\n";

test("/bash/exec → Controllers\\Bash\\Exec",
    resolveUri('/bash/exec')[0] === 'Controllers\\Bash\\Exec');

test("/instagram/chat/response → Controllers\\Instagram\\Chat\\Response",
    resolveUri('/instagram/chat/response')[0] === 'Controllers\\Instagram\\Chat\\Response');

echo "\n=== Tests de résolution URL → Contrôleur (paramètres dynamiques) ===\n\n";

$result = resolveUri('/instagram/user/mel_133');
test("/instagram/user/mel_133 → Controllers\\Instagram\\User avec params",
    $result[0] === 'Controllers\\Instagram\\User' && $result[1] === ['mel_133']);

$result = resolveUri('/instagram/user/mel_133/chat');
test("/instagram/user/mel_133/chat → Controllers\\Instagram\\User avec params [mel_133, chat]",
    $result[0] === 'Controllers\\Instagram\\User' && $result[1] === ['mel_133', 'chat']);

echo "\n=== Tests 404 (URL invalide) ===\n\n";

test("/route-inexistante → null (404)",
    resolveUri('/route-inexistante')[0] === null);

test("/foo/bar/baz → null (404)",
    resolveUri('/foo/bar/baz')[0] === null);

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
