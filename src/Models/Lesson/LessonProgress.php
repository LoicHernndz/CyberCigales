<?php

namespace Models\Lesson;

use config\Database;

class LessonProgress
{
    private $db;

    private const REQUIRED_LESSONS = ['cesar', 'vigenere', 'permutation'];

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS lesson_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            lesson_slug VARCHAR(50) NOT NULL,
            completed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_lesson (user_id, lesson_slug),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        $this->db->execute();
    }

    public function markCompleted(int $userId, string $slug): void
    {
        $this->db->query('INSERT IGNORE INTO lesson_progress (user_id, lesson_slug) VALUES (:user_id, :slug)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':slug', $slug);
        $this->db->execute();
    }

    public function isCompleted(int $userId, string $slug): bool
    {
        $this->db->query('SELECT id FROM lesson_progress WHERE user_id = :user_id AND lesson_slug = :slug');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':slug', $slug);
        return (bool) $this->db->single();
    }

    public function getCompletedLessons(int $userId): array
    {
        $this->db->query('SELECT lesson_slug FROM lesson_progress WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $results = $this->db->resultSet();
        return array_map(fn($row) => $row->lesson_slug, $results);
    }

    public function areRequiredLessonsCompleted(int $userId): bool
    {
        $completed = $this->getCompletedLessons($userId);
        foreach (self::REQUIRED_LESSONS as $lesson) {
            if (!in_array($lesson, $completed)) {
                return false;
            }
        }
        return true;
    }

    public function getMissingLessons(int $userId): array
    {
        $completed = $this->getCompletedLessons($userId);
        $labels = ['cesar' => 'César', 'vigenere' => 'Vigenère', 'permutation' => 'Permutation'];
        $missing = [];
        foreach (self::REQUIRED_LESSONS as $lesson) {
            if (!in_array($lesson, $completed)) {
                $missing[] = $labels[$lesson];
            }
        }
        return $missing;
    }

    public function resetProgress(int $userId): void
    {
        $this->db->query('DELETE FROM lesson_progress WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }

    public function getRequiredLessons(): array
    {
        return self::REQUIRED_LESSONS;
    }
}
