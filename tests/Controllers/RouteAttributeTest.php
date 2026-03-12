<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Attributes\Route;

class RouteAttributeTest extends TestCase
{
    // =============================================
    // Vérification des attributs Route
    // =============================================

    #[Test]
    #[DataProvider('routeProvider')]
    public function controllerHasCorrectRouteAttribute(string $className, string $expectedPath, string $expectedName): void
    {
        $ref = new \ReflectionClass($className);
        $attrs = $ref->getAttributes(Route::class);

        $this->assertNotEmpty($attrs, "$className devrait avoir un attribut #[Route]");

        $route = $attrs[0]->newInstance();
        $this->assertSame($expectedPath, $route->path, "$className: chemin incorrect");
        $this->assertSame($expectedName, $route->name, "$className: nom incorrect");
    }

    public static function routeProvider(): array
    {
        return [
            'Captcha'                   => ['Controllers\Captcha', '/captcha', 'captcha'],
            'ChiffrementCesar'          => ['Controllers\Code\ChiffrementCesar', '/code/chiffrement-cesar', 'code_chiffrement_cesar'],
            'DechiffrementCesar'        => ['Controllers\Code\DechiffrementCesar', '/code/dechiffrement-cesar', 'code_dechiffrement_cesar'],
            'ChiffrementVigenere'       => ['Controllers\Code\ChiffrementVigenere', '/code/chiffrement-vigenere', 'code_chiffrement_vigenere'],
            'DechiffrementVigenere'     => ['Controllers\Code\DechiffrementVigenere', '/code/dechiffrement-vigenere', 'code_dechiffrement_vigenere'],
            'ChiffrementPermutation'    => ['Controllers\Code\ChiffrementPermutation', '/code/chiffrement-permutation', 'code_chiffrement_permutation'],
            'DechiffrementPermutation'  => ['Controllers\Code\DechiffrementPermutation', '/code/dechiffrement-permutation', 'code_dechiffrement_permutation'],
            'OutilPermutation'          => ['Controllers\Code\OutilPermutation', '/code/outil-permutation', 'code_outil_permutation'],
            'Frequency'                 => ['Controllers\Game\Frequency', '/game/frequency', 'game_frequency'],
            'Phishing'                  => ['Controllers\Game\Phishing', '/game/phishing', 'game_phishing'],
            'CypherRush'                => ['Controllers\Game\CypherRush', '/game/cypher-rush', 'game_cypher_rush'],
            'Hamming'                   => ['Controllers\Game\Hamming', '/game/hamming', 'game_hamming'],
            'LeconComplete'             => ['Controllers\Lecon\Complete', '/lecon/complete', 'lecon_complete'],
        ];
    }

    // =============================================
    // Vérification que tous les controllers héritent de AbstractController
    // =============================================

    #[Test]
    #[DataProvider('controllerClassProvider')]
    public function controllerExtendsAbstractController(string $className): void
    {
        $ref = new \ReflectionClass($className);
        $parent = $ref->getParentClass();

        // Soit directement AbstractController, soit un parent qui l'extend
        $isChild = false;
        while ($parent) {
            if ($parent->getName() === 'Controllers\AbstractController') {
                $isChild = true;
                break;
            }
            $parent = $parent->getParentClass();
        }
        $this->assertTrue($isChild, "$className devrait hériter de AbstractController");
    }

    #[Test]
    #[DataProvider('controllerClassProvider')]
    public function controllerHasGetMethod(string $className): void
    {
        $ref = new \ReflectionClass($className);
        $this->assertTrue($ref->hasMethod('getMethod'), "$className devrait avoir getMethod()");
    }

    public static function controllerClassProvider(): array
    {
        return [
            'Captcha'                   => ['Controllers\Captcha'],
            'Agenda'                    => ['Controllers\Agenda'],
            'Mentions'                  => ['Controllers\Mentions'],
            'Outils'                    => ['Controllers\Outils'],
            'Plan'                      => ['Controllers\Plan'],
            'Email'                     => ['Controllers\Email'],
            'Web'                       => ['Controllers\Web'],
            'Macos'                     => ['Controllers\Macos'],
            'Dashboard'                 => ['Controllers\Dashboard'],
            'HomepageIndex'             => ['Controllers\Homepage\Index'],
            'Login'                     => ['Controllers\User\Login'],
            'Signup'                    => ['Controllers\User\Signup'],
            'Logout'                    => ['Controllers\User\Logout'],
            'Profil'                    => ['Controllers\User\Profil'],
            'ChiffrementCesar'          => ['Controllers\Code\ChiffrementCesar'],
            'Frequency'                 => ['Controllers\Game\Frequency'],
            'Hamming'                   => ['Controllers\Game\Hamming'],
            'CypherRush'                => ['Controllers\Game\CypherRush'],
            'Phishing'                  => ['Controllers\Game\Phishing'],
            'InstagramIndex'            => ['Controllers\Instagram\Index'],
            'LeconIndex'                => ['Controllers\Lecon\Index'],
            'LeconComplete'             => ['Controllers\Lecon\Complete'],
        ];
    }
}
