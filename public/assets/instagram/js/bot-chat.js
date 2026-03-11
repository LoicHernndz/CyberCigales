/**
 * JavaScript pour le chat avec un bot
 *
 * Fonctionnalités :
 * - Envoi de messages en temps réel
 * - Réponses automatiques du bot
 * - Persistance des messages via localStorage
 * - Gestion des événements clavier (Enter)
 * - Scroll automatique vers les nouveaux messages
 */

let username = window.location.href.toString().split("/")[5]
var STORAGE_KEY = 'chat_messages_' + username;

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.querySelector('.send-btn');
    const messagesContainer = document.querySelector('.messages');

    if (!messageInput || !sendButton || !messagesContainer) {
        console.error('Éléments du chat non trouvés');
        return;
    }

    // ========================================
    // PERSISTANCE DES MESSAGES
    // ========================================

    function getSavedMessages() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveMessage(content, type, time) {
        var messages = getSavedMessages();
        messages.push({ content: content, type: type, time: time });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
    }

    function restoreSavedMessages() {
        var messages = getSavedMessages();
        messages.forEach(function(msg) {
            var html = createMessageHtmlWithTime(msg.content, msg.type, msg.time);
            messagesContainer.insertAdjacentHTML('beforeend', html);
        });
        return messages.length > 0;
    }

    // ========================================
    // GESTION DE L'ENVOI DE MESSAGES
    // ========================================

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    sendButton.addEventListener('click', sendMessage);

    function sendMessage() {
        var message = messageInput.value.trim();

        if (message) {
            addUserMessage(message);
            messageInput.value = '';
            scrollToBottom();

            setTimeout(function() {
                generateResponse(message);
            }, 2000);
        }
    }

    function getCurrentTime() {
        return new Date().toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function addUserMessage(message) {
        var time = getCurrentTime();
        var html = createMessageHtmlWithTime(message, 'sent', time);
        messagesContainer.insertAdjacentHTML('beforeend', html);
        saveMessage(message, 'sent', time);
    }

    function addBotMessage(message) {
        var time = getCurrentTime();
        var html = createMessageHtmlWithTime(message, 'received', time);
        messagesContainer.insertAdjacentHTML('beforeend', html);
        saveMessage(message, 'received', time);
    }

    function createMessageHtmlWithTime(content, type, time) {
        return '<div class="message ' + type + '">'
            + '<div class="message-content">'
            + '<p>' + escapeHtml(content) + '</p>'
            + '<span class="time">' + escapeHtml(time) + '</span>'
            + '</div></div>';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function scrollToBottom() {
        var container = document.querySelector('.messages-container');
        if (container) container.scrollTop = container.scrollHeight;
    }

    async function generateResponse(message) {
        try {
            var url = "/instagram/chat/response?name=" + encodeURIComponent(username) + "&message=" + encodeURIComponent(message);
            var response = await fetch(url);
            if (!response.ok) {
                throw new Error('Response status: ' + response.status);
            }

            var text = await response.text();

            if (text.length > 0) {
                addBotMessage(text);
                scrollToBottom();
            }

            return text;

        } catch (error) {
            console.error(error.message);
            return "?!";
        }
    }

    // ========================================
    // INITIALISATION
    // ========================================

    var hadSavedMessages = restoreSavedMessages();

    if (!hadSavedMessages) {
        generateResponse("");
    }

    scrollToBottom();
});
