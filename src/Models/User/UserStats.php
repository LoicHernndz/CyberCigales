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
        
        // Statistiques Cypher Rush
        $cypherStats = $this->getCypherStats($userId);
        
        // Activité récente
        $recentActivity = $this->getRecentActivity($userId);
        
        // Progression globale
        $progression = $this->getProgression($userId);
        
        return [
            'general' => $generalStats,
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
            COALESCE(score, 0) as total_score,
            date_inscription
        FROM users 
        WHERE id = :user_id');
        
        $this->db->bind(':user_id', $userId);
        $user = $this->db->single();
        
        if (!$user) {
            return [];
        }
        
        // Calculer le temps total passé (approximatif basé sur l'activité)
        $totalTime = $this->calculateTotalTime($userId);
        
        $membreDepuis = $user->date_inscription ? date('d/m/Y', strtotime($user->date_inscription)) : date('d/m/Y');
        
        // Calculer les jours depuis l'inscription
        $joursDepuis = 0;
        if ($user->date_inscription) {
            $diff = time() - strtotime($user->date_inscription);
            $joursDepuis = floor($diff / (60 * 60 * 24));
        }
        
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
        $cypherStats = $this->getCypherStats($userId);
        
        return [
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
            ]
        ];
    }

    /**
     * Calcule le temps total approximatif passé sur la plateforme
     */
    private function calculateTotalTime(int $userId): string
    {
        $totalMinutes = 0;
        
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
