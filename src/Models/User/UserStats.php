<?php

namespace Models\User;

use config\Database;

/**
 * Modèle pour gérer les statistiques utilisateur
 */
class UserStats
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Récupère toutes les statistiques d'un utilisateur
     */
    public function getUserStats(int $userId): array
    {
        // Statistiques générales
        $generalStats = $this->getGeneralStats($userId);
        
        // Statistiques QCM RGPD
        $rgpdStats = $this->getRgpdStats($userId);
        
        // Statistiques Cypher Rush
        $cypherStats = $this->getCypherStats($userId);
        
        // Activité récente
        $recentActivity = $this->getRecentActivity($userId);
        
        // Progression globale
        $progression = $this->getProgression($userId);
        
        return [
            'general' => $generalStats,
            'rgpd' => $rgpdStats,
            'cypher' => $cypherStats,
            'activity' => $recentActivity,
            'progression' => $progression
        ];
    }

    /**
     * Statistiques générales de l'utilisateur
     */
    private function getGeneralStats(int $userId): array
    {
        $this->db->query('SELECT 
            id,
            prenom,
            nom,
            pseudo,
            email,
            COALESCE(score, 0) as total_score
        FROM users 
        WHERE id = :user_id');
        
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();
        
        if (!$user) {
            return [];
        }
        
        // Calculer le temps total passé (approximatif basé sur l'activité)
        $totalTime = $this->calculateTotalTime($userId);
        
        // Calculer la date d'inscription approximative basée sur la première activité
        $membreDepuis = $this->getFirstActivityDate($userId);
        $joursDepuis = $this->calculateDaysSinceFirstActivity($userId);
        
        return [
            'id' => $user->id,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'pseudo' => $user->pseudo,
            'email' => $user->email,
            'total_score' => (int)$user->total_score,
            'membre_depuis' => $membreDepuis,
            'jours_membre' => $joursDepuis,
            'total_time' => $totalTime
        ];
    }

    /**
     * Récupère la date de première activité de l'utilisateur
     */
    private function getFirstActivityDate(int $userId): string
    {
        try {
            // Chercher la première activité dans user_qcm_progress
            $this->db->query('
                SELECT MIN(answered_at) as first_date 
                FROM user_qcm_progress 
                WHERE user_id = :user_id
            ');
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            if ($result && $result->first_date) {
                return date('d/m/Y', strtotime($result->first_date));
            }
        } catch (\Exception $e) {
            // Table n'existe pas ou erreur
        }
        
        return date('d/m/Y'); // Date du jour si pas d'activité
    }

    /**
     * Calcule le nombre de jours depuis la première activité
     */
    private function calculateDaysSinceFirstActivity(int $userId): int
    {
        try {
            $this->db->query('
                SELECT DATEDIFF(NOW(), MIN(answered_at)) as days_count 
                FROM user_qcm_progress 
                WHERE user_id = :user_id
            ');
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();
            
            if ($result && $result->days_count) {
                return (int)$result->days_count;
            }
        } catch (\Exception $e) {
            // Table n'existe pas ou erreur
        }
        
        return 0;
    }

    /**
     * Statistiques RGPD
     */
    private function getRgpdStats(int $userId): array
    {
        // Nombre de questions répondues
        $this->db->query('SELECT 
            COUNT(*) as questions_answered,
            SUM(CASE WHEN est_correcte = 1 THEN 1 ELSE 0 END) as correct_answers,
            COALESCE(SUM(points_obtenus), 0) as rgpd_score,
            MAX(answered_at) as last_attempt
        FROM user_qcm_progress 
        WHERE user_id = :user_id');
        
        $this->db->bind(':user_id', $userId);
        $stats = $this->db->single();
        
        // Nombre total de questions disponibles
        $this->db->query('SELECT COUNT(*) as total_questions FROM qcm_questions WHERE est_active = 1');
        $totalQuestions = $this->db->single();
        
        $questionsAnswered = (int)($stats->questions_answered ?? 0);
        $correctAnswers = (int)($stats->correct_answers ?? 0);
        $totalQuestionsCount = (int)($totalQuestions->total_questions ?? 0);
        
        $completion = $totalQuestionsCount > 0 
            ? round(($questionsAnswered / $totalQuestionsCount) * 100) 
            : 0;
        
        $accuracy = $questionsAnswered > 0 
            ? round(($correctAnswers / $questionsAnswered) * 100) 
            : 0;
        
        return [
            'questions_answered' => $questionsAnswered,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestionsCount,
            'score' => (int)($stats->rgpd_score ?? 0),
            'completion' => $completion,
            'accuracy' => $accuracy,
            'last_attempt' => $stats->last_attempt ?? null
        ];
    }

    /**
     * Statistiques Cypher Rush
     */
    private function getCypherStats(int $userId): array
    {
        try {
            $this->db->query('SELECT 
                COUNT(*) as games_played,
                MAX(score) as best_score,
                AVG(score) as avg_score,
                MIN(time_elapsed) as best_time,
                SUM(hints_used) as total_hints_used,
                MAX(played_at) as last_played
            FROM cypher_scores 
            WHERE user_id = :user_id');
            
            $this->db->bind(':user_id', $userId);
            $stats = $this->db->single();
            
            return [
                'games_played' => (int)($stats->games_played ?? 0),
                'best_score' => (int)($stats->best_score ?? 0),
                'avg_score' => round($stats->avg_score ?? 0),
                'best_time' => (int)($stats->best_time ?? 0),
                'total_hints_used' => (int)($stats->total_hints_used ?? 0),
                'last_played' => $stats->last_played ?? null
            ];
        } catch (\Exception $e) {
            // Table cypher_scores n'existe pas encore
            return [
                'games_played' => 0,
                'best_score' => 0,
                'avg_score' => 0,
                'best_time' => 0,
                'total_hints_used' => 0,
                'last_played' => null
            ];
        }
    }

    /**
     * Activité récente (7 derniers jours)
     */
    private function getRecentActivity(int $userId): array
    {
        $activities = [];
        
        // Activité QCM
        try {
            $this->db->query('SELECT 
                DATE(answered_at) as date,
                COUNT(*) as count,
                "rgpd" as type
            FROM user_qcm_progress 
            WHERE user_id = :user_id 
            AND answered_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(answered_at)');
            
            $this->db->bind(':user_id', $userId);
            $rgpdActivity = $this->db->resultSet();
            $activities = array_merge($activities, $rgpdActivity);
        } catch (\Exception $e) {
            // Table n'existe pas
        }
        
        // Activité Cypher (si la table existe)
        try {
            $this->db->query('SELECT 
                DATE(played_at) as date,
                COUNT(*) as count,
                "cypher" as type
            FROM cypher_scores 
            WHERE user_id = :user_id 
            AND played_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(played_at)');
            
            $this->db->bind(':user_id', $userId);
            $cypherActivity = $this->db->resultSet();
            $activities = array_merge($activities, $cypherActivity);
        } catch (\Exception $e) {
            // Table cypher_scores n'existe pas encore
        }
        
        // Organiser par date pour les 7 derniers jours
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[$date] = [
                'date' => date('d/m', strtotime($date)),
                'rgpd' => 0,
                'cypher' => 0,
                'total' => 0
            ];
        }
        
        foreach ($activities as $activity) {
            $date = $activity->date;
            if (isset($dates[$date])) {
                $dates[$date][$activity->type] = (int)$activity->count;
                $dates[$date]['total'] += (int)$activity->count;
            }
        }
        
        return array_values($dates);
    }

    /**
     * Progression par module
     */
    private function getProgression(int $userId): array
    {
        $rgpdStats = $this->getRgpdStats($userId);
        $cypherStats = $this->getCypherStats($userId);
        
        return [
            'rgpd' => [
                'name' => 'Formation RGPD',
                'completion' => $rgpdStats['completion'],
                'score' => $rgpdStats['score'],
                'status' => $rgpdStats['completion'] >= 100 ? 'completed' : 'in_progress'
            ],
            'cypher' => [
                'name' => 'Cypher Rush',
                'completion' => $cypherStats['games_played'] > 0 ? 100 : 0,
                'score' => $cypherStats['best_score'],
                'status' => $cypherStats['games_played'] > 0 ? 'completed' : 'locked'
            ],
            'password' => [
                'name' => 'Password Fortress',
                'completion' => 0,
                'score' => 0,
                'status' => 'locked'
            ],
            'phishing' => [
                'name' => 'Phishing Detective',
                'completion' => 0,
                'score' => 0,
                'status' => 'locked'
            ]
        ];
    }

    /**
     * Calcule le temps total approximatif passé sur la plateforme
     */
    private function calculateTotalTime(int $userId): string
    {
        $totalMinutes = 0;
        
        // Temps estimé par question RGPD : 2 minutes
        try {
            $this->db->query('SELECT COUNT(*) as count FROM user_qcm_progress WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            $rgpdCount = $this->db->single()->count ?? 0;
            $totalMinutes += $rgpdCount * 2;
        } catch (\Exception $e) {
            // Table n'existe pas
        }
        
        // Temps Cypher Rush (en secondes dans la BDD)
        try {
            $this->db->query('SELECT COALESCE(SUM(time_elapsed), 0) as total_seconds FROM cypher_scores WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
            $cypherSeconds = $this->db->single()->total_seconds ?? 0;
            $totalMinutes += ceil($cypherSeconds / 60);
        } catch (\Exception $e) {
            // Table cypher_scores n'existe pas encore
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h" . ($minutes > 0 ? " {$minutes}min" : "");
        } else {
            return "{$minutes}min";
        }
    }

    /**
     * Récupère les badges débloqués
     */
    public function getBadges(int $userId): array
    {
        $stats = $this->getUserStats($userId);
        $badges = [];
        
        // Badge Premier Pas
        if ($stats['general']['total_score'] > 0) {
            $badges[] = [
                'id' => 'first_step',
                'name' => 'Premier Pas',
                'icon' => 'emoji_events',
                'description' => 'Première activité complétée',
                'unlocked' => true
            ];
        }
        
        // Badge Expert RGPD
        if ($stats['rgpd']['completion'] >= 100 && $stats['rgpd']['accuracy'] >= 80) {
            $badges[] = [
                'id' => 'rgpd_expert',
                'name' => 'Expert RGPD',
                'icon' => 'verified_user',
                'description' => '100% du QCM RGPD avec 80% de bonnes réponses',
                'unlocked' => true
            ];
        }
        
        // Badge Crypto Master
        if ($stats['cypher']['games_played'] >= 5 && $stats['cypher']['best_score'] >= 800) {
            $badges[] = [
                'id' => 'crypto_master',
                'name' => 'Crypto Master',
                'icon' => 'vpn_key',
                'description' => '5 parties de Cypher Rush avec un score de 800+',
                'unlocked' => true
            ];
        }
        
        // Badge Marathon
        if ($stats['general']['jours_membre'] >= 30) {
            $badges[] = [
                'id' => 'marathon',
                'name' => 'Marathon',
                'icon' => 'local_fire_department',
                'description' => '30 jours d\'activité',
                'unlocked' => true
            ];
        }
        
        return $badges;
    }

    /**
     * Récupère le classement de l'utilisateur
     */
    public function getUserRank(int $userId): array
    {
        // Rang global
        $this->db->query('SELECT COUNT(*) + 1 as rank 
                         FROM users 
                         WHERE score > (SELECT score FROM users WHERE id = :user_id)');
        $this->db->bind(':user_id', $userId);
        $globalRank = $this->db->single()->rank ?? 0;
        
        // Total d'utilisateurs
        $this->db->query('SELECT COUNT(*) as total FROM users');
        $totalUsers = $this->db->single()->total ?? 0;
        
        return [
            'global_rank' => $globalRank,
            'total_users' => $totalUsers,
            'percentile' => $totalUsers > 0 ? round((1 - ($globalRank / $totalUsers)) * 100) : 0
        ];
    }
}

