<?php

namespace Tests\Router;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests PHPUnit du routeur dynamique
 *
 * Reproduit la logique de résolution de index.php :
 * 1. Convertir les segments kebab-case → PascalCase
 * 2. Tenter du chemin le plus long au plus court
 * 3. Convention Index pour les dossiers
 * 4. Paramètres dynamiques dans le reste de l'URL
 */
class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        // S'assurer que l'autoloader du projet est chargé pour class_exists()
        $autoloaderPath = dirname(__DIR__, 2) . '/src/config/Autoloader.php';
        if (file_exists($autoloaderPath)) {
            $originalDir = getcwd();
            chdir(dirname(__DIR__, 2) . '/public');
            if (!defined('AUTOLOADER_LOADED')) {
                include_once $autoloaderPath;
                define('AUTOLOADER_LOADED', true);
            }
            chdir($originalDir);
        }
    }

    // =============================================
    // Helpers (même logique que index.php)
    // =============================================

    private function kebabToPascal(string $segment): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $segment)));
    }

    /**
     * Résout une URI en [className, params]
     * Même algorithme que index.php
     */
    private function resolveUri(string $uri): array
    {
        if ($uri === '/') {
            return ['Controllers\\Homepage\\Index', []];
        }

        $segments = explode('/', trim($uri, '/'));
        $pascalSegments = array_map([$this, 'kebabToPascal'], $segments);

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

    // =============================================
    // kebabToPascal
    // =============================================

    #[Test]
    #[DataProvider('kebabToPascalProvider')]
    public function kebabToPascalConvertsCorrectly(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->kebabToPascal($input));
    }

    public static function kebabToPascalProvider(): array
    {
        return [
            'simple'                => ['login', 'Login'],
            'deux mots'             => ['reset-password', 'ResetPassword'],
            'trois mots'            => ['chiffrement-cesar', 'ChiffrementCesar'],
            'déjà majuscule'        => ['ABC', 'ABC'],
            'un seul char'          => ['a', 'A'],
            'segments courts'       => ['a-b-c', 'ABC'],
            'segment long'          => ['mon-segment-long', 'MonSegmentLong'],
            'vide'                  => ['', ''],
            'cypher-rush'           => ['cypher-rush', 'CypherRush'],
            'data-breach'           => ['data-breach', 'DataBreach'],
            'outil-permutation'     => ['outil-permutation', 'OutilPermutation'],
        ];
    }

    // =============================================
    // Résolution directe (une classe par segment)
    // =============================================

    #[Test]
    #[DataProvider('directRouteProvider')]
    public function directRouteResolvesCorrectly(string $uri, string $expectedClass): void
    {
        $result = $this->resolveUri($uri);
        $this->assertSame($expectedClass, $result[0], "URI '$uri' devrait résoudre vers $expectedClass");
    }

    public static function directRouteProvider(): array
    {
        return [
            'homepage'      => ['/', 'Controllers\\Homepage\\Index'],
            'dashboard'     => ['/dashboard', 'Controllers\\Dashboard'],
            'mentions'      => ['/mentions', 'Controllers\\Mentions'],
            'captcha'       => ['/captcha', 'Controllers\\Captcha'],
            'plan'          => ['/plan', 'Controllers\\Plan'],
            'macos'         => ['/macos', 'Controllers\\Macos'],
            'outils'        => ['/outils', 'Controllers\\Outils'],
            'email'         => ['/email', 'Controllers\\Email'],
            'agenda'        => ['/agenda', 'Controllers\\Agenda'],
            'web'           => ['/web', 'Controllers\\Web'],
        ];
    }

    // =============================================
    // Résolution sous-dossiers
    // =============================================

    #[Test]
    #[DataProvider('subfolderRouteProvider')]
    public function subfolderRouteResolvesCorrectly(string $uri, string $expectedClass): void
    {
        $result = $this->resolveUri($uri);
        $this->assertSame($expectedClass, $result[0]);
    }

    public static function subfolderRouteProvider(): array
    {
        return [
            'user/login'            => ['/user/login', 'Controllers\\User\\Login'],
            'user/signup'           => ['/user/signup', 'Controllers\\User\\Signup'],
            'user/reset-password'   => ['/user/reset-password', 'Controllers\\User\\ResetPassword'],
            'user/new-password'     => ['/user/new-password', 'Controllers\\User\\NewPassword'],
            'user/edit'             => ['/user/edit', 'Controllers\\User\\Edit'],
            'user/delete'           => ['/user/delete', 'Controllers\\User\\Delete'],
            'user/logout'           => ['/user/logout', 'Controllers\\User\\Logout'],
            'user/profil'           => ['/user/profil', 'Controllers\\User\\Profil'],
            'game/hamming'          => ['/game/hamming', 'Controllers\\Game\\Hamming'],
            'game/cypher-rush'      => ['/game/cypher-rush', 'Controllers\\Game\\CypherRush'],
            'game/frequency'        => ['/game/frequency', 'Controllers\\Game\\Frequency'],
            'game/phishing'         => ['/game/phishing', 'Controllers\\Game\\Phishing'],
            'data-breach/check'     => ['/data-breach/check', 'Controllers\\DataBreach\\Check'],
        ];
    }

    // =============================================
    // Résolution chiffrement (Code/)
    // =============================================

    #[Test]
    #[DataProvider('codeRouteProvider')]
    public function codeRouteResolvesCorrectly(string $uri, string $expectedClass): void
    {
        $result = $this->resolveUri($uri);
        $this->assertSame($expectedClass, $result[0]);
    }

    public static function codeRouteProvider(): array
    {
        return [
            'chiffrement-cesar'         => ['/code/chiffrement-cesar', 'Controllers\\Code\\ChiffrementCesar'],
            'dechiffrement-cesar'       => ['/code/dechiffrement-cesar', 'Controllers\\Code\\DechiffrementCesar'],
            'chiffrement-vigenere'      => ['/code/chiffrement-vigenere', 'Controllers\\Code\\ChiffrementVigenere'],
            'dechiffrement-vigenere'    => ['/code/dechiffrement-vigenere', 'Controllers\\Code\\DechiffrementVigenere'],
            'chiffrement-permutation'   => ['/code/chiffrement-permutation', 'Controllers\\Code\\ChiffrementPermutation'],
            'dechiffrement-permutation' => ['/code/dechiffrement-permutation', 'Controllers\\Code\\DechiffrementPermutation'],
            'outil-permutation'         => ['/code/outil-permutation', 'Controllers\\Code\\OutilPermutation'],
        ];
    }

    // =============================================
    // Convention Index (dossier sans fichier direct)
    // =============================================

    #[Test]
    #[DataProvider('indexConventionProvider')]
    public function indexConventionResolvesCorrectly(string $uri, string $expectedClass): void
    {
        $result = $this->resolveUri($uri);
        $this->assertSame($expectedClass, $result[0]);
    }

    public static function indexConventionProvider(): array
    {
        return [
            'lecon'         => ['/lecon', 'Controllers\\Lecon\\Index'],
            'instagram'     => ['/instagram', 'Controllers\\Instagram\\Index'],
            'bash'          => ['/bash', 'Controllers\\Bash\\Index'],
            'minigames'     => ['/minigames', 'Controllers\\Minigames\\Index'],
        ];
    }

    // =============================================
    // Leçons individuelles
    // =============================================

    #[Test]
    #[DataProvider('leconRouteProvider')]
    public function leconRouteResolvesCorrectly(string $uri, string $expectedClass): void
    {
        $result = $this->resolveUri($uri);
        $this->assertSame($expectedClass, $result[0]);
    }

    public static function leconRouteProvider(): array
    {
        return [
            'lecon/cesar'       => ['/lecon/cesar', 'Controllers\\Lecon\\Cesar'],
            'lecon/vigenere'    => ['/lecon/vigenere', 'Controllers\\Lecon\\Vigenere'],
            'lecon/permutation' => ['/lecon/permutation', 'Controllers\\Lecon\\Permutation'],
            'lecon/hist-mdp'    => ['/lecon/hist-mdp', 'Controllers\\Lecon\\HistMdp'],
            'lecon/rgpd'        => ['/lecon/rgpd', 'Controllers\\Lecon\\Rgpd'],
        ];
    }

    // =============================================
    // Paramètres dynamiques
    // =============================================

    #[Test]
    public function dynamicParamsAreCaptured(): void
    {
        $result = $this->resolveUri('/instagram/user/mel_133');
        $this->assertSame('Controllers\\Instagram\\User', $result[0]);
        $this->assertSame(['mel_133'], $result[1]);
    }

    #[Test]
    public function multipleDynamicParams(): void
    {
        $result = $this->resolveUri('/instagram/user/mel_133/chat');
        $this->assertSame('Controllers\\Instagram\\User', $result[0]);
        $this->assertSame(['mel_133', 'chat'], $result[1]);
    }

    #[Test]
    public function noParamsForDirectMatch(): void
    {
        $result = $this->resolveUri('/plan');
        $this->assertSame([], $result[1]);
    }

    // =============================================
    // 404 (URL invalide → null)
    // =============================================

    #[Test]
    #[DataProvider('notFoundProvider')]
    public function unknownRouteReturnsNull(string $uri): void
    {
        $result = $this->resolveUri($uri);
        $this->assertNull($result[0], "URI '$uri' devrait retourner null (404)");
    }

    public static function notFoundProvider(): array
    {
        return [
            'route inexistante'         => ['/route-inexistante'],
            'profondeur inconnue'       => ['/foo/bar/baz'],
            'segment inconnu unique'    => ['/zzz-inconnu'],
            'sous-chemin profond'       => ['/a/b/c/d/e/f'],
        ];
    }

    // =============================================
    // Edge cases (robustesse)
    // =============================================

    #[Test]
    public function trailingSlashIsHandled(): void
    {
        $result = $this->resolveUri('/plan/');
        $this->assertSame('Controllers\\Plan', $result[0]);
    }

    #[Test]
    public function trailingSlashOnSubroute(): void
    {
        $result = $this->resolveUri('/lecon/cesar/');
        $this->assertSame('Controllers\\Lecon\\Cesar', $result[0]);
    }

    #[Test]
    public function trailingSlashOnDynamicRoute(): void
    {
        $result = $this->resolveUri('/instagram/user/mel_133/');
        $this->assertSame('Controllers\\Instagram\\User', $result[0]);
        $this->assertSame(['mel_133'], $result[1]);
    }

    // =============================================
    // Bash et Instagram profonds
    // =============================================

    #[Test]
    public function bashExecRoute(): void
    {
        $result = $this->resolveUri('/bash/exec');
        $this->assertSame('Controllers\\Bash\\Exec', $result[0]);
    }

    #[Test]
    public function instagramChatResponseRoute(): void
    {
        $result = $this->resolveUri('/instagram/chat/response');
        $this->assertSame('Controllers\\Instagram\\Chat\\Response', $result[0]);
    }
}
