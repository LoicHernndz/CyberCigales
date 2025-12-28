<?php
namespace Controllers\InterfaceMail;

use Views\InterfaceMail\InterfaceMailView;
use Models\InterfaceMail\InterfaceMailModel;
use Models\UnilateralDiscussion\UnilateralDiscussionModel;
use Controllers\AbstractController;

class InterfaceMail extends AbstractController
{
    function getMethod(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }
        
        $view = new InterfaceMailView();
        $mailModel = new InterfaceMailModel();
        $discussionModel = new UnilateralDiscussionModel();

        // 1. Récupérer les emails statiques
        $emails = $mailModel->getemail();
        
        // 2. Récupérer le dernier message reçu (si mode récepteur)
        $userId = $_SESSION['user_id'];
        $lastMessage = $discussionModel->getLastMessage($userId);
        
        // 3. Ajouter le message reçu aux emails si présent
        if ($lastMessage) {
            $senderUsername = $discussionModel->getUsername($lastMessage['sender_id']);
            $messageEmail = [
                "sender" => $senderUsername ?? "Utilisateur #" . $lastMessage['sender_id'],
                "subject" => "Message unilatéral",
                "time" => date('H:i', strtotime($lastMessage['updated_at'])),
                "snippet" => substr($lastMessage['message'], 0, 50) . '...',
                "content" => "<p>" . nl2br(htmlspecialchars($lastMessage['message'])) . "</p>",
                "is_unilateral" => true,
                "sender_id" => $lastMessage['sender_id']
            ];
            array_unshift($emails, $messageEmail); // Ajouter en premier
        }

        // 4. Passer les données à la vue
        $view->render($emails, $userId);
    }
    
    function postMethod(){
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }
        
        $action = $_POST['action'] ?? '';
        $userId = $_SESSION['user_id'];
        $model = new UnilateralDiscussionModel();
        
        switch ($action) {
            case 'send':
                $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
                $message = $_POST['message'] ?? '';
                
                if (empty($message) || $receiverId <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Données invalides']);
                    return;
                }
                
                $success = $model->sendMessage($userId, $receiverId, $message);
                echo json_encode(['success' => $success, 'message' => $success ? 'Message envoyé' : 'Erreur']);
                break;
                
            case 'receive':
                $lastMessage = $model->getLastMessage($userId);
                if ($lastMessage) {
                    $senderUsername = $model->getUsername($lastMessage['sender_id']);
                    echo json_encode([
                        'success' => true,
                        'has_message' => true,
                        'data' => [
                            'message' => $lastMessage['message'],
                            'sender_id' => $lastMessage['sender_id'],
                            'sender_username' => $senderUsername ?? 'Inconnu',
                            'updated_at' => $lastMessage['updated_at']
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => true, 'has_message' => false]);
                }
                break;
                
            case 'search_user':
                $pseudo = $_POST['pseudo'] ?? '';
                if (empty($pseudo)) {
                    echo json_encode(['success' => false, 'message' => 'Pseudo requis']);
                    return;
                }
                $user = $model->findUserByPseudo($pseudo);
                echo json_encode(['success' => $user !== null, 'data' => $user]);
                break;
                
            case 'search_users':
                $search = $_POST['search'] ?? '';
                $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
                if (empty($search)) {
                    echo json_encode(['success' => false, 'message' => 'Terme requis']);
                    return;
                }
                $users = $model->searchUsersByPseudo($search, $limit);
                echo json_encode(['success' => true, 'data' => $users]);
                break;
                
            case 'disconnect':
                // Déconnexion : suppression de la ligne de la BDD
                $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
                if ($receiverId > 0) {
                    $model->disconnect($userId, $receiverId);
                } else {
                    $model->disconnectAll($userId);
                }
                echo json_encode(['success' => true, 'message' => 'Déconnecté']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Action invalide']);
        }
    }
}