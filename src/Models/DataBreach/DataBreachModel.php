<?php
namespace Models\DataBreach;

/**
 * Modèle pour la vérification de fuites de données
 * Inspiré de Have I Been Pwned et basé sur des fuites réelles
 * où les algorithmes de décodage ont été divulgués
 */
class DataBreachModel
{
    /**
     * Liste des fuites de données majeures avec algorithmes de décodage divulgués
     * 
     * @return array Liste des fuites avec leurs détails
     */
    public function getDataBreaches(): array
    {
        return [
            [
                'name' => 'LinkedIn 2012',
                'date' => 'Mai 2012',
                'records' => '164 millions',
                'description' => 'Fuite massive où les mots de passe étaient hashés avec SHA-1 sans salt. L\'algorithme de décodage a été divulgué, permettant de décrypter 90% des mots de passe.',
                'algorithm' => 'SHA-1 (non salé)',
                'status' => 'Algorithme de décodage divulgué'
            ],
            [
                'name' => 'Adobe 2013',
                'date' => 'Octobre 2013',
                'records' => '153 millions',
                'description' => 'Fuite où les mots de passe étaient protégés par un chiffrement réversible. L\'algorithme de décodage a été publié sur des forums de hackers.',
                'algorithm' => 'Chiffrement réversible (3DES)',
                'status' => 'Algorithme de décodage divulgué'
            ],
            [
                'name' => 'MySpace 2016',
                'date' => 'Mai 2016',
                'records' => '427 millions',
                'description' => 'Fuite où les mots de passe utilisaient SHA-1 avec un salt faible. L\'algorithme de décodage a été reverse-engineered et partagé.',
                'algorithm' => 'SHA-1 (salt faible)',
                'status' => 'Algorithme de décodage divulgué'
            ],
            [
                'name' => 'CyberCigales Internal 2024',
                'date' => 'Janvier 2024',
                'records' => '12 500',
                'description' => 'Fuite interne où un développeur a accidentellement commité l\'algorithme de décodage dans un dépôt public. Les données ont été décryptées en 48h.',
                'algorithm' => 'César + Vigenère combinés',
                'status' => 'Algorithme de décodage divulgué'
            ]
        ];
    }
    
    /**
     * Liste d'emails compromis dans les différentes fuites
     * Dans un vrai système, cela viendrait d'une base de données
     * 
     * @return array Emails compromis par fuite
     */
    public function getCompromisedEmails(): array
    {
        return [
            'melina@cybercigales.fr' => [
                'breaches' => ['LinkedIn 2012', 'Adobe 2013', 'CyberCigales Internal 2024'],
                'total' => 3
            ],
            'admin@cybercigales.fr' => [
                'breaches' => ['CyberCigales Internal 2024'],
                'total' => 1
            ],
            'contact@cybercigales.fr' => [
                'breaches' => ['MySpace 2016', 'CyberCigales Internal 2024'],
                'total' => 2
            ],
            'test@example.com' => [
                'breaches' => ['LinkedIn 2012'],
                'total' => 1
            ],
            'demo@cybercigales.fr' => [
                'breaches' => ['Adobe 2013', 'MySpace 2016'],
                'total' => 2
            ]
        ];
    }
    
    /**
     * Vérifie si un email a été compromis dans une ou plusieurs fuites
     * 
     * @param string $email Email à vérifier
     * @return array|null Informations sur les fuites ou null si non compromis
     */
    public function checkEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        $compromised = $this->getCompromisedEmails();
        
        if (isset($compromised[$email])) {
            $breachInfo = $compromised[$email];
            $allBreaches = $this->getDataBreaches();
            
            $breachDetails = [];
            foreach ($breachInfo['breaches'] as $breachName) {
                foreach ($allBreaches as $breach) {
                    if ($breach['name'] === $breachName) {
                        $breachDetails[] = $breach;
                        break;
                    }
                }
            }
            
            return [
                'email' => $email,
                'compromised' => true,
                'breach_count' => $breachInfo['total'],
                'breaches' => $breachDetails
            ];
        }
        
        return [
            'email' => $email,
            'compromised' => false,
            'breach_count' => 0,
            'breaches' => []
        ];
    }
    
    /**
     * Retourne les statistiques globales des fuites
     * 
     * @return array Statistiques
     */
    public function getStatistics(): array
    {
        $breaches = $this->getDataBreaches();
        $totalRecords = 0;
        
        foreach ($breaches as $breach) {
            $records = str_replace([' millions', ' milliers', ','], '', $breach['records']);
            $totalRecords += (float)$records * 1000000;
        }
        
        return [
            'total_breaches' => count($breaches),
            'total_records' => number_format($totalRecords, 0, ',', ' '),
            'breaches_with_decoding' => count($breaches) // Toutes ont l'algorithme divulgué
        ];
    }
}

