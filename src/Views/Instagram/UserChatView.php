<?php
namespace Views\Instagram;

/**
 * Vue pour le chat Instagram avec un utilisateur générique
 * 
 * Gère le rendu du template HTML du chat.
 * La génération HTML des messages se fait ici (pas dans le controller).
 */
class UserChatView extends BaseInstagramView
{
    private const TEMPLATE_PATH = __DIR__ . '/user-chat.html';

    private array $chatMessages = [];

    /**
     * Reçoit les messages bruts depuis le controller
     */
    public function setChatMessages(array $messages): void
    {
        $this->chatMessages = $messages;
    }

    public function templatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    public function templateKeys(): array
    {
        $keys = parent::templateKeys();
        $keys['MESSAGES'] = $this->renderMessages();
        return $keys;
    }

    /**
     * Génère le HTML pour les messages du chat
     */
    private function renderMessages(): string
    {
        $html = '';
        foreach ($this->chatMessages as $message) {
            $senderClass = ($message['type'] === 'sent') ? 'sent' : 'received';
            $html .= '<div class="message ' . $senderClass . '">'
                . '<div class="message-content">'
                . '<p>' . htmlspecialchars($message['content']) . '</p>'
                . '<span class="time">' . $message['time'] . '</span>'
                . '</div></div>';
        }
        return $html;
    }
}

