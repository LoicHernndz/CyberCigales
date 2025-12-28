<?php
namespace Controllers\DataBreach;

use Controllers\AbstractController;
use Models\DataBreach\DataBreachModel;
use Views\DataBreach\DataBreachCheckView;

/**
 * Contrôleur pour la vérification de fuites de données
 * Style "Have I Been Pwned"
 */
class DataBreachCheck extends AbstractController
{
    /**
     * Affiche la page de vérification
     */
    function getMethod()
    {
        $model = new DataBreachModel();
        $view = new DataBreachCheckView();
        
        $statistics = $model->getStatistics();
        $breaches = $model->getDataBreaches();
        
        $view->addTemplateKey('TOTAL_BREACHES', $statistics['total_breaches']);
        $view->addTemplateKey('TOTAL_RECORDS', $statistics['total_records']);
        $view->addTemplateKey('BREACHES_WITH_DECODING', $statistics['breaches_with_decoding']);
        
        $view->render();
    }
    
    /**
     * Gère la vérification d'email via AJAX
     */
    function postMethod()
    {
        header('Content-Type: application/json');
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email requis'
            ]);
            return;
        }
        
        // Validation basique de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Format d\'email invalide'
            ]);
            return;
        }
        
        $model = new DataBreachModel();
        $result = $model->checkEmail($email);
        
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    }
}

