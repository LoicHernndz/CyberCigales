/**
 * Script pour la discussion unilatérale dans l'interface Mail
 */

document.addEventListener('DOMContentLoaded', function() {
    if (typeof USER_ID === 'undefined' || USER_ID === null) {
        console.error('USER_ID non défini');
        return;
    }
    
    const newMessageBtn = document.getElementById('new-message-btn');
    const sendModal = document.getElementById('send-message-modal');
    const receiverPseudoInput = document.getElementById('receiver-pseudo-input');
    const receiverIdInput = document.getElementById('receiver-id-input');
    const searchUserBtn = document.getElementById('search-user-btn');
    const searchResults = document.getElementById('user-search-results');
    const selectedUserInfo = document.getElementById('selected-user-info');
    const selectedPseudoSpan = document.getElementById('selected-pseudo');
    const messageTextarea = document.getElementById('message-textarea');
    const sendMessageBtn = document.getElementById('send-message-btn');
    const sendStatus = document.getElementById('send-status');
    
    let receiverId = null;
    let searchTimeout = null;
    let pollingInterval = null;
    
    // Ouvrir le modal d'envoi
    if (newMessageBtn) {
        newMessageBtn.addEventListener('click', function() {
            sendModal.style.display = 'flex';
            receiverPseudoInput.focus();
        });
    }
    
    // Fermer le modal et déconnecter (supprimer la ligne de la BDD)
    window.closeSendModal = function() {
        // Si un destinataire était sélectionné, supprimer la discussion de la BDD
        if (receiverId && receiverId > 0) {
            disconnectDiscussion(receiverId);
        }
        
        sendModal.style.display = 'none';
        receiverPseudoInput.value = '';
        receiverIdInput.value = '';
        messageTextarea.value = '';
        selectedUserInfo.style.display = 'none';
        searchResults.style.display = 'none';
        sendStatus.style.display = 'none';
        receiverId = null;
    };
    
    // Fonction pour déconnecter (supprimer la ligne de la BDD)
    async function disconnectDiscussion(targetReceiverId) {
        try {
            const formData = new FormData();
            formData.append('action', 'disconnect');
            formData.append('receiver_id', targetReceiverId);
            
            await fetch('/email', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Erreur déconnexion:', error);
        }
    }
    
    // Fonction pour déconnecter (supprimer la ligne de la BDD)
    async function disconnectDiscussion(targetReceiverId) {
        try {
            const formData = new FormData();
            formData.append('action', 'disconnect');
            formData.append('receiver_id', targetReceiverId);
            
            await fetch('/email', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Erreur déconnexion:', error);
        }
    }
    
    // Recherche en temps réel (debounce)
    if (receiverPseudoInput) {
        receiverPseudoInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
                receiverIdInput.value = '';
                selectedUserInfo.style.display = 'none';
                receiverId = null;
                return;
            }
            
            searchTimeout = setTimeout(async function() {
                try {
                    const formData = new FormData();
                    formData.append('action', 'search_users');
                    formData.append('search', searchTerm);
                    formData.append('limit', '5');
                    
                    const response = await fetch('/email', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.data.length > 0) {
                        displaySearchResults(data.data);
                    } else {
                        searchResults.innerHTML = '<p class="no-results">Aucun utilisateur trouvé</p>';
                        searchResults.style.display = 'block';
                    }
                } catch (error) {
                    console.error('Erreur recherche:', error);
                }
            }, 300);
        });
    }
    
    // Recherche manuelle
    if (searchUserBtn) {
        searchUserBtn.addEventListener('click', async function() {
            const pseudo = receiverPseudoInput.value.trim();
            
            if (!pseudo) {
                showStatus('Veuillez entrer un pseudo', 'error');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'search_user');
                formData.append('pseudo', pseudo);
                
                const response = await fetch('/email', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success && data.data) {
                    selectUser(data.data);
                } else {
                    showStatus('Utilisateur non trouvé', 'error');
                    searchResults.style.display = 'none';
                }
            } catch (error) {
                console.error('Erreur:', error);
                showStatus('Erreur de connexion', 'error');
            }
        });
    }
    
    // Fonction pour afficher les résultats de recherche
    function displaySearchResults(users) {
        let html = '<div class="results-list">';
        users.forEach(user => {
            html += `
                <div class="result-item" data-user-id="${user.id}" data-user-pseudo="${escapeHtml(user.pseudo)}">
                    <strong>${escapeHtml(user.pseudo)}</strong>
                    ${user.prenom || user.nom ? `<span class="user-name">${escapeHtml((user.prenom || '') + ' ' + (user.nom || ''))}</span>` : ''}
                    <span class="user-id">ID: ${user.id}</span>
                </div>
            `;
        });
        html += '</div>';
        searchResults.innerHTML = html;
        searchResults.style.display = 'block';
        
        // Ajouter les event listeners sur les résultats
        searchResults.querySelectorAll('.result-item').forEach(item => {
            item.addEventListener('click', function() {
                const userId = parseInt(this.dataset.userId);
                const userPseudo = this.dataset.userPseudo;
                selectUser({ id: userId, pseudo: userPseudo });
            });
        });
    }
    
    // Fonction pour sélectionner un utilisateur
    function selectUser(user) {
        receiverId = user.id;
        receiverIdInput.value = user.id;
        receiverPseudoInput.value = user.pseudo;
        selectedPseudoSpan.textContent = user.pseudo;
        selectedUserInfo.style.display = 'block';
        searchResults.style.display = 'none';
        sendStatus.style.display = 'none';
    }
    
    // Envoi de message
    if (sendMessageBtn) {
        sendMessageBtn.addEventListener('click', async function() {
            const message = messageTextarea.value.trim();
            
            if (!message) {
                showStatus('Veuillez entrer un message', 'error');
                return;
            }
            
            if (!receiverId || receiverId <= 0) {
                showStatus('Veuillez sélectionner un destinataire', 'error');
                return;
            }
            
            sendMessageBtn.disabled = true;
            showStatus('Envoi en cours...', 'info');
            
            try {
                const formData = new FormData();
                formData.append('action', 'send');
                formData.append('receiver_id', receiverId);
                formData.append('message', message);
                
                const response = await fetch('/email', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showStatus('Message envoyé avec succès !', 'success');
                    messageTextarea.value = '';
                    setTimeout(() => {
                        closeSendModal();
                    }, 1500);
                } else {
                    showStatus('Erreur : ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showStatus('Erreur de connexion', 'error');
            } finally {
                sendMessageBtn.disabled = false;
            }
        });
    }
    
    // Fonction pour afficher les messages de statut
    function showStatus(message, type) {
        sendStatus.textContent = message;
        sendStatus.className = 'status-message ' + type;
        sendStatus.style.display = 'block';
    }
    
    // Stocker les messages reçus dans localStorage (historique par sender_id)
    function saveReceivedMessage(messageData) {
        if (!messageData || !messageData.sender_id) return false;
        
        const storageKey = `unilateral_messages_${USER_ID}`;
        let allMessages = [];
        
        // Récupérer les messages existants
        try {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                allMessages = JSON.parse(stored);
            }
        } catch (e) {
            console.error('Erreur lecture localStorage:', e);
        }
        
        // Créer un ID unique pour ce message (sender_id + timestamp)
        const messageId = `${messageData.sender_id}_${messageData.updated_at}`;
        
        // Vérifier si ce message n'existe pas déjà
        const exists = allMessages.some(msg => 
            msg.sender_id === messageData.sender_id && 
            msg.updated_at === messageData.updated_at
        );
        
        if (exists) {
            return false; // Message déjà stocké
        }
        
        // Ajouter le nouveau message
        const messageToStore = {
            id: messageId,
            sender_id: messageData.sender_id,
            sender_username: messageData.sender_username,
            message: messageData.message,
            updated_at: messageData.updated_at,
            saved_at: new Date().toISOString()
        };
        
        allMessages.push(messageToStore);
        
        // Garder seulement les 50 derniers messages pour éviter de surcharger
        if (allMessages.length > 50) {
            allMessages = allMessages.slice(-50);
        }
        
        // Sauvegarder
        localStorage.setItem(storageKey, JSON.stringify(allMessages));
        return true;
    }
    
    // Récupérer les messages stockés depuis localStorage
    function getStoredMessages() {
        if (!USER_ID) return [];
        
        const storageKey = `unilateral_messages_${USER_ID}`;
        try {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                return JSON.parse(stored);
            }
        } catch (e) {
            console.error('Erreur parsing messages stockés:', e);
        }
        return [];
    }
    
    // Ajouter un message à la liste d'emails dynamiquement
    function addMessageToEmailList(messageData) {
        const emailList = document.getElementById('email-list-container');
        if (!emailList) return;
        
        // Vérifier si le message n'est pas déjà affiché
        const messageId = `unilateral_${messageData.sender_id}_${messageData.updated_at}`;
        const existingMessage = emailList.querySelector(`[data-message-id="${messageId}"]`);
        if (existingMessage) return; // Déjà affiché
        
        const messageEmail = {
            sender: messageData.sender_username || `Utilisateur #${messageData.sender_id}`,
            subject: "Message unilatéral",
            time: new Date(messageData.updated_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
            snippet: messageData.message.substring(0, 50) + (messageData.message.length > 50 ? '...' : ''),
            content: `<p>${messageData.message.replace(/\n/g, '<br>')}</p>`,
            is_unilateral: true,
            sender_id: messageData.sender_id,
            message_id: messageId
        };
        
        const emailDataJson = escapeHtml(JSON.stringify(messageEmail));
        const emailItem = document.createElement('li');
        emailItem.className = 'email-item';
        emailItem.setAttribute('data-email', emailDataJson);
        emailItem.setAttribute('data-message-id', messageId);
        emailItem.innerHTML = `
            <div class='email-header'>
                <span class='sender'>${escapeHtml(messageEmail.sender)}</span>
                <span class='time'>${messageEmail.time}</span>
            </div>
            <div class='subject'>${escapeHtml(messageEmail.subject)}</div>
            <div class='snippet'>${escapeHtml(messageEmail.snippet)}</div>
        `;
        
        // Ajouter l'event listener pour le clic
        emailItem.addEventListener('click', function() {
            // Utiliser la fonction updateReadingPane du script.js si elle existe
            if (typeof updateReadingPane === 'function') {
                updateReadingPane(messageEmail);
            } else {
                // Fallback si la fonction n'existe pas
                const readingPane = document.getElementById('reading-pane');
                if (readingPane) {
                    readingPane.innerHTML = `
                        <h2>${escapeHtml(messageEmail.subject)}</h2>
                        <div class="recipient-info">
                            <strong>De :</strong> ${escapeHtml(messageEmail.sender)} <br>
                            <strong>À :</strong> noname@noname.com
                        </div>
                        <div class="email-content">
                            ${messageEmail.content}
                        </div>
                    `;
                }
            }
            
            // Sélectionner visuellement
            document.querySelectorAll('.email-item').forEach(item => item.classList.remove('selected'));
            emailItem.classList.add('selected');
            emailItem.classList.add('read');
        });
        
        // Ajouter en haut de la liste
        if (emailList.firstChild) {
            emailList.insertBefore(emailItem, emailList.firstChild);
        } else {
            emailList.appendChild(emailItem);
        }
    }
    
    // Polling pour récupérer les nouveaux messages (toutes les 3 secondes)
    function fetchNewMessages() {
        if (!USER_ID) return;
        
        fetch('/email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=receive'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_message) {
                // Sauvegarder le message dans localStorage (vérifie les doublons)
                const wasNew = saveReceivedMessage(data.data);
                
                // Si c'est un nouveau message, l'ajouter à la liste
                if (wasNew) {
                    addMessageToEmailList(data.data);
                }
            }
        })
        .catch(error => {
            console.error('Erreur polling:', error);
        });
    }
    
    // Démarrer le polling
    pollingInterval = setInterval(fetchNewMessages, 3000);
    
    // Nettoyage lors de la fermeture
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

