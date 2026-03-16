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
    private const IMAGE_TOKEN_REGEX = '/\{\{img:([^}]+)\}\}/';

    /**
     * Reçoit les messages bruts depuis le controller
     */
    public function setChatMessages(array $messages): void
    {
        $this->chatMessages = $messages;
    }

    /**
     * @return string Chemin vers le template user-chat.html
     */
    public function templatePath(): string
    {
        return self::TEMPLATE_PATH;
    }

    /**
     * Fusionne les clés parentes avec le HTML des messages du chat
     *
     * @return array Clés de template incluant 'MESSAGES'
     */
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
                . $this->renderMessageContent($message['content'])
                . '<span class="time">' . $message['time'] . '</span>'
                . '</div></div>';
        }
        return $html;
    }

    /**
     * Rend le contenu d'un message en HTML sûr.
     *
     * Supporte un token d'image: {{img:/images/...}}.
     */
    private function renderMessageContent(string $content): string
    {
        if (!preg_match(self::IMAGE_TOKEN_REGEX, $content)) {
            return '<p>' . htmlspecialchars($content) . '</p>';
        }

        $parts = preg_split(self::IMAGE_TOKEN_REGEX, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $html = '';

        for ($i = 0; $i < count($parts); $i++) {
            if ($i % 2 === 0) {
                $text = trim($parts[$i] ?? '');
                if ($text !== '') {
                    $html .= '<p>' . htmlspecialchars($text) . '</p>';
                }
                continue;
            }

            $src = trim($parts[$i] ?? '');
            if ($this->isAllowedImageSrc($src)) {
                $safeSrc = htmlspecialchars($src);
                $html .= '<img class="message-image" src="' . $safeSrc . '" alt="image" style="max-width: 100%; border-radius: 12px;" />';
            } else {
                $html .= '<p>' . htmlspecialchars('{{img:' . $src . '}}') . '</p>';
            }
        }

        return $html === '' ? '<p></p>' : $html;
    }

    private function isAllowedImageSrc(string $src): bool
    {
        if ($src === '') {
            return false;
        }
        if (str_contains($src, '..')) {
            return false;
        }
        return str_starts_with($src, '/images/');
    }
}

