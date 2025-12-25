<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    /**
     * Affiche la page du chat avec Melina
     * Les messages sont chargés via AJAX par le JavaScript avec le bon conversationId
     */
    function getMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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
        // Les messages seront chargés via AJAX par le JavaScript
        $view->addTemplateKey('MESSAGES', '');
        
        $view->render();
    }
    
    /**
     * Gère l'envoi de messages et le chargement des messages existants
     * Reçoit les requêtes AJAX du JavaScript
     */
    function postMethod(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        $model = new InstagramModel();
        $response = ['success' => false, 'message' => ''];
        
        // Récupérer l'ID de conversation depuis POST (obligatoire)
        $conversationId = $_POST['conv_id'] ?? '';
        
        if (empty($conversationId)) {
            $response['message'] = 'ID de conversation manquant';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
            header('Location: /instagram/melina/chat');
            exit;
        }
        
        // Vérifier si c'est une demande de chargement des messages
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        if ($action === 'load') {
            $messages = $model->getMelinaChatMessages($conversationId);
            $response = [
                'success' => true,
                'messages' => $messages
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
        }
        
        // Sauvegarder le message de l'utilisateur pour cette conversation
        $saved = $model->saveMessage('sent', $messageContent, $conversationId);
        
        if ($saved) {
            // Générer une réponse automatique de Melina
            $responses = [
                "C'est super ! 😊",
                "J'adore ça ! ✨",
                "Merci pour ton message ! 💕",
                "C'est génial ! 🎉",
                "Parfait ! 👍",
                "J'aime beaucoup ! 💖",
                "C'est magnifique ! 🌟",
                "Excellent ! 👏",
                "Trop cool ! 🔥",
                "J'adore ! 😍"
            ];
            $melinaResponse = $responses[array_rand($responses)];
            
            // Sauvegarder la réponse de Melina pour cette conversation
            $model->saveMessage('received', $melinaResponse, $conversationId);
            
            // Retourner l'ID de conversation et les messages dans la réponse
            $response['conv_id'] = $conversationId;
            $response['success'] = true;
            $response['userMessage'] = [
                'type' => 'sent',
                'content' => $messageContent,
                'time' => date('H:i')
            ];
            $response['melinaMessage'] = [
                'type' => 'received',
                'content' => $melinaResponse,
                'time' => date('H:i')
            ];
        } else {
            $response['message'] = 'Erreur lors de la sauvegarde du message';
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
