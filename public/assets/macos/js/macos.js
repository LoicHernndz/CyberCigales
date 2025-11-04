// État Global de l'Application
const state = {
    openWindows: {}, // Clé: AppName, Valeur: DOM element de la fenêtre
    activeApp: 'Bureau' // Nom de l'application actuellement au premier plan
};

// =========================================================
// 1. FONCTIONS DE GESTION DU TEMPS ET DE LA BARRE DE MENUS
// =========================================================

/**
 * Met à jour l'heure affichée dans la barre de menus.
 */
function updateTime() {
    const timeDisplay = document.getElementById('time-display');
    const now = new Date();
    const options = {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false // Format 24h
    };
    timeDisplay.textContent = now.toLocaleTimeString('fr-FR', options);
}

/**
 * Met à jour le nom de l'application active dans la barre de menus.
 * @param {string} appName - Le nom de l'application.
 */
function setActiveAppName(appName) {
    const activeAppNameElement = document.getElementById('active-app-name');
    activeAppNameElement.textContent = appName;
    state.activeApp = appName;
}

/**
 * Bascule l'affichage du menu Apple (dropdown).
 */
function toggleAppleMenu() {
    const dropdown = document.getElementById('apple-menu-dropdown');
    dropdown.classList.toggle('hidden');
    // Cacher les autres menus si ouverts
    document.getElementById('file-menu-dropdown').classList.add('hidden');
}

/**
 * Bascule l'affichage du menu Fichier (dropdown).
 */
function toggleFileMenu() {
    const dropdown = document.getElementById('file-menu-dropdown');
    dropdown.classList.toggle('hidden');
    // Cacher les autres menus si ouverts
    document.getElementById('apple-menu-dropdown').classList.add('hidden');
}

// =========================================================
// 2. FONCTIONS DE GESTION DES FENÊTRES ET DES APPLICATIONS
// =========================================================

/**
 * Met à jour l'indicateur actif sous l'icône du Dock.
 * @param {string} appName - Le nom de l'application.
 * @param {boolean} isActive - Si l'application doit être marquée comme active.
 */
function updateDockIcon(appName, isActive) {
    const dockIcons = document.querySelectorAll('.dock-icon');
    dockIcons.forEach(icon => {
        // On récupère le nom de l'application pour cette icône à partir du contenu
        const iconAppName = icon.querySelector('div').textContent.split('(')[0].trim();
        let dot = icon.querySelector('.active-dot');

        if (iconAppName === appName) {
            if (isActive) {
                if (!dot) {
                    dot = document.createElement('div');
                    dot.className = 'active-dot absolute -bottom-1 w-1 h-1 bg-blue-500 rounded-full transition-all duration-300';
                    icon.appendChild(dot);
                }
            } else if (dot) {
                icon.removeChild(dot);
            }
        }
    });
}

/**
 * Crée le HTML pour une nouvelle fenêtre d'application.
 * @param {string} appName - Le nom de l'application.
 * @returns {HTMLElement} La fenêtre créée.
 */
function createNewWindow(appName) {
    const windowId = `app-window-${appName.toLowerCase().replace(/\s/g, '-')}`;
    const windowElement = document.createElement('div');

    // Définition de la structure de base, du style et de la draggabilité
    windowElement.id = windowId;
    windowElement.className = 'app-window fixed bg-white/70 backdrop-blur-md rounded-xl shadow-2xl overflow-hidden resize transition-shadow duration-300 ring-1 ring-gray-200';
    windowElement.style.width = '600px';
    windowElement.style.height = '400px';
    windowElement.style.minWidth = '300px';
    windowElement.style.minHeight = '200px';
    // Position de départ aléatoire mais centrée
    windowElement.style.left = `${(window.innerWidth / 2) - 300 + (Math.random() * 50 - 25)}px`;
    windowElement.style.top = `${(window.innerHeight / 2) - 200 + (Math.random() * 50 - 25)}px`;
    windowElement.style.zIndex = '100'; // S'assurer qu'elle est au-dessus du bureau

    // Barre de titre de la fenêtre (draggable handle)
    windowElement.innerHTML = `
        <div class="title-bar p-2 flex items-center bg-white/50 border-b border-gray-200 cursor-default" data-drag-handle="true">
            <div class="window-controls flex space-x-2 mr-3">
                <button class="w-3 h-3 bg-red-500 rounded-full hover:opacity-75" onclick="closeApp('${appName}')" title="Fermer"></button>
                <button class="w-3 h-3 bg-yellow-500 rounded-full hover:opacity-75" onclick="minimizeApp('${appName}')" title="Minimiser"></button>
                <button class="w-3 h-3 bg-green-500 rounded-full hover:opacity-75" onclick="maximizeApp('${appName}')" title="Agrandir"></button>
            </div>
            <span class="text-sm font-semibold">${appName}</span>
        </div>
        <div class="window-content p-4 overflow-y-auto h-[calc(100%-33px)] text-gray-700">
            ${getContentForApp(appName)}
        </div>
        <!-- Poignée de redimensionnement (dans le coin inférieur droit) -->
        <div class="resize-handle absolute bottom-0 right-0 w-3 h-3 cursor-nwse-resize"></div>
    `;

    document.getElementById('desktop').appendChild(windowElement);

    // Initialiser la draggabilité et le redimensionnement
    makeDraggable(windowElement);
    makeResizable(windowElement);

    return windowElement;
}

/**
 * Fournit un contenu simulé pour chaque application.
 * @param {string} appName - Le nom de l'application.
 * @returns {string} Le contenu HTML de la fenêtre.
 */
function getContentForApp(appName) {
    switch (appName) {
        case 'Finder':
            return `
                <h2 class="text-xl font-bold mb-3">Mes Fichiers</h2>
                <div class="space-y-1">
                    <p class="p-2 bg-gray-100 rounded">Documents (120 Mo)</p>
                    <p class="p-2 bg-gray-100 rounded">Images (5 Go)</p>
                    <p class="p-2 bg-gray-100 rounded">Téléchargements (2 Go)</p>
                </div>
                <p class="mt-4 text-xs text-gray-500">Ceci est une simulation de l'explorateur de fichiers.</p>
            `;
        case 'Web':
            return `
                <div class="h-8 bg-gray-200 flex items-center px-3 mb-4 rounded-lg">
                    <span class="text-gray-600">🌐 simulation-site-web.com</span>
                </div>
                <h1 class="text-3xl font-light text-center">Bienvenue sur le Web Simulé</h1>
                <p class="mt-4 text-center">Utilisez ce navigateur pour imaginer vos recherches les plus folles.</p>
            `;
        case 'Mail':
            return `
                <h2 class="text-xl font-bold mb-3">Boîte de Réception</h2>
                <div class="space-y-2">
                    <div class="p-2 border-l-4 border-blue-500 bg-blue-50 rounded shadow-sm">
                        <p class="font-semibold">Nouveau: Invitation à collaborer</p>
                        <p class="text-sm text-gray-600 truncate">Bonjour, j'aimerais vous ajouter à notre nouveau projet...</p>
                    </div>
                    <div class="p-2 bg-gray-50 rounded shadow-sm">
                        <p class="font-semibold text-gray-500">Rappel: Réunion demain</p>
                        <p class="text-sm text-gray-600 truncate">N'oubliez pas que la réunion d'équipe aura lieu à 10h...</p>
                    </div>
                </div>
            `;
        case 'Terminal':
            return `
                <div class="h-full bg-gray-900 text-green-400 p-2 font-mono text-sm overflow-auto">
                    $ Bienvenue sur le Terminal Simulé.<br>
                    $ ping 127.0.0.1<br>
                    > 64 bytes from 127.0.0.1: time=0.01 ms<br>
                    > 64 bytes from 127.0.0.1: time=0.01 ms<br>
                    $ <span class="blinking-cursor">_</span>
                </div>
                <style>
                    .blinking-cursor { animation: blink 1s step-end infinite; }
                    @keyframes blink { from, to { color: transparent } 50% { color: #48bb78 } }
                </style>
            `;
        default:
            return `<p class="text-lg text-center mt-8">Application "${appName}" lancée avec succès!</p><p class="text-xs text-center mt-2 text-gray-500">Fermez la fenêtre en cliquant sur le bouton rouge.</p>`;
    }
}


/**
 * Ouvre ou met au premier plan une application.
 * @param {string} appName - Le nom de l'application à ouvrir.
 */
function openApp(appName) {
    if (state.openWindows[appName]) {
        // L'application est déjà ouverte, la mettre au premier plan
        focusWindow(state.openWindows[appName]);
        // Si elle est minimisée, la restaurer
        state.openWindows[appName].classList.remove('minimized');
    } else {
        // Créer une nouvelle fenêtre
        const newWindow = createNewWindow(appName);
        state.openWindows[appName] = newWindow;
        focusWindow(newWindow); // Focus immédiatement
    }
    updateDockIcon(appName, true);
}

/**
 * Met une fenêtre au premier plan (gestion du z-index).
 * @param {HTMLElement} windowElement - L'élément DOM de la fenêtre.
 */
function focusWindow(windowElement) {
    // Retirer le z-index élevé de toutes les autres fenêtres
    document.querySelectorAll('.app-window').forEach(win => {
        win.style.zIndex = '100';
        win.classList.remove('active-window');
    });

    // Mettre au premier plan
    windowElement.style.zIndex = '101';
    windowElement.classList.add('active-window');

    // Mettre à jour le nom dans la barre de menus
    const appName = windowElement.querySelector('.title-bar span').textContent;
    setActiveAppName(appName);
}


/**
 * Gère le clic sur la fenêtre pour la mettre au premier plan.
 * @param {Event} e - L'événement de clic.
 */
function handleWindowClick(e) {
    let targetWindow = e.target.closest('.app-window');
    if (targetWindow && targetWindow !== state.openWindows[state.activeApp]) {
        focusWindow(targetWindow);
    }
}


// =========================================================
// 3. CONTRÔLES DE FENÊTRE (Fermer, Minimiser, Agrandir)
// =========================================================

/**
 * Ferme une application et sa fenêtre.
 * @param {string} appName - Le nom de l'application à fermer.
 */
function closeApp(appName) {
    const windowElement = state.openWindows[appName];
    if (windowElement) {
        windowElement.remove();
        delete state.openWindows[appName];
        updateDockIcon(appName, false);

        // Déterminer la prochaine application active
        const openApps = Object.keys(state.openWindows);
        if (openApps.length > 0) {
            const nextAppName = openApps[openApps.length - 1]; // La dernière ouverte
            focusWindow(state.openWindows[nextAppName]);
        } else {
            setActiveAppName('Bureau');
        }
    }
}

/**
 * Minimise la fenêtre (simplement la masque/déplace pour cette simulation).
 * @param {string} appName - Le nom de l'application à minimiser.
 */
function minimizeApp(appName) {
    const windowElement = state.openWindows[appName];
    if (windowElement) {
        // Dans une vraie simulation, elle s'animerait vers le Dock
        windowElement.classList.add('minimized');
        windowElement.style.zIndex = '99'; // Mettre en arrière-plan

        // Simuler le changement d'application active
        const openApps = Object.keys(state.openWindows).filter(name => name !== appName);
        if (openApps.length > 0) {
            const nextAppName = openApps[openApps.length - 1];
            focusWindow(state.openWindows[nextAppName]);
        } else {
            setActiveAppName('Bureau');
        }

        // Message de confirmation (non visible dans la simulation)
        console.log(`${appName} a été minimisé.`);
    }
}

/**
 * Agrandit la fenêtre (simplement la met en plein écran pour cette simulation).
 * @param {string} appName - Le nom de l'application à maximiser.
 */
function maximizeApp(appName) {
    const windowElement = state.openWindows[appName];
    if (windowElement) {
        if (windowElement.classList.contains('maximized')) {
            // Restaurer à la taille précédente (non implémenté pour la simplicité)
            windowElement.classList.remove('maximized');
            windowElement.style.width = '600px';
            windowElement.style.height = '400px';
        } else {
            windowElement.classList.add('maximized');
            // Mettre la fenêtre en plein écran (légèrement décalé pour la barre de menus)
            windowElement.style.left = '0';
            windowElement.style.top = '28px'; // Sous la barre de menus
            windowElement.style.width = '100vw';
            windowElement.style.height = 'calc(100vh - 4rem)'; // Moins la barre de menus et le dock
        }
    }
}

// =========================================================
// 4. LOGIQUE DE DRAGGABILITÉ (Déplacement) ET REDIMENSIONNEMENT
// =========================================================

/**
 * Rend une fenêtre déplaçable (draggable).
 * @param {HTMLElement} windowElement - L'élément DOM de la fenêtre.
 */
function makeDraggable(windowElement) {
    const titleBar = windowElement.querySelector('[data-drag-handle="true"]');
    let isDragging = false;
    let offsetX, offsetY;

    const startDrag = (e) => {
        // Ne rien faire si c'est un clic sur les boutons de contrôle
        if (e.target.closest('.window-controls')) return;

        isDragging = true;
        // Mettre la fenêtre au premier plan lors du début du drag
        focusWindow(windowElement);
        windowElement.classList.remove('maximized'); // Annuler le mode maximisé si l'utilisateur essaie de bouger la fenêtre

        // Calculer le décalage entre la souris et le coin supérieur gauche de la fenêtre
        offsetX = e.clientX - windowElement.offsetLeft;
        offsetY = e.clientY - windowElement.offsetTop;

        // Ajouter un écouteur global pour le mouvement et la fin du drag
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDrag);

        e.preventDefault(); // Empêcher la sélection de texte
    };

    const drag = (e) => {
        if (!isDragging) return;

        let newX = e.clientX - offsetX;
        let newY = e.clientY - offsetY;

        // Limiter le mouvement (optionnel, mais améliore l'UX)
        newX = Math.max(0, Math.min(newX, window.innerWidth - windowElement.offsetWidth));
        newY = Math.max(28, Math.min(newY, window.innerHeight - windowElement.offsetHeight)); // 28px pour la barre de menus

        windowElement.style.left = `${newX}px`;
        windowElement.style.top = `${newY}px`;
    };

    const stopDrag = () => {
        isDragging = false;
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', stopDrag);
    };

    titleBar.addEventListener('mousedown', startDrag);
}

/**
 * Rend une fenêtre redimensionnable.
 * @param {HTMLElement} windowElement - L'élément DOM de la fenêtre.
 */
function makeResizable(windowElement) {
    const handle = windowElement.querySelector('.resize-handle');
    let isResizing = false;
    let startX, startY, startWidth, startHeight;

    const startResize = (e) => {
        isResizing = true;
        // Mettre la fenêtre au premier plan
        focusWindow(windowElement);

        windowElement.classList.remove('maximized');

        // Enregistrer la position et la taille de départ
        startX = e.clientX;
        startY = e.clientY;
        startWidth = windowElement.offsetWidth;
        startHeight = windowElement.offsetHeight;

        document.addEventListener('mousemove', resize);
        document.addEventListener('mouseup', stopResize);

        e.preventDefault();
    };

    const resize = (e) => {
        if (!isResizing) return;

        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;

        let newWidth = startWidth + deltaX;
        let newHeight = startHeight + deltaY;

        // Limiter la taille à minWidth/minHeight
        newWidth = Math.max(parseInt(windowElement.style.minWidth), newWidth);
        newHeight = Math.max(parseInt(windowElement.style.minHeight), newHeight);

        windowElement.style.width = `${newWidth}px`;
        windowElement.style.height = `${newHeight}px`;
    };

    const stopResize = () => {
        isResizing = false;
        document.removeEventListener('mousemove', resize);
        document.removeEventListener('mouseup', stopResize);
    };

    handle.addEventListener('mousedown', startResize);
}


// =========================================================
// 5. INITIALISATION
// =========================================================

window.addEventListener('load', () => {
    // 1. Initialiser l'heure
    updateTime();
    // Mettre à jour l'heure toutes les 60 secondes (pour les minutes)
    setInterval(updateTime, 60000);

    // 2. Gestion des clics en dehors des menus pour les cacher
    document.addEventListener('click', (e) => {
        const appleMenu = document.getElementById('apple-menu');
        const fileMenuToggle = document.getElementById('file-menu-toggle');

        // Si le clic n'est ni sur l'Apple Menu ni sur le File Menu, cacher les deux
        if (!appleMenu.contains(e.target)) {
            document.getElementById('apple-menu-dropdown').classList.add('hidden');
        }
        if (!fileMenuToggle.contains(e.target) && e.target !== document.getElementById('file-menu-dropdown')) {
            document.getElementById('file-menu-dropdown').classList.add('hidden');
        }
    });

    // 3. Gestion de l'activation des fenêtres par clic
    document.getElementById('desktop').addEventListener('mousedown', handleWindowClick);

    // 4. Exécuter openApp pour le Finder au chargement (pour simuler l'ouverture automatique)
    // openApp('Finder');
});

// Exporter les fonctions pour qu'elles soient accessibles depuis le HTML (comme openApp)
window.openApp = openApp;
window.closeApp = closeApp;
window.minimizeApp = minimizeApp;
window.maximizeApp = maximizeApp;
window.toggleAppleMenu = toggleAppleMenu;
window.toggleFileMenu = toggleFileMenu;
