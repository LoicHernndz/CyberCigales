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
    
    // Fermer le modal (sans supprimer de la BDD - le message reste visible)
    window.closeSendModal = function() {
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
    
    // Stocker les messages reçus dans localStorage pour les conserver même après suppression BDD
    function saveReceivedMessage(messageData) {
        if (!messageData || !messageData.sender_id) return;
        
        const storageKey = `unilateral_message_${USER_ID}_${messageData.sender_id}`;
        const messageToStore = {
            sender_id: messageData.sender_id,
            sender_username: messageData.sender_username,
            message: messageData.message,
            updated_at: messageData.updated_at,
            saved_at: new Date().toISOString()
        };
        
        localStorage.setItem(storageKey, JSON.stringify(messageToStore));
    }
    
    // Récupérer les messages stockés depuis localStorage
    function getStoredMessages() {
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
                // Sauvegarder le message dans localStorage
                saveReceivedMessage(data.data);
                // Recharger la page pour afficher le nouveau message
                location.reload();
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

