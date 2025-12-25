-- Script SQL pour vérifier et corriger la structure de la table instagram_messages
-- À exécuter dans phpMyAdmin ou ton outil SQL préféré

-- 1. Vérifier la structure actuelle de la table
DESCRIBE instagram_messages;

-- 2. Vérifier si la colonne session_id existe
SHOW COLUMNS FROM instagram_messages LIKE 'session_id';

-- 3. Si la colonne n'existe pas, l'ajouter
ALTER TABLE `instagram_messages` 
ADD COLUMN `session_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`;

-- 4. Ajouter l'index sur session_id (si pas déjà présent)
ALTER TABLE `instagram_messages` 
ADD INDEX `idx_session_id` (`session_id`);

-- 5. Vérifier la structure finale
DESCRIBE instagram_messages;

-- 6. Test d'insertion pour vérifier que tout fonctionne
INSERT INTO `instagram_messages` (`session_id`, `type`, `content`, `created_at`) 
VALUES ('test_123', 'sent', 'Message de test', NOW());

-- 7. Vérifier que l'insertion a fonctionné
SELECT * FROM `instagram_messages` ORDER BY `created_at` DESC LIMIT 5;

-- 8. Nettoyer le message de test (optionnel)
-- DELETE FROM `instagram_messages` WHERE `session_id` = 'test_123';

