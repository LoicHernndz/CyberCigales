<?php

namespace Views\User\Profil;

use Views\AbstractView;

class ProfilView extends AbstractView {
    private $stats;
    private $badges;
    private $rank;

    public function __construct($stats = [], $badges = [], $rank = [])
    {
        $this->stats = $stats;
        $this->badges = $badges;
        $this->rank = $rank;
    }

    // Méthode qui retourne le chemin du fichier HTML à utiliser pour le rendu
    public function templatePath() : string {
        return __DIR__ . '/profil.html';
    }

    // Méthode qui fournit les variables à injecter dans le template HTML
    public function templateKeys() : array {
        $keys = [];
        
        // Informations utilisateur de base
        if (!empty($this->stats['general'])) {
            $keys['FIRSTNAME_KEY'] = $this->stats['general']['prenom'];
            $keys['LASTNAME_KEY'] = $this->stats['general']['nom'];
            $keys['USERNAME_KEY'] = $this->stats['general']['pseudo'];
            $keys['EMAIL_KEY'] = $this->stats['general']['email'];
            $keys['MEMBRE_DEPUIS'] = $this->stats['general']['membre_depuis'];
        } else {
            $keys['FIRSTNAME_KEY'] = 'Invité';
            $keys['LASTNAME_KEY'] = '';
            $keys['USERNAME_KEY'] = 'Invité';
            $keys['EMAIL_KEY'] = '';
            $keys['MEMBRE_DEPUIS'] = '';
        }
        
        // Statistiques principales
        $keys['SCORE_KEY'] = $this->stats['general']['total_score'] ?? 0;
        $keys['TIME_KEY'] = $this->stats['general']['total_time'] ?? '0min';
        $keys['BADGES_KEY'] = count($this->badges);
        
        // Statistiques RGPD
        $keys['RGPD_PROGRESS'] = $this->stats['rgpd']['completion'] ?? 0;
        $keys['RGPD_SCORE'] = $this->stats['rgpd']['score'] ?? 0;
        $keys['RGPD_ACCURACY'] = $this->stats['rgpd']['accuracy'] ?? 0;
        $keys['RGPD_QUESTIONS'] = $this->stats['rgpd']['questions_answered'] ?? 0;
        $keys['RGPD_TOTAL_QUESTIONS'] = $this->stats['rgpd']['total_questions'] ?? 0;
        
        // Statistiques Cypher Rush
        $keys['CYPHER_GAMES'] = $this->stats['cypher']['games_played'] ?? 0;
        $keys['CYPHER_BEST_SCORE'] = $this->stats['cypher']['best_score'] ?? 0;
        $keys['CYPHER_AVG_SCORE'] = $this->stats['cypher']['avg_score'] ?? 0;
        
        // Classement
        $keys['GLOBAL_RANK'] = $this->rank['global_rank'] ?? 0;
        $keys['TOTAL_USERS'] = $this->rank['total_users'] ?? 0;
        $keys['PERCENTILE'] = $this->rank['percentile'] ?? 0;
        
        // Activité récente (JSON pour JavaScript)
        $keys['ACTIVITY_DATA'] = json_encode($this->stats['activity'] ?? []);
        
        // Progression par module (JSON pour JavaScript)
        $keys['PROGRESSION_DATA'] = json_encode($this->stats['progression'] ?? []);
        
        // Badges (HTML)
        $keys['BADGES_HTML'] = $this->renderBadges();
        
        // Stats détaillées RGPD (HTML)
        $keys['RGPD_STATS_HTML'] = $this->renderRgpdStats();
        
        // Stats détaillées Cypher (HTML)
        $keys['CYPHER_STATS_HTML'] = $this->renderCypherStats();
        
        return $keys;
    }

    private function renderBadges(): string
    {
        if (empty($this->badges)) {
            return '<div class="no-badges">
                <span class="material-icons">lock</span>
                <p>Aucun badge débloqué pour le moment</p>
            </div>';
        }

        $html = '';
        foreach ($this->badges as $badge) {
            $html .= '<div class="achievement unlocked">
                <span class="material-icons">' . htmlspecialchars($badge['icon']) . '</span>
                <p>' . htmlspecialchars($badge['name']) . '</p>
                <span class="badge-tooltip">' . htmlspecialchars($badge['description']) . '</span>
            </div>';
        }

        return $html;
    }

    private function renderRgpdStats(): string
    {
        $rgpd = $this->stats['rgpd'] ?? [];
        
        return '<div class="stat-detail">
            <span class="material-icons">quiz</span>
            <span>Questions répondues : ' . ($rgpd['questions_answered'] ?? 0) . '/' . ($rgpd['total_questions'] ?? 0) . '</span>
        </div>
        <div class="stat-detail">
            <span class="material-icons">check_circle</span>
            <span>Bonnes réponses : ' . ($rgpd['correct_answers'] ?? 0) . '</span>
        </div>
        <div class="stat-detail">
            <span class="material-icons">percent</span>
            <span>Précision : ' . ($rgpd['accuracy'] ?? 0) . '%</span>
        </div>';
    }

    private function renderCypherStats(): string
    {
        $cypher = $this->stats['cypher'] ?? [];
        
        if (($cypher['games_played'] ?? 0) === 0) {
            return '<div class="stat-detail">
                <span class="material-icons">info</span>
                <span>Aucune partie jouée</span>
            </div>';
        }

        $bestTime = ($cypher['best_time'] ?? 0);
        $timeFormatted = gmdate("i:s", $bestTime);
        
        return '<div class="stat-detail">
            <span class="material-icons">videogame_asset</span>
            <span>Parties jouées : ' . ($cypher['games_played'] ?? 0) . '</span>
        </div>
        <div class="stat-detail">
            <span class="material-icons">star</span>
            <span>Meilleur score : ' . ($cypher['best_score'] ?? 0) . '</span>
        </div>
        <div class="stat-detail">
            <span class="material-icons">timer</span>
            <span>Meilleur temps : ' . $timeFormatted . '</span>
        </div>';
    }
}
