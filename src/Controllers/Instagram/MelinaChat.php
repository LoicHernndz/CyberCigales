<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    /**
     * Affiche la page du chat avec Melina
     * Les messages sont stockés en session PHP (pas de persistance en BDD)
     */
    function getMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialiser la session de chat si elle n'existe pas
        if (!isset($_SESSION['melina_chat_messages'])) {
            $model = new InstagramModel();
            $_SESSION['melina_chat_messages'] = $model->getDefaultMessages();
            $_SESSION['melina_chat_found_keys'] = [];
        }
        
        $view = new MelinaChatView();
        $model = new InstagramModel();
        
        // Récupérer les informations du profil de Melina
        $melinaInfo = $model->getMelinaProfile();
        $melinaInfo['status'] = 'En ligne';
        
        // Passer les données à la vue
        $view->addTemplateKey('MELINA_AVATAR', $melinaInfo['avatar']);
        $view->addTemplateKey('MELINA_DISPLAY_NAME', $melinaInfo['display_name']);
        $view->addTemplateKey('MELINA_STATUS', $melinaInfo['status']);
        $view->addTemplateKey('MESSAGES', '');
        
        $view->render();
    }
    
    /**
     * Gère l'envoi de messages et le chargement des messages existants
     * Utilise la session PHP pour stocker les messages (pas de BDD)
     */
    function postMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialiser la session de chat si elle n'existe pas
        if (!isset($_SESSION['melina_chat_messages'])) {
            $model = new InstagramModel();
            $_SESSION['melina_chat_messages'] = $model->getDefaultMessages();
            $_SESSION['melina_chat_found_keys'] = [];
        }
        
        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        $model = new InstagramModel();
        $response = ['success' => false, 'message' => ''];
        
        // Vérifier si c'est une demande de chargement des messages
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        if ($action === 'load') {
            $response = [
                'success' => true,
                'messages' => $_SESSION['melina_chat_messages'] ?? []
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        // Sinon, c'est un envoi de message
        $messageContent = trim($_POST['message'] ?? '');
        
        if (empty($messageContent)) {
            $response['message'] = 'Le message ne peut pas être vide';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
            header('Location: /instagram/melina/chat');
            exit;
        }
        
        // Ajouter le message de l'utilisateur à la session
        $userMessage = [
            'type' => 'sent',
            'content' => $messageContent,
            'time' => date('H:i')
        ];
        $_SESSION['melina_chat_messages'][] = $userMessage;
        
        // Vérifier si le message contient une clé
        $foundKeys = $_SESSION['melina_chat_found_keys'] ?? [];
        $keyResult = $model->checkMessageForKeys($messageContent, $foundKeys);
        
        if ($keyResult) {
            // Clé trouvée ! Réponse spéciale
            $melinaResponse = $keyResult['response'];
            // Marquer la clé comme trouvée
            $_SESSION['melina_chat_found_keys'][] = $keyResult['key'];
        } else {
            // Pas de clé, réponse par défaut
            $melinaResponse = $model->getDefaultResponse();
        }
        
        // Ajouter la réponse de Melina à la session
        $melinaMessage = [
            'type' => 'received',
            'content' => $melinaResponse,
            'time' => date('H:i')
        ];
        $_SESSION['melina_chat_messages'][] = $melinaMessage;
        
        // Préparer la réponse JSON
        $response['success'] = true;
        $response['userMessage'] = $userMessage;
        $response['melinaMessage'] = $melinaMessage;
        if ($keyResult) {
            $response['keyFound'] = true;
            $response['foundMessage'] = $keyResult['found_message'];
        }
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        // Si ce n'est pas AJAX, rediriger vers la page du chat
        header('Location: /instagram/melina/chat');
        exit;
    }
    
    public function support(string $method): bool
    {
        return $method === 'GET' || $method === 'POST';
    }
}
