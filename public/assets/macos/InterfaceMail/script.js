/**
 * Script de gestion de l'interface de messagerie (Client-side).
 *
 * Ce script s'exécute au chargement du DOM. Il gère :
 * 1. L'écoute des clics sur la liste des e-mails (générée par PHP).
 * 2. La mise à jour dynamique du panneau de lecture (Reading Pane) sans rechargement de page.
 * 3. La gestion visuelle des états "lu" et "sélectionné".
 *
 * @file      interface-mail.js
 * @author    [Ton Nom]
 */
document.addEventListener('DOMContentLoaded', () => {

    /**
     * @type {HTMLElement} Le conteneur du panneau de lecture où le contenu de l'e-mail s'affiche.
     */
    const readingPane = document.getElementById('reading-pane');
    
    // Charger les messages stockés depuis localStorage et les ajouter à la liste
    function loadStoredMessages() {
        if (typeof USER_ID === 'undefined' || !USER_ID) return;
        
        const storedMessages = getStoredMessages();
        const emailList = document.getElementById('email-list-container');
        
        if (!emailList) return;
        
        storedMessages.forEach(msg => {
            // Vérifier si le message n'est pas déjà dans la liste
            const existingMessage = emailList.querySelector(`[data-message-id="unilateral_${msg.sender_id}"]`);
            if (existingMessage) return; // Déjà affiché
            
            // Créer l'élément email pour le message stocké
            const messageEmail = {
                sender: msg.sender_username || `Utilisateur #${msg.sender_id}`,
                subject: "Message unilatéral",
                time: new Date(msg.updated_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                snippet: msg.message.substring(0, 50) + '...',
                content: `<p>${msg.message.replace(/\n/g, '<br>')}</p>`,
                is_unilateral: true,
                sender_id: msg.sender_id,
                message_id: `unilateral_${msg.sender_id}`
            };
            
            const emailDataJson = htmlspecialchars(JSON.stringify(messageEmail));
            const emailItem = document.createElement('li');
            emailItem.className = 'email-item read';
            emailItem.setAttribute('data-email', emailDataJson);
            emailItem.setAttribute('data-message-id', `unilateral_${msg.sender_id}`);
            emailItem.innerHTML = `
                <div class='email-header'>
                    <span class='sender'>${htmlspecialchars(messageEmail.sender)}</span>
                    <span class='time'>${messageEmail.time}</span>
                </div>
                <div class='subject'>${htmlspecialchars(messageEmail.subject)}</div>
                <div class='snippet'>${htmlspecialchars(messageEmail.snippet)}</div>
            `;
            
            // Ajouter en haut de la liste (après le premier élément s'il existe)
            if (emailList.firstChild) {
                emailList.insertBefore(emailItem, emailList.firstChild);
            } else {
                emailList.appendChild(emailItem);
            }
            
            // Ajouter l'event listener pour le clic
            emailItem.addEventListener('click', function() {
                updateReadingPane(messageEmail);
                document.querySelectorAll('.email-item').forEach(item => item.classList.remove('selected'));
                emailItem.classList.add('selected');
            });
        });
    }
    
    // Fonction helper pour échapper HTML
    function htmlspecialchars(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Fonction pour récupérer les messages stockés depuis localStorage
    function getStoredMessages() {
        if (typeof USER_ID === 'undefined' || !USER_ID) return [];
        
        const messages = [];
        const keys = Object.keys(localStorage);
        
        keys.forEach(key => {
            if (key.startsWith(`unilateral_message_${USER_ID}_`)) {
                try {
                    const messageData = JSON.parse(localStorage.getItem(key));
                    messages.push(messageData);
                } catch (e) {
                    console.error('Erreur parsing message stocké:', e);
                }
            }
        });
        
        return messages;
    }
    
    // Charger les messages stockés au chargement de la page
    loadStoredMessages();

    /**
     * Met à jour le HTML du panneau de lecture avec les données de l'e-mail fourni.
     *
     * @param {Object} emailData - L'objet e-mail désérialisé depuis le JSON.
     * @param {string} emailData.subject - Le sujet de l'e-mail.
     * @param {string} emailData.sender - Le nom ou l'adresse de l'expéditeur.
     * @param {string} emailData.content - Le corps du message (peut contenir du HTML).
     * @returns {void}
     */
    const updateReadingPane = (emailData) => {
        // Le contenu HTML est mis à jour avec les données de l'e-mail cliqué
        readingPane.innerHTML = `
            <h2>${emailData.subject}</h2>
            <div class="recipient-info">
                <strong>De :</strong> ${emailData.sender} <br>
                <strong>À :</strong> noname@noname.com
            </div>
            <div class="email-content">
                ${emailData.content}
            </div>
        `;
    };

    // --- Gère le clic sur un e-mail qui est DÉJÀ dans le DOM (rempli par PHP) ---

    /**
     * @type {NodeListOf<Element>} Liste de tous les éléments <li> représentant les e-mails.
     */
    const emailItems = document.querySelectorAll('.email-item');

    emailItems.forEach(item => {
        /**
         * Écouteur d'événement pour le clic sur un e-mail.
         * Gère le changement de classe CSS et le parsing des données JSON.
         */
        item.addEventListener('click', () => {
            // Désélectionner tous les autres e-mails
            emailItems.forEach(i => i.classList.remove('selected'));

            // Sélectionner l'e-mail cliqué et le marquer comme lu
            item.classList.add('selected');
            item.classList.add('read');

            // Récupérer les données JSON sérialisées par PHP dans l'attribut data-email
            const emailDataJson = item.getAttribute('data-email');

            try {
                // Décodage des données
                const emailData = JSON.parse(emailDataJson);

                // Mettre à jour le panneau de lecture
                updateReadingPane(emailData);
            } catch (e) {
                console.error("Erreur de parsing JSON de l'attribut data-email:", e);
                // Si l'e-mail n'a pas pu être parsé, on affiche le placeholder avec un message d'erreur
                readingPane.innerHTML = '<div class="placeholder">Erreur de chargement des données.</div>';
            }
        });
    });
});