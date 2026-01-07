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
 * - Progression dans le chat (rechargement après bonne réponse)
 */

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
            // Ajouter le message de l'utilisateur à l'interface
            addUserMessage(message);
            
            // Vider le champ de saisie
            messageInput.value = '';
            
            // Scroll vers le bas
            scrollToBottom();

            // Envoyer au serveur et attendre la réponse
            setTimeout(() => {
                generateResponse(message);
            }, 1000);
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
        scrollToBottom();
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
    
    /**
     * Envoie le message au serveur et traite la réponse
     * @param {string} message - Le message envoyé par l'utilisateur
     */
    async function generateResponse(message) {
        try {
            let url = "/instagram/chat/response?name=melina&message=" + encodeURIComponent(message);
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            const data = await response.json();
            
            // Afficher le message de réponse
            addMelinaMessage(data.message);
            
            // Si la progression a changé, recharger la page après un délai
            if (data.progress === true) {
                setTimeout(() => {
                    // Afficher un message avant le rechargement
                    addMelinaMessage("La conversation va se mettre à jour...");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }, 2000);
            }

        } catch (error) {
            console.error('Erreur:', error.message);
            addMelinaMessage("Oops, une erreur s'est produite... 😅");
        }
    }
    
    // ========================================
    // INITIALISATION
    // ========================================
    
    // Scroll initial vers le bas pour voir les derniers messages
    scrollToBottom();
    
    console.log('Chat avec Melina initialisé avec succès');
});
