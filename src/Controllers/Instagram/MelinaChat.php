<?php
namespace Controllers\Instagram;

use Views\Instagram\MelinaChatView;
use Models\Instagram\InstagramModel;
use Controllers\AbstractController;

class MelinaChat extends AbstractController
{
    function getMethod(){
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Récupérer l'ID de conversation depuis POST (envoyé par JavaScript) ou GET
        // Le JavaScript génère un ID unique par onglet dans sessionStorage
        $conversationId = $_POST['conv_id'] ?? $_GET['conv_id'] ?? '';
        
        // Si aucun ID n'est fourni, générer un nouveau (fallback)
        if (empty($conversationId)) {
            $conversationId = bin2hex(random_bytes(16)); // 32 caractères hexadécimaux
        }
        
        // Création des instances MVC
        $view = new MelinaChatView();
        $model = new InstagramModel();
        
        // ========================================
        // RÉCUPÉRATION DES DONNÉES VIA LE MODÈLE
        // ========================================
        $melinaInfo = $model->getMelinaProfile();
        $melinaInfo['status'] = 'En ligne'; // Ajout du statut pour le chat
        
        // Récupérer les messages pour cette conversation unique
        $chatMessages = $model->getMelinaChatMessages($conversationId);
        
        // Passer l'ID de conversation à la vue pour qu'il soit dans l'URL
        $view->addTemplateKey('CONVERSATION_ID', $conversationId);
        
        // ========================================
        // GÉNÉRATION DU HTML POUR LE HEADER DU CHAT
        // ========================================
        $view->addTemplateKey('MELINA_AVATAR', $melinaInfo['avatar']);
        $view->addTemplateKey('MELINA_DISPLAY_NAME', $melinaInfo['display_name']);
        $view->addTemplateKey('MELINA_STATUS', $melinaInfo['status']);

        // ========================================
        // GÉNÉRATION DU HTML POUR LES MESSAGES
        // ========================================
        $messagesHtml = '';
        foreach($chatMessages as $message) {
            $senderClass = ($message['type'] === 'sent') ? 'sent' : 'received';
            $messagesHtml .= '
            <div class="message ' . $senderClass . '">
                <div class="message-content">
                    <p>' . htmlspecialchars($message['content']) . '</p>
                    <span class="time">' . $message['time'] . '</span>
                </div>
            </div>';
        }
        
        // Passage des données à la vue
        $view->addTemplateKey('MESSAGES', $messagesHtml);
        
        $view->render();
    }
    
    function postMethod(){
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si c'est une requête AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        
        $model = new InstagramModel();
        $response = ['success' => false, 'message' => ''];
        
        // Récupérer le message depuis POST
        $messageContent = $_POST['message'] ?? '';
        $messageContent = trim($messageContent);
        
        // Récupérer l'ID de conversation depuis POST
        // FormData envoie les données dans $_POST, mais vérifions aussi le contenu brut
        $conversationId = $_POST['conv_id'] ?? '';
        
        // Debug : vérifier ce qui est reçu
        error_log("DEBUG POST data: " . print_r($_POST, true));
        error_log("DEBUG conv_id reçu: " . ($conversationId ?: 'VIDE'));
        
        // Si pas d'ID dans POST, essayer la session
        if (empty($conversationId)) {
            $conversationId = $_SESSION['instagram_conv_id'] ?? '';
            error_log("DEBUG conv_id depuis session: " . ($conversationId ?: 'VIDE'));
        }
        
        // Si toujours vide, générer un nouvel ID
        if (empty($conversationId)) {
            $conversationId = bin2hex(random_bytes(16));
            $_SESSION['instagram_conv_id'] = $conversationId;
            error_log("DEBUG Nouveau conv_id généré: " . $conversationId);
        }
        
        if (empty($messageContent)) {
            $response['message'] = 'Le message ne peut pas être vide';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($response);
                return;
            }
        }
        
        // Debug : logger l'ID de conversation utilisé
        error_log("Sauvegarde message avec conversationId: " . $conversationId);
        
        // Sauvegarder le message de l'utilisateur pour cette conversation
        $saved = $model->saveMessage('sent', $messageContent, $conversationId);
        
        // Debug : logger le résultat
        error_log("Résultat sauvegarde message: " . ($saved ? 'succès' : 'échec'));
        
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
            
            // Retourner l'ID de conversation dans la réponse
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
