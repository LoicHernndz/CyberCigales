/**
 * Système d'interface pour les notifications dans Instagram (icône cloche)
 */

document.addEventListener('DOMContentLoaded', () => {
    const bellButtons = document.querySelectorAll('button[aria-label="Notifications"]');
    if (bellButtons.length === 0) return;

    const bellButton = bellButtons[0];
    bellButton.style.position = 'relative';

    // Créer le panel de notifications
    const panel = document.createElement('div');
    panel.className = 'instagram-notif-panel hidden';
    panel.innerHTML = `
        <div class="panel-header">
            <h3>Notifications</h3>
        </div>
        <div class="panel-content" id="instagram-notif-list">
            <div class="empty-state">Aucune notification</div>
        </div>
    `;

    // Insérer le panel près du bouton cloche pour éviter de casser des layouts grid
    const targetContainer = document.querySelector('.header_right') || document.querySelector('.header-actions') || document.body;
    targetContainer.appendChild(panel);

    // Fonction pour formater le temps relatif
    function getRelativeTime(timestamp) {
        const diff = Date.now() - timestamp;
        const minutes = Math.floor(diff / 60000);
        if (minutes < 1) return "À l'instant";
        if (minutes < 60) return `${minutes} min`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} h`;
        return `${Math.floor(hours / 24)} j`;
    }

    // Mettre à jour la liste des notifications
    function renderNotifications() {
        const listDiv = document.getElementById('instagram-notif-list');
        try {
            const history = JSON.parse(localStorage.getItem('instagram_notifications') || '[]');

            if (history.length === 0) {
                listDiv.innerHTML = '<div class="empty-state">Aucune notification</div>';
                return;
            }

            listDiv.innerHTML = history.map(data => `
                <a href="/instagram/user/${data.username}" class="notif-item">
                    <img class="notif-avatar" src="${data.avatar}" alt="${data.username}">
                    <div class="notif-text">
                        <strong>${data.username}</strong> a publié : ${data.caption}
                    </div>
                    <span class="notif-time">${getRelativeTime(data.timestamp || Date.now())}</span>
                </a>
            `).join('');
        } catch (e) {
            listDiv.innerHTML = '<div class="empty-state">Erreur de chargement</div>';
        }
    }

    // Mettre à jour le badge sur la cloche
    function updateBellBadge() {
        const unreadCount = parseInt(localStorage.getItem('instagram_unread_count') || '0');
        let badge = bellButton.querySelector('.bell-badge');

        if (unreadCount > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'bell-badge';
                bellButton.appendChild(badge);
            }
            badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
        } else {
            if (badge) badge.remove();
        }
    }

    // Réinitialiser le compteur de cloche
    function resetBellBadge() {
        localStorage.setItem('instagram_unread_count', '0');
        updateBellBadge();
        // Permet au fichier macos.js parent de retirer le badge aussi 
        window.dispatchEvent(new Event('storage'));
    }

    // Toggle dropdown
    bellButton.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        const isHidden = panel.classList.contains('hidden');

        if (isHidden) {
            renderNotifications();
            resetBellBadge();
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    });

    // Clic extérieur pour fermer
    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && !bellButton.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });

    // Écouter les mises à jour depuis les autres iframes / depuis l'app parent (iOS)
    window.addEventListener('storage', (e) => {
        if (e.key === 'instagram_unread_count') {
            updateBellBadge();
        }
        if (e.key === 'instagram_notifications') {
            renderNotifications();
        }
    });

    // Initialisation
    updateBellBadge();
});
