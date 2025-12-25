<?php
/**
 * Script de test pour vérifier la sauvegarde des messages Instagram
 * À exécuter directement dans le navigateur : http://localhost/test_instagram_db.php
 */

require_once 'src/config/Autoloader.php';
use config\Database;
use Models\Instagram\InstagramModel;

// Démarrer la session
session_start();

echo "<h1>Test de sauvegarde Instagram</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Test 1: Vérifier que la table existe
    echo "<h2>Test 1: Vérification de la table</h2>";
    try {
        $db->query("SELECT COUNT(*) as count FROM instagram_messages");
        $result = $db->single();
        echo "<p style='color: green;'>✓ Table instagram_messages existe (contient " . $result->count . " messages)</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Table instagram_messages n'existe pas: " . $e->getMessage() . "</p>";
        echo "<p>Création de la table...</p>";
        $db->exec("CREATE TABLE IF NOT EXISTS instagram_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(255) NOT NULL DEFAULT '',
            type ENUM('sent', 'received') NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_session_id (session_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Table créée</p>";
    }
    
    // Test 2: Vérifier que la colonne session_id existe
    echo "<h2>Test 2: Vérification de la colonne session_id</h2>";
    try {
        $db->query("SELECT session_id FROM instagram_messages LIMIT 1");
        $db->execute();
        echo "<p style='color: green;'>✓ Colonne session_id existe</p>";
    } catch (\Exception $e) {
        echo "<p style='color: orange;'>⚠ Colonne session_id n'existe pas, ajout en cours...</p>";
        try {
            $db->exec("ALTER TABLE instagram_messages ADD COLUMN session_id VARCHAR(255) NOT NULL DEFAULT '' AFTER id");
            $db->exec("ALTER TABLE instagram_messages ADD INDEX idx_session_id (session_id)");
            echo "<p style='color: green;'>✓ Colonne session_id ajoutée</p>";
        } catch (\Exception $e2) {
            echo "<p style='color: red;'>✗ Erreur lors de l'ajout: " . $e2->getMessage() . "</p>";
        }
    }
    
    // Test 3: Tester l'insertion directe
    echo "<h2>Test 3: Test d'insertion directe</h2>";
    $testSessionId = 'test_' . time();
    $testContent = 'Message de test ' . date('H:i:s');
    
    try {
        $db->query("INSERT INTO instagram_messages (session_id, type, content, created_at) VALUES (:session_id, :type, :content, NOW())");
        $db->bind(':session_id', $testSessionId);
        $db->bind(':type', 'sent');
        $db->bind(':content', $testContent);
        $result = $db->execute();
        
        if ($result) {
            echo "<p style='color: green;'>✓ Insertion directe réussie</p>";
            echo "<p>Session ID: $testSessionId</p>";
            echo "<p>Contenu: $testContent</p>";
        } else {
            echo "<p style='color: red;'>✗ Insertion directe échouée (execute() retourne false)</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Erreur lors de l'insertion directe: " . $e->getMessage() . "</p>";
        echo "<p>Fichier: " . $e->getFile() . " Ligne: " . $e->getLine() . "</p>";
    }
    
    // Test 4: Tester via le modèle
    echo "<h2>Test 4: Test via InstagramModel</h2>";
    $model = new InstagramModel();
    $modelSessionId = 'model_test_' . time();
    $modelContent = 'Message via modèle ' . date('H:i:s');
    
    $saved = $model->saveMessage('sent', $modelContent, $modelSessionId);
    
    if ($saved) {
        echo "<p style='color: green;'>✓ Sauvegarde via modèle réussie</p>";
        echo "<p>Session ID: $modelSessionId</p>";
        echo "<p>Contenu: $modelContent</p>";
    } else {
        echo "<p style='color: red;'>✗ Sauvegarde via modèle échouée</p>";
    }
    
    // Test 5: Vérifier les messages sauvegardés
    echo "<h2>Test 5: Vérification des messages sauvegardés</h2>";
    try {
        $db->query("SELECT id, session_id, type, content, created_at FROM instagram_messages ORDER BY created_at DESC LIMIT 5");
        $messages = $db->resultSet();
        
        if (count($messages) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Session ID</th><th>Type</th><th>Contenu</th><th>Date</th></tr>";
            foreach ($messages as $msg) {
                echo "<tr>";
                echo "<td>" . $msg->id . "</td>";
                echo "<td>" . htmlspecialchars($msg->session_id) . "</td>";
                echo "<td>" . $msg->type . "</td>";
                echo "<td>" . htmlspecialchars(substr($msg->content, 0, 50)) . "</td>";
                echo "<td>" . $msg->created_at . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠ Aucun message trouvé dans la base</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Erreur lors de la lecture: " . $e->getMessage() . "</p>";
    }
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>✗ Erreur générale: " . $e->getMessage() . "</p>";
    echo "<p>Fichier: " . $e->getFile() . " Ligne: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

