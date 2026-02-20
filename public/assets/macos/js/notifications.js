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

const SIMULATED_POSTS = [
    { username: 'mel_133', avatar: '/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png', caption: "Nouveau look pour cette nouvelle semaine ! 💫✨ #fashion #style" },
    { username: 'alex_photo', avatar: '/images/instagram/alexander-schimmeck-2zJhA9RSkys-unsplash.jpg', caption: "Golden hour à Paris 🌇 Un moment magique #photography" },
    { username: 'anna_food', avatar: '/images/instagram/anna-bratiychuk-IeNoBmJ011g-unsplash.jpg', caption: "Nouvelle recette : tiramisu aux fraises 🍓🍰 #foodie" },
    { username: 'heather_travel', avatar: '/images/instagram/heather-barnes-CNDiESvWfrk-unsplash.jpg', caption: "Les rizières de Bali au lever du soleil 🌅 #travel" },
    { username: 'leo_creative', avatar: '/images/instagram/leo_visions-n5ojSxRb1Vs-unsplash.jpg', caption: "Nouvelle illustration terminée ! 🎨 Qu'en pensez-vous ?" },
    { username: 'corina_pets', avatar: '/images/instagram/corina-rainer-sScNrKruEPs-unsplash.jpg', caption: "Caramel apprend à donner la patte ! 🐾 Trop fier de lui 🥺" },
    { username: 'mike_coffee', avatar: '/images/instagram/mike-kenneally-TD4DBagg2wE-unsplash.jpg', caption: "Nouveau blend éthiopien ☕ Notes de myrtille et chocolat" },
    { username: 'monika_cuisine', avatar: '/images/instagram/monika-grabkowska-EbRBhZ-I_p8-unsplash.jpg', caption: "Tajine marocain fait maison 🍲 La recette arrive bientôt !" },
    {
        username: 'diliara_style', avatar: '/images/instagram/diliara-garifullina-I48gnI1Qs5o-unsplash.jpg', caption: "Haul soldes d'hiver 🛍️ Mes 5 meilleures trouvailles !"
    },
    { username: 'annie_nature', avatar: '/images/instagram/annie-spratt-e92dhXE8PUg-unsplash.jpg', caption: "Les cerisiers en fleurs au jardin 🌸 Le printemps arrive !" }
];

let usedPostIndices = [];
function getRandomPost() {
    if (usedPostIndices.length >= SIMULATED_POSTS.length) usedPostIndices = [];
    let index;
    do { index = Math.floor(Math.random() * SIMULATED_POSTS.length); } while (usedPostIndices.includes(index));
    usedPostIndices.push(index);
    return SIMULATED_POSTS[index];
}

function scheduleNextNotification() {
    const delay = NOTIF_CONFIG.minIntervalMs + Math.random() * (NOTIF_CONFIG.maxIntervalMs - NOTIF_CONFIG.minIntervalMs);
    setTimeout(() => {
        showNotification(getRandomPost());
        scheduleNextNotification();
    }, delay);
}

// Initialisation globale
window.addEventListener('load', () => {
    initNotificationContainer();

    // Restaurer le badge au chargement
    const unread = parseInt(localStorage.getItem('instagram_unread_count') || '0');
    if (unread > 0) {
        updateDockBadge('Instagram', unread);
    }

    // Lancer la boucle de simulation
    setTimeout(() => {
        showNotification(getRandomPost());
        scheduleNextNotification();
    }, 8000 + Math.random() * 7000); // 8-15s pour le rpeimier
});

// Écouter les modifs de storage (venant d'autres frames/pages) pour resync le badge
window.addEventListener('storage', (e) => {
    if (e.key === 'instagram_unread_count') {
        updateDockBadge('Instagram', parseInt(e.newValue || '0'));
    }
});
