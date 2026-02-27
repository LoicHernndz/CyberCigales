<?php
/**
 * Contrôleur de test pour le routage hybride
 * 
 * Démontre le nouveau style /test/method/params
 */

namespace Controllers;

class Test
{
    /**
     * Méthode de test simple
     */
    public function index()
    {
        echo "<h1>Test Index</h1>";
        echo "<p>Route: /test/index</p>";
        echo "<p>Le nouveau style fonctionne !</p>";
    }
    
    /**
     * Méthode avec un paramètre
     * 
     * @param int $id
     */
    public function show($id = null)
    {
        echo "<h1>Test Show</h1>";
        echo "<p>Route: /test/show" . ($id ? "/$id" : "") . "</p>";
        echo "<p>ID reçu: " . ($id ?? 'aucun') . "</p>";
    }
    
    /**
     * Méthode avec plusieurs paramètres
     * 
     * @param string $name
     * @param int $age
     */
    public function profile($name = null, $age = null)
    {
        echo "<h1>Test Profile</h1>";
        echo "<p>Route: /test/profile" . ($name ? "/$name" : "") . ($age ? "/$age" : "") . "</p>";
        echo "<p>Nom: " . ($name ?? 'inconnu') . "</p>";
        echo "<p>Âge: " . ($age ?? 'inconnu') . "</p>";
    }
    
    /**
     * Compatibilité ancien style (fallback)
     */
    public function control()
    {
        echo "<h1>Test Control (ancien style)</h1>";
        echo "<p>Route: /test</p>";
        echo "<p>Utilise la méthode control() par défaut</p>";
        echo "<hr>";
        echo "<h2>Tester le nouveau style :</h2>";
        echo "<ul>";
        echo "<li><a href='/test/index'>/test/index</a></li>";
        echo "<li><a href='/test/show'>/test/show</a></li>";
        echo "<li><a href='/test/show/42'>/test/show/42</a></li>";
        echo "<li><a href='/test/profile/John/25'>/test/profile/John/25</a></li>";
        echo "</ul>";
    }
}
