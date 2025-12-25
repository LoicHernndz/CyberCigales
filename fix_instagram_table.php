<?php
/**
 * Script pour vérifier et corriger automatiquement la structure de la table instagram_messages
 * À exécuter une seule fois : http://localhost/fix_instagram_table.php
 */

require_once 'src/config/Autoloader.php';
use config\Database;

echo "<h1>Correction de la table instagram_messages</h1>";

try {
    $db = new Database();
    
    // 1. Vérifier si la table existe
    echo "<h2>Étape 1: Vérification de l'existence de la table</h2>";
    try {
        $db->query("SELECT COUNT(*) as count FROM instagram_messages");
        $result = $db->single();
        echo "<p style='color: green;'>✓ Table instagram_messages existe (contient " . $result->count . " messages)</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Table n'existe pas, création en cours...</p>";
        $createQuery = "CREATE TABLE IF NOT EXISTS instagram_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(255) NOT NULL DEFAULT '',
            type ENUM('sent', 'received') NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_session_id (session_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $db->exec($createQuery);
        echo "<p style='color: green;'>✓ Table créée</p>";
    }
    
    // 2. Vérifier si la colonne session_id existe
    echo "<h2>Étape 2: Vérification de la colonne session_id</h2>";
    try {
        // Vérifier via INFORMATION_SCHEMA
        $db->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'instagram_messages' 
                   AND COLUMN_NAME = 'session_id'");
        $result = $db->single();
        
        if ($result && $result->count > 0) {
            echo "<p style='color: green;'>✓ Colonne session_id existe déjà</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Colonne session_id n'existe pas, ajout en cours...</p>";
            
            // Ajouter la colonne
            try {
                $db->exec("ALTER TABLE instagram_messages ADD COLUMN session_id VARCHAR(255) NOT NULL DEFAULT '' AFTER id");
                echo "<p style='color: green;'>✓ Colonne session_id ajoutée</p>";
            } catch (\Exception $e) {
                echo "<p style='color: red;'>✗ Erreur lors de l'ajout de la colonne: " . $e->getMessage() . "</p>";
            }
            
            // Ajouter l'index
            try {
                $db->exec("ALTER TABLE instagram_messages ADD INDEX idx_session_id (session_id)");
                echo "<p style='color: green;'>✓ Index sur session_id ajouté</p>";
            } catch (\Exception $e) {
                echo "<p style='color: orange;'>⚠ Index peut-être déjà existant: " . $e->getMessage() . "</p>";
            }
        }
    } catch (\Exception $e) {
        echo "<p style='color: orange;'>⚠ Vérification via INFORMATION_SCHEMA échouée, tentative d'ajout direct...</p>";
        try {
            $db->exec("ALTER TABLE instagram_messages ADD COLUMN session_id VARCHAR(255) NOT NULL DEFAULT '' AFTER id");
            $db->exec("ALTER TABLE instagram_messages ADD INDEX idx_session_id (session_id)");
            echo "<p style='color: green;'>✓ Colonne et index ajoutés (méthode fallback)</p>";
        } catch (\Exception $e2) {
            if (strpos($e2->getMessage(), 'Duplicate column') !== false) {
                echo "<p style='color: green;'>✓ Colonne existe déjà</p>";
            } else {
                echo "<p style='color: red;'>✗ Erreur: " . $e2->getMessage() . "</p>";
            }
        }
    }
    
    // 3. Vérifier la structure finale
    echo "<h2>Étape 3: Structure finale de la table</h2>";
    try {
        $db->query("DESCRIBE instagram_messages");
        $columns = $db->resultSet();
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td><strong>" . $col->Field . "</strong></td>";
            echo "<td>" . $col->Type . "</td>";
            echo "<td>" . $col->Null . "</td>";
            echo "<td>" . $col->Key . "</td>";
            echo "<td>" . ($col->Default ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Erreur lors de la vérification: " . $e->getMessage() . "</p>";
    }
    
    // 4. Test d'insertion
    echo "<h2>Étape 4: Test d'insertion</h2>";
    $testSessionId = 'fix_test_' . time();
    $testContent = 'Message de test de correction ' . date('H:i:s');
    
    try {
        $db->query("INSERT INTO instagram_messages (session_id, type, content, created_at) VALUES (:session_id, :type, :content, NOW())");
        $db->bind(':session_id', $testSessionId);
        $db->bind(':type', 'sent');
        $db->bind(':content', $testContent);
        $result = $db->execute();
        
        if ($result) {
            echo "<p style='color: green;'>✓ Insertion de test réussie !</p>";
            echo "<p>Session ID: <code>$testSessionId</code></p>";
            echo "<p>Contenu: <code>$testContent</code></p>";
            
            // Vérifier que le message a bien été inséré
            $db->query("SELECT * FROM instagram_messages WHERE session_id = :session_id");
            $db->bind(':session_id', $testSessionId);
            $inserted = $db->single();
            
            if ($inserted) {
                echo "<p style='color: green;'>✓ Message vérifié dans la base de données</p>";
                echo "<p>ID du message: " . $inserted->id . "</p>";
                
                // Nettoyer le message de test
                $db->query("DELETE FROM instagram_messages WHERE session_id = :session_id");
                $db->bind(':session_id', $testSessionId);
                $db->execute();
                echo "<p style='color: blue;'>ℹ Message de test supprimé</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Insertion échouée (execute() retourne false)</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color: red;'>✗ Erreur lors de l'insertion de test: " . $e->getMessage() . "</p>";
        echo "<p>Fichier: " . $e->getFile() . " Ligne: " . $e->getLine() . "</p>";
    }
    
    echo "<h2 style='color: green;'>✓ Correction terminée !</h2>";
    echo "<p>Tu peux maintenant tester l'envoi de messages dans l'application Instagram.</p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>✗ Erreur générale: " . $e->getMessage() . "</p>";
    echo "<p>Fichier: " . $e->getFile() . " Ligne: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

