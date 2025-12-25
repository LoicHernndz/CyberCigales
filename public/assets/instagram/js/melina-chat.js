/**
 * JavaScript pour le chat avec Melina
 * Gestion complète de l'ID de conversation côté client
 */

// Générer ou récupérer l'ID de conversation unique (32 caractères hexadécimaux)
// Cet ID est unique par navigateur et persiste dans localStorage
let conversationId = localStorage.getItem('instagram_conv_id');

if (!conversationId || conversationId.length !== 32) {
    conversationId = Array.from(crypto.getRandomValues(new Uint8Array(16)))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
    localStorage.setItem('instagram_conv_id', conversationId);
}

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du DOM
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.querySelector('.send-btn');
    const messagesContainer = document.querySelector('.messages');
    
    // Vérification que les éléments existent
    if (!messageInput || !sendButton || !messagesContainer) {
        console.error('Éléments du chat non trouvés');
        return;
    }
    
    // ========================================
    // GESTION DE L'ENVOI DE MESSAGES
    // ========================================
    
    // Événement sur le champ de saisie (touche Entrée)
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Événement sur le bouton d'envoi
    sendButton.addEventListener('click', sendMessage);
    
    /**
     * Fonction principale d'envoi de message
     */
    function sendMessage() {
        const message = messageInput.value.trim();
        
        // Vérification que le message n'est pas vide
        if (!message) {
            return;
        }
        
        // Vérifier que conversationId est valide
        if (!conversationId || conversationId.length !== 32) {
            console.error('ID de conversation invalide:', conversationId);
            return;
        }
        
        // Vider le champ de saisie immédiatement
        messageInput.value = '';
        
        // Ajouter le message de l'utilisateur à l'interface (optimiste)
        addUserMessage(message);
        scrollToBottom();
        
        // Envoyer le message au serveur via AJAX
        const formData = new FormData();
        formData.append('message', message);
        formData.append('conv_id', conversationId);
        
        fetch(window.location.pathname, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Le message utilisateur est déjà affiché, on ajoute juste la réponse de Melina
                if (data.melinaMessage) {
                    addMelinaMessage(data.melinaMessage.content);
                    scrollToBottom();
                }
                }
        })
        .catch(function(error) {
            // Erreur silencieuse
        });
    }
    
    /**
     * Ajoute un message de l'utilisateur à l'interface
     */
    function addUserMessage(message) {
        const messageHtml = createMessageHtml(message, 'sent');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }
    
    /**
     * Ajoute un message de Melina à l'interface
     */
    function addMelinaMessage(message) {
        const messageHtml = createMessageHtml(message, 'received');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }
    
    /**
     * Crée le HTML pour un message
     */
    function createMessageHtml(content, type) {
        const time = new Date().toLocaleTimeString('fr-FR', {
            hour: '2-digit', 
            minute: '2-digit'
        });
        
        return `
            <div class="message ${type}">
                <div class="message-content">
                    <p>${escapeHtml(content)}</p>
                    <span class="time">${time}</span>
                </div>
            </div>
        `;
    }
    
    /**
     * Échappe les caractères HTML pour éviter les injections XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Scroll automatique vers le bas de la conversation
     */
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Charger les messages existants au chargement de la page
    loadMessages();
    
    /**
     * Charge les messages existants depuis le serveur
     */
    function loadMessages() {
        const formData = new FormData();
        formData.append('conv_id', conversationId);
        formData.append('action', 'load');
        
        fetch(window.location.pathname + '?action=load', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success && data.messages) {
                // Vider le conteneur de messages
                messagesContainer.innerHTML = '';
                
                // Ajouter tous les messages
                data.messages.forEach(function(msg) {
                    if (msg.type === 'sent') {
                        addUserMessage(msg.content);
                    } else {
                        addMelinaMessage(msg.content);
                    }
                });
                
                scrollToBottom();
            }
        })
        .catch(function(error) {
            console.error('Erreur lors du chargement des messages:', error);
        });
    }
    
    scrollToBottom();
});
