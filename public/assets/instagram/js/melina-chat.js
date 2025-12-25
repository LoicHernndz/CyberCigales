/**
 * JavaScript pour le chat avec Melina
 * 
 * Basé sur le projet IG Grid Profile de Angela Holden
 * Repository: https://github.com/angelajholden/ig-grid-profile
 * 
 * Fonctionnalités :
 * - Envoi de messages en temps réel
 * - Réponses automatiques de Melina
 * - Gestion des événements clavier (Enter)
 * - Scroll automatique vers les nouveaux messages
 */

// Générer un ID unique pour cette conversation (unique par navigateur, persistant)
// Utiliser localStorage pour que l'ID persiste après fermeture
// Tous les onglets du même navigateur partageront le même ID (et donc les mêmes messages)
// Chaque navigateur différent (Chrome, Firefox, etc.) aura son propre ID
let conversationId = localStorage.getItem('instagram_conv_id');

if (!conversationId) {
    // Générer un ID unique (32 caractères hexadécimaux)
    conversationId = Array.from(crypto.getRandomValues(new Uint8Array(16)))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
    // Stocker dans localStorage (persiste après fermeture)
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
        if (message) {
            // Vider le champ de saisie immédiatement pour une meilleure UX
            messageInput.value = '';
            
            // Ajouter le message de l'utilisateur à l'interface (optimiste)
            addUserMessage(message);
            scrollToBottom();
            
            // Envoyer le message au serveur via AJAX
            const formData = new FormData();
            formData.append('message', message);
            formData.append('conv_id', conversationId); // Envoyer l'ID de conversation unique
            
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
                } else {
                    console.error('Erreur:', data.message || 'Erreur inconnue');
                }
            })
            .catch(function(error) {
                console.error('Erreur lors de l\'envoi du message:', error);
            });
        }
    }
    
    /**
     * Ajoute un message de l'utilisateur à l'interface
     * @param {string} message - Le message à afficher
     */
    function addUserMessage(message) {
        const messageHtml = createMessageHtml(message, 'sent');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }
    
    /**
     * Ajoute un message de Melina à l'interface
     * @param {string} message - Le message à afficher
     */
    function addMelinaMessage(message) {
        const messageHtml = createMessageHtml(message, 'received');
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }
    
    /**
     * Crée le HTML pour un message
     * @param {string} content - Le contenu du message
     * @param {string} type - Le type de message ('sent' ou 'received')
     * @returns {string} Le HTML généré
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
     * @param {string} text - Le texte à échapper
     * @returns {string} Le texte échappé
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
    
    
    // ========================================
    // INITIALISATION
    // ========================================
    
    // Scroll initial vers le bas pour voir les derniers messages
    scrollToBottom();
    
    console.log('Chat avec Melina initialisé avec succès');
});
