/**
 * JavaScript pour le chat avec Melina
 * Les messages sont sauvegardés dans localStorage pour persister après fermeture
 */

// Clé pour localStorage
const STORAGE_KEY = 'melina_chat_messages';

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
        
        // Vider le champ de saisie immédiatement
        messageInput.value = '';
        
        // Ajouter le message de l'utilisateur à l'interface (optimiste)
        addUserMessage(message);
        scrollToBottom();
        
        // Envoyer le message au serveur via AJAX
        const formData = new FormData();
        formData.append('message', message);
        
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
                    // Utiliser l'heure du serveur si disponible
                    const melinaTime = data.melinaMessage.time || undefined;
                    const messageHtml = createMessageHtml(data.melinaMessage.content, 'received', melinaTime);
                    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                    saveMessagesToStorage();
                    scrollToBottom();
                    
                    // Si une clé a été trouvée, afficher un message spécial
                    if (data.keyFound && data.foundMessage) {
                        // Notification visuelle que la clé a été trouvée
                        console.log('🎉 Clé trouvée:', data.foundMessage);
                    }
                }
            }
        })
        .catch(function(error) {
            // Erreur silencieuse
        });
    }
    
    /**
     * Sauvegarde les messages dans localStorage
     */
    function saveMessagesToStorage() {
        const messages = [];
        const messageElements = messagesContainer.querySelectorAll('.message');
        
        messageElements.forEach(function(msgEl) {
            const contentEl = msgEl.querySelector('p');
            const timeEl = msgEl.querySelector('.time');
            const type = msgEl.classList.contains('sent') ? 'sent' : 'received';
            
            if (contentEl) {
                messages.push({
                    type: type,
                    content: contentEl.textContent.trim(),
                    time: timeEl ? timeEl.textContent.trim() : new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
                });
            }
        });
        
        localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
    }
    
    /**
     * Charge les messages depuis localStorage
     */
    function loadMessagesFromStorage() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const messages = JSON.parse(stored);
                return messages;
            }
        } catch (e) {
            console.error('Erreur lors du chargement depuis localStorage:', e);
        }
        return null;
    }
    
    /**
     * Ajoute un message de l'utilisateur à l'interface
     */
    function addUserMessage(message) {
        const messageHtml = createMessageHtml(message, 'sent');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        saveMessagesToStorage();
    }
    
    /**
     * Ajoute un message de Melina à l'interface
     */
    function addMelinaMessage(message) {
        const messageHtml = createMessageHtml(message, 'received');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        saveMessagesToStorage();
    }
    
    /**
     * Crée le HTML pour un message
     * @param {string} content - Contenu du message
     * @param {string} type - Type de message ('sent' ou 'received')
     * @param {string} [time] - Heure du message (optionnel, utilise l'heure actuelle si non fournie)
     */
    function createMessageHtml(content, type, time) {
        if (!time) {
            time = new Date().toLocaleTimeString('fr-FR', {
                hour: '2-digit', 
                minute: '2-digit'
            });
        }
        
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
    
    /**
     * Charge les messages existants (priorité à localStorage, puis synchronisation avec le serveur)
     */
    function loadMessages() {
        // D'abord, charger depuis localStorage pour affichage immédiat
        const storedMessages = loadMessagesFromStorage();
        
        if (storedMessages && storedMessages.length > 0) {
            // Vider le conteneur de messages
            messagesContainer.innerHTML = '';
            
            // Ajouter tous les messages depuis localStorage
            storedMessages.forEach(function(msg) {
                const messageHtml = createMessageHtml(msg.content, msg.type, msg.time);
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            });
            
            // Sauvegarder après chargement (pour s'assurer que c'est à jour)
            saveMessagesToStorage();
            scrollToBottom();
        }
        
        // Ensuite, synchroniser avec le serveur (session PHP)
        const formData = new FormData();
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
            if (data.success && data.messages && data.messages.length > 0) {
                // Si le serveur a plus de messages que localStorage, mettre à jour
                const storedMessages = loadMessagesFromStorage() || [];
                if (data.messages.length > storedMessages.length) {
                    // Vider le conteneur
                    messagesContainer.innerHTML = '';
                    
                    // Ajouter tous les messages du serveur
                    data.messages.forEach(function(msg) {
                        const messageHtml = createMessageHtml(msg.content, msg.type, msg.time);
                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                    });
                    
                    // Sauvegarder dans localStorage
                    saveMessagesToStorage();
                    scrollToBottom();
                } else {
                    // Si le serveur a les mêmes messages, s'assurer que localStorage est à jour
                    saveMessagesToStorage();
                }
            } else if (data.success && data.messages && data.messages.length === 0 && storedMessages && storedMessages.length > 0) {
                // Si le serveur n'a pas de messages mais localStorage oui, garder localStorage
                // (cas où la session PHP a expiré mais localStorage a encore les messages)
                // Ne rien faire, on garde les messages de localStorage
            }
        })
        .catch(function(error) {
            console.error('Erreur lors de la synchronisation avec le serveur:', error);
            // En cas d'erreur, on garde les messages de localStorage
        });
    }
    
    // Charger les messages au chargement de la page
    loadMessages();
    scrollToBottom();
});
