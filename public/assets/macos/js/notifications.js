/**
 * Système de notifications Instagram pour l'interface macOS avec LocalStorage
 */

const NOTIF_CONFIG = {
    maxVisible: 3,
    autoDismissMs: 5000,
    minIntervalMs: 20000,
    maxIntervalMs: 50000,
};

let notifContainer = null;

function initNotificationContainer() {
    if (notifContainer) return;
    notifContainer = document.createElement('div');
    notifContainer.className = 'notification-container';
    notifContainer.id = 'notification-container';
    document.body.appendChild(notifContainer);
}

function getStoredNotifications() {
    try {
        return JSON.parse(localStorage.getItem('instagram_notifications') || '[]');
    } catch (e) {
        return [];
    }
}

function saveNotificationToStorage(data) {
    const history = getStoredNotifications();
    data.timestamp = Date.now();
    data.id = 'notif_' + Math.random().toString(36).substr(2, 9);
    history.unshift(data); // Ajouter au début
    if (history.length > 50) history.pop(); // Garder max 50
    localStorage.setItem('instagram_notifications', JSON.stringify(history));

    let unread = parseInt(localStorage.getItem('instagram_unread_count') || '0');
    unread++;
    localStorage.setItem('instagram_unread_count', unread);

    // Déclencher un événement storage artificiel pour la même fenêtre
    window.dispatchEvent(new Event('storage'));

    return unread;
}

function showNotification(data) {
    initNotificationContainer();

    const existing = notifContainer.querySelectorAll('.notification-toast:not(.removing)');
    if (existing.length >= NOTIF_CONFIG.maxVisible) {
        dismissNotification(existing[0]);
    }

    const toast = document.createElement('div');
    toast.className = 'notification-toast';
    toast.innerHTML = `
        <img class="notif-icon" src="${data.avatar}" alt="${data.username}">
        <div class="notif-body">
            <div class="notif-app">Instagram</div>
            <div class="notif-title">${data.username} a publié</div>
            <div class="notif-text">${data.caption}</div>
        </div>
        <span class="notif-time">à l'instant</span>
    `;

    toast.addEventListener('click', () => {
        dismissNotification(toast);
        // Si openApp existe (dans macos.js)
        if (typeof openApp === 'function') {
            openApp('Instagram');
        }
    });

    notifContainer.appendChild(toast);

    // Sauvegarder et mettre à jour le compte
    const unreadCount = saveNotificationToStorage(data);
    updateDockBadge('Instagram', unreadCount);

    playNotificationSound();

    setTimeout(() => {
        dismissNotification(toast);
    }, NOTIF_CONFIG.autoDismissMs);
}

function dismissNotification(toast) {
    if (!toast || toast.classList.contains('removing')) return;
    toast.classList.add('removing');
    toast.addEventListener('animationend', () => {
        toast.remove();
    });
}

function updateDockBadge(appName, count) {
    const dockIcon = document.querySelector(`.dock-icon[data-app="${appName}"]`);
    if (!dockIcon) return;

    let badge = dockIcon.querySelector('.dock-badge');

    if (count <= 0) {
        if (badge) badge.remove();
        return;
    }

    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'dock-badge';
        dockIcon.appendChild(badge);
    }

    badge.textContent = count > 99 ? '99+' : count;
    badge.style.animation = 'none';
    badge.offsetHeight; // force reflow
    badge.style.animation = '';
}

function resetDockBadge(appName) {
    if (appName === 'Instagram') {
        localStorage.setItem('instagram_unread_count', '0');
        // Déclencher event
        window.dispatchEvent(new Event('storage'));
    }
    updateDockBadge(appName, 0);
}

function playNotificationSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(1047, ctx.currentTime + 0.08);
        osc.type = 'sine';

        gain.gain.setValueAtTime(0.08, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);

        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.3);
    } catch (e) { }
}

window.addEventListener('load', () => {
    // On se contente d'écouter et de nettoyer le LS.
    // La SEULE notification de mel_133 sera injectée par InterfaceWebModel (lemonde.fr-acces-complet)
});

// Écouter les modifs de storage (venant d'autres frames/pages) pour resync le badge
window.addEventListener('storage', (e) => {
    if (e.key === 'instagram_unread_count') {
        updateDockBadge('Instagram', parseInt(e.newValue || '0'));
    }
});
