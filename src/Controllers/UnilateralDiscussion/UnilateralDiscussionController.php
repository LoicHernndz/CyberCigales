<?php
namespace Controllers\UnilateralDiscussion;

use Controllers\AbstractController;
use Models\UnilateralDiscussion\UnilateralDiscussionModel;
use Views\UnilateralDiscussion\UnilateralDiscussionView;

/**
 * Contrôleur pour la discussion unilatérale
 * Gère l'envoi et la réception de messages sans historique
 */
class UnilateralDiscussionController extends AbstractController
{
    /**
     * Affiche la page de discussion
     */
    function getMethod()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit();
        }
        
        $view = new UnilateralDiscussionView();
        $model = new UnilateralDiscussionModel();
        
        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? 'Utilisateur';
        
        // Récupérer le mode (diffuseur ou récepteur)
        $mode = $_GET['mode'] ?? 'sender'; // 'sender' ou 'receiver'
        
        // Récupérer l'ID du destinataire si en mode diffuseur
        $receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;
        
        $view->addTemplateKey('USER_ID', $userId);
        $view->addTemplateKey('USERNAME', htmlspecialchars($username));
        $view->addTemplateKey('MODE', $mode);
        $view->addTemplateKey('RECEIVER_ID', $receiverId ?? '');
        
        $view->render();
    }
    
    /**
     * Gère l'envoi et la réception de messages via AJAX
     */
    function postMethod()
    {
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Non authentifié'
            ]);
            return;
        }
        
        $action = $_POST['action'] ?? '';
        $userId = $_SESSION['user_id'];
        $model = new UnilateralDiscussionModel();
        
        switch ($action) {
            case 'send':
                // Envoi d'un message
                $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
                $message = $_POST['message'] ?? '';
                
                if (empty($message)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Message vide'
                    ]);
                    return;
                }
                
                if ($receiverId <= 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Destinataire invalide'
                    ]);
                    return;
                }
                
                $success = $model->sendMessage($userId, $receiverId, $message);
                
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Message envoyé' : 'Erreur lors de l\'envoi'
                ]);
                break;
                
            case 'receive':
                // Récupération du dernier message
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
                    echo json_encode([
                        'success' => true,
                        'has_message' => false,
                        'data' => null
                    ]);
                }
                break;
                
            case 'disconnect':
                // Déconnexion : suppression de la discussion
                $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
                
                if ($receiverId > 0) {
                    $model->disconnect($userId, $receiverId);
                } else {
                    $model->disconnectAll($userId);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Déconnecté'
                ]);
                break;
                
            case 'search_user':
                // Recherche d'un utilisateur par pseudo
                $pseudo = $_POST['pseudo'] ?? '';
                
                if (empty($pseudo)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Pseudo requis'
                    ]);
                    return;
                }
                
                $user = $model->findUserByPseudo($pseudo);
                
                if ($user) {
                    echo json_encode([
                        'success' => true,
                        'data' => $user
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Utilisateur non trouvé'
                    ]);
                }
                break;
                
            case 'search_users':
                // Recherche partielle d'utilisateurs par pseudo
                $search = $_POST['search'] ?? '';
                $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
                
                if (empty($search)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Terme de recherche requis'
                    ]);
                    return;
                }
                
                $users = $model->searchUsersByPseudo($search, $limit);
                
                echo json_encode([
                    'success' => true,
                    'data' => $users
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Action invalide'
                ]);
        }
    }
}

