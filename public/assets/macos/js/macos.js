// État Global de l'Application
const state = {
    openWindows: {}, // Clé: AppName, Valeur: DOM element de la fenêtre
    activeApp: 'Bureau', // Nom de l'application actuellement au premier plan
    commandHistory: [], // Tableau pour stocker l'historique des commandes du Terminal
    historyIndex: -1, // Index pour la navigation dans l'historique (0 = plus ancienne, -1 = nouvelle ligne)
    // NOUVEL ÉTAT : Ajout de l'état de connexion pour le Terminal
    isTerminalLoggedIn: false
};

// =========================================================
// 0. SYSTÈME DE FICHIERS SIMULÉ ET ÉTAT DU TERMINAL
// =========================================================

// Représente le répertoire de travail actuel, initialisé à 'home'
let currentPath = ['home'];

// NOUVEAUX IDENTIFIANTS : Définition des identifiants (simples pour la démo)
const LOGIN_CREDENTIALS = {
    user: 'admin',
    pass: 'secure' // Note: Ce mot de passe est simple pour la démo. L'indice se trouve dans les fichiers si l'utilisateur doit le deviner.
};

/**
 * Arborescence complète du système de fichiers simulé.
 * 'dir' = Répertoire, 'file' = Fichier
 */
const fileSystem = {
    name: 'home', // Le répertoire racine (équivalent à ~)
    type: 'dir',
    children: {
        'images': {
            type: 'dir',
            children: {
                'photos-ete-2025': { type: 'dir', children: {} },
                'photos-printemps-2025': { type: 'dir', children: {} },
                'photos-hiver-2025': { type: 'dir', children: {} },
                'photos-automne-2025': { type: 'dir', children: {} },
                'photos-hiver-2024': { type: 'dir', children: {} },
                'photos-ete-2024': { type: 'dir', children: {} },
                'photos-hiver-2022': { type: 'dir', children: {} },
            }
        },
        'videos': {
            type: 'dir',
            children: {
                'videos-ete-2025': { type: 'dir', children: {} },
                'videos-printemps-2025': { type: 'dir', children: {} },
                'videos-hiver-2025': { type: 'dir', children: {} },
                'videos-automne-2025': { type: 'dir', children: {} },
                'videos-hiver-2024': { type: 'dir', children: {} },
                'videos-ete-2024': { type: 'dir', children: {} },
                'videos-hiver-2022': { type: 'dir', children: {} },
            }
        },
        'documents': {
            type: 'dir',
            children: {
                'documents-professionnels': {
                    type: 'dir',
                    children: {
                        'projet-jeu-roblox': { type: 'dir', children: {} },
                        'cours-python.txt': { type: 'file', content: 'Ceci est un document de cours sur les bases et la syntaxe de Python.' },
                        'cours-maths.txt': { type: 'file', content: 'Notes de cours avancées en algèbre linéaire et calcul différentiel.' },
                        'cours-sql.txt': { type: 'file', content: 'Introduction et requêtes complexes pour la gestion de bases de données relationnelles.' },
                        'cours-javascript.txt': { type: 'file', content: 'Un guide de référence rapide pour les fonctions asynchrones et l\'API DOM.' },
                        'methode-brut-force.txt': { type: 'file', content: 'La méthode par brute force est le fait de tester comme un "bourin" un très grand nombre de possibilités de caractère en espérant tomber sur le bon mot de passe. ' +
                                "Cela est donc très long. \nMais si on test cette méthode avec des éléments que nous estimons suceptible d'être présent dans le mot de passe, le temps nécessaire pour trouver ce mot de passe s'en trouve donc réduit."}
                    }
                },
                'documents-personnels': {
                    type: 'dir',
                    children: {
                        'anniversaires.txt': { type: 'file', content: 'Mère: 12 Janvier\nPère: 25 Mai' },
                        'la-plus-belle-ville-du-monde.txt': { type: 'file', content: "Ô ma Marseille, cité de lumière, où le ciel bleu rencontre la mer.\nDu Vieux-Port vibrant aux calanques secrètes, nulle part ailleurs je n'ai trouvé tant de fêtes.\nTes quartiers chantent, ton soleil réchauffe l'âme, tu es la plus belle ville, mon éternelle flamme." }
                    }
                },
                'documents-confidentiels': {
                    type: 'dir',
                    children: {
                        'mot-de-passe-twitter.txt': { type: 'file', content: 'Utilisateur: @fakeAccount_42\nMot de passe: SecureP@sswOrd123' },
                        'mot-de-passe-email.txt': { type: 'file', content: 'Le mot de passe pour l\'adresse e-mail personnelle est: EmailS3cret!' }
                    }
                }
            }
        },
        'telechargements': { type: 'dir', children: {} },
        'musiques': { type: 'dir', children: {} },
    }
};

/**
 * Navigue dans le système de fichiers pour trouver un répertoire ou fichier.
 * @param {string[]} pathArray - Le chemin absolu ou relatif à suivre.
 * @returns {object | null} L'objet du système de fichiers trouvé, ou null.
 */
function getFileSystemObject(pathArray) {
    let currentObject = fileSystem;
    if (pathArray.length === 1 && pathArray[0] === 'home') {
        return fileSystem;
    }

    for (let i = 1; i < pathArray.length; i++) {
        const segment = pathArray[i];
        if (!currentObject.children || !currentObject.children[segment]) {
            return null;
        }
        currentObject = currentObject.children[segment];
    }
    return currentObject;
}

/**
 * Construit la chaîne de répertoire de travail pour le prompt (~/path/to/dir).
 * @returns {string} Le chemin formaté.
 */
function getPromptPath() {
    if (currentPath.length === 1 && currentPath[0] === 'home') {
        return '~';
    }
    // Supprimer 'home' du début et joindre le reste
    return '~/' + currentPath.slice(1).join('/');
}


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
        <div class="resize-handle absolute bottom-0 right-0 w-3 h-3 cursor-nwse-resize"></div>
    `;

    document.getElementById('desktop').appendChild(windowElement);

    // Initialiser la draggabilité et le redimensionnement
    makeDraggable(windowElement);
    makeResizable(windowElement);

    // Initialisation spécifique du terminal après création
    if (appName === 'Terminal') {
        initializeTerminal(windowElement);
    }

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
            // UTILISE LA NOUVELLE FONCTION POUR LE CONTENU DU TERMINAL
            return getTerminalContent();
        default:
            return `<p class="text-lg text-center mt-8">Application "${appName}" lancée avec succès!</p><p class="text-xs text-center mt-2 text-gray-500">Fermez la fenêtre en cliquant sur le bouton rouge.</p>`;
    }
}

/**
 * NOUVELLE FONCTION: Fournit le contenu HTML initial pour la fenêtre Terminal (écran de connexion ou console).
 * @returns {string} Le contenu HTML de la fenêtre Terminal.
 */
function getTerminalContent() {
    // Si l'utilisateur est déjà connecté, on affiche l'invite normale, sinon l'écran de connexion
    const initialPromptText = state.isTerminalLoggedIn ? getPromptPath() + ' $ ' : 'Nom d\'utilisateur: ';
    // Commence par 'text' car on attend le nom d'utilisateur en clair
    const initialInputType = 'text';

    return `
        <div id="terminal-wrapper" class="h-full bg-gray-900 text-green-400 p-2 font-mono text-sm overflow-auto flex flex-col">
            <div id="terminal-output" class="flex-grow overflow-y-auto">
                <span class="text-gray-400">** MacOS Terminal Simulé **</span><br>
                ${state.isTerminalLoggedIn ?
        `<span class="text-gray-400">Bienvenue. Tapez '<span class="text-yellow-400">help</span>' pour les commandes.</span><br>` :
        `<span class="text-gray-400">Connexion requise pour accéder au système de fichiers.</span><br>`
    }
            </div>
            
            <div id="terminal-input-line" class="flex items-center">
                <input id="terminal-input" type="${initialInputType}" spellcheck="false" class="absolute left-0 top-0 w-full h-full bg-transparent border-none text-transparent caret-green-400 z-50 focus:outline-none" style="left: -9999px; opacity: 0;">
                
                <span id="terminal-prompt" class="text-blue-400">${initialPromptText}</span>
                <span id="terminal-display-input" class="whitespace-pre text-white"></span>
                <span id="terminal-cursor" class="blinking-cursor w-2 h-4 inline-block bg-green-400"></span>
            </div>
        </div>
        <style>
            /* Style CSS intégré pour l'animation du curseur */
            .blinking-cursor { 
                animation: blink 1s step-end infinite; 
                display: inline-block;
                vertical-align: middle;
                margin-left: 2px;
                margin-right: -4px;
            }
            @keyframes blink { 
                from, to { opacity: 0 } 
                50% { opacity: 1 } 
            }
            /* Forcer le terminal à utiliser un style de curseur de texte */
            #terminal-wrapper {
                cursor: text;
            }
            /* Ajuster la hauteur de la zone de contenu pour le terminal */
            #app-window-terminal .window-content {
                height: calc(100% - 33px); /* 100% - hauteur de la barre de titre */
                padding: 0; /* Pas de padding pour un look plein écran de la console */
            }
            .file { color: #ffffff; }
            .dir { color: #5BBAFF; }
        </style>
    `;
}


/**
 * Logique d'initialisation pour rendre le terminal interactif.
 * @param {HTMLElement} windowElement - L'élément DOM de la fenêtre Terminal.
 */
function initializeTerminal(windowElement) {
    const output = windowElement.querySelector('#terminal-output');
    const input = windowElement.querySelector('#terminal-input');
    const displayInput = windowElement.querySelector('#terminal-display-input');
    const promptElement = windowElement.querySelector('#terminal-prompt');
    const wrapper = windowElement.querySelector('#terminal-wrapper');
    // Supprimer l'ancienne référence à l'élément d'affichage du nom d'utilisateur

    // NOUVEAU: Variables d'état interne pour la connexion
    let currentLoginStep = state.isTerminalLoggedIn ? 'logged-in' : 'username'; // 'username', 'password', ou 'logged-in'
    let enteredUsername = '';

    // Mettre à jour l'input initial. Si on est en mode "username", l'input doit être de type "text" au début.
    if (!state.isTerminalLoggedIn) {
        promptElement.textContent = 'Nom d\'utilisateur: ';
        input.type = 'text'; // Démarre en mode text
    } else {
        promptElement.textContent = getPromptPath() + ' $ ';
        input.type = 'text';
    }


    // 1. Mettre le focus sur l'input quand on clique n'importe où dans le wrapper
    wrapper.addEventListener('click', () => {
        input.focus();
    });

    // 2. Mettre à jour le texte affiché par rapport à l'input invisible
    input.addEventListener('input', () => {
        // NOUVEAU: Logique unifiée pour l'affichage en clair du nom d'utilisateur et des commandes.
        if (state.isTerminalLoggedIn || currentLoginStep === 'username') {
            // Afficher le texte saisi en clair
            displayInput.textContent = input.value;
        } else if (currentLoginStep === 'password') {
            // Afficher des étoiles pour le mot de passe
            displayInput.textContent = '*'.repeat(input.value.length);
        }

        // Scroll automatique vers le bas
        output.scrollTop = output.scrollHeight;
    });

    // 3. Gestion de l'exécution de commande (touche Entrée) ET navigation dans l'historique
    input.addEventListener('keydown', (e) => {
        const fullLine = input.value;

        if (e.key === 'Enter') {
            e.preventDefault();

            // --- LOGIQUE DE CONNEXION ---
            if (!state.isTerminalLoggedIn) {
                // Étape 1: Saisie du Nom d'utilisateur
                if (currentLoginStep === 'username') {
                    // Simuler l'affichage de l'entrée précédente (Nom d'utilisateur: [nom tapé])
                    output.innerHTML += `${promptElement.textContent}<span class="text-white">${fullLine}</span><br>`;

                    enteredUsername = fullLine.trim();
                    currentLoginStep = 'password';

                    // Passer à l'input de mot de passe
                    promptElement.textContent = 'Mot de passe: ';
                    input.type = 'password'; // Assurer qu'il est de type password
                    input.value = '';
                    displayInput.textContent = '';

                    // Étape 2: Saisie du Mot de passe
                } else if (currentLoginStep === 'password') {
                    // Vérifier les identifiants
                    if (enteredUsername === LOGIN_CREDENTIALS.user && fullLine === LOGIN_CREDENTIALS.pass) {
                        // CONNEXION RÉUSSIE
                        output.innerHTML += `${promptElement.textContent}<span class="text-white">********</span><br>`; // Afficher des étoiles pour l'historique
                        state.isTerminalLoggedIn = true;
                        output.innerHTML += `<span class="text-green-400">Connexion réussie! Bienvenue, ${enteredUsername}.</span><br>`;
                        output.innerHTML += `<span class="text-gray-400">Tapez '<span class="text-yellow-400">help</span>' pour les commandes.</span><br>`;

                        // Réinitialiser pour le mode console normal
                        currentLoginStep = 'logged-in';
                        input.type = 'text'; // Repasser en mode texte pour les commandes
                        input.value = '';
                        displayInput.textContent = '';
                        // Mettre à jour le prompt
                        promptElement.textContent = getPromptPath() + ' $ ';

                    } else {
                        // ÉCHEC DE LA CONNEXION
                        output.innerHTML += `${promptElement.textContent}<span class="text-white">********</span><br>`;
                        output.innerHTML += `<span class="text-red-400">Échec de la connexion. Veuillez réessayer.</span><br>`;

                        // Réinitialiser les étapes
                        currentLoginStep = 'username';
                        enteredUsername = '';

                        // Revenir à l'input de nom d'utilisateur
                        promptElement.textContent = 'Nom d\'utilisateur: ';
                        input.type = 'text';
                        input.value = '';
                        displayInput.textContent = '';
                    }
                }
            }
            // --- LOGIQUE DE COMMANDE NORMALE (SI CONNECTÉ) ---
            else if (state.isTerminalLoggedIn) {
                // Exécuter la commande (uniquement si elle n'est pas vide)
                if (fullLine.trim() !== '') {
                    // Afficher la commande tapée dans l'historique
                    output.innerHTML += `<span class="text-blue-400">${getPromptPath()} $ </span><span class="text-white">${fullLine}</span><br>`; // Ajout de text-white ici aussi

                    // Exécuter la commande
                    executeCommand(fullLine, output, promptElement);
                } else {
                    // Si la commande est vide, ajouter juste le prompt et passer à la ligne
                    output.innerHTML += `<span class="text-blue-400">${getPromptPath()} $ </span><br>`;
                }

                // Réinitialiser l'input et l'historique
                input.value = '';
                displayInput.textContent = '';
                state.historyIndex = state.commandHistory.length; // Réinitialiser l'index à la fin
            }

            // Scroll automatique vers le bas
            output.scrollTop = output.scrollHeight;

            // Gestion des flèches Haut/Bas pour l'historique (seulement si connecté)
        } else if (state.isTerminalLoggedIn && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
            e.preventDefault(); // Empêcher le déplacement du curseur dans le champ de saisie

            // Logique de navigation dans l'historique
            let newIndex = state.historyIndex;

            if (e.key === 'ArrowUp') {
                newIndex--;
            } else if (e.key === 'ArrowDown') {
                newIndex++;
            }

            if (newIndex >= 0 && newIndex < state.commandHistory.length) {
                state.historyIndex = newIndex;
                const command = state.commandHistory[state.historyIndex];
                input.value = command;
                displayInput.textContent = command;
                input.selectionStart = input.selectionEnd = input.value.length;
            } else if (newIndex === state.commandHistory.length) {
                state.historyIndex = newIndex;
                input.value = '';
                displayInput.textContent = '';
            } else if (state.commandHistory.length > 0) {
                state.historyIndex = Math.max(0, Math.min(newIndex, state.commandHistory.length));
            } else {
                state.historyIndex = -1;
            }
        }
    });

    // S'assurer que l'input a le focus au lancement
    setTimeout(() => input.focus(), 100);
}

/**
 * Exécute une commande de terminal simulée et affiche le résultat.
 * @param {string} fullCommand - La ligne de commande complète (ex: 'cd documents/personnels').
 * @param {HTMLElement} outputElement - L'élément de sortie de la console.
 * @param {HTMLElement} promptElement - L'élément pour mettre à jour le chemin.
 */
function executeCommand(fullCommand, outputElement, promptElement) {
    // 1. Mise à jour de l'historique
    const trimmedCommand = fullCommand.trim();
    if (trimmedCommand !== '') {
        // Ajouter la commande à l'historique si elle n'est pas vide
        if (state.commandHistory[state.commandHistory.length - 1] !== trimmedCommand) {
            state.commandHistory.push(trimmedCommand);
        }
    }

    const [command, ...args] = trimmedCommand.split(/\s+/);
    let response = '';
    const arg = args[0] || '';

    // NOUVELLE LOGIQUE: Bloquer les commandes du système de fichiers si l'utilisateur n'est pas connecté
    const fileSystemCommands = ['ls', 'cd', 'cat'];
    if (!state.isTerminalLoggedIn && fileSystemCommands.includes(command.toLowerCase())) {
        response = `<span class="text-red-400">Accès refusé. Vous devez être connecté pour exécuter cette commande.</span>`;
        // Ajouter la réponse
        outputElement.innerHTML += `<span class="text-white">${response}</span><br>`;

        // Mettre à jour le prompt après l'exécution de la commande
        promptElement.textContent = getPromptPath() + ' $ ';
        return; // ARRÊTER L'EXÉCUTION
    }

    switch (command.toLowerCase()) {
        case 'help':
            response = 'Commandes disponibles: <span class="text-yellow-400">help</span>, <span class="text-yellow-400">clear</span>, <span class="text-yellow-400">echo &lt;texte&gt;</span>, <span class="text-yellow-400">ping</span>.<br>' +
                'Commandes du système de fichiers:<br>' +
                ' <span class="text-yellow-400">ls</span>: Lister les fichiers et répertoires.<br>' +
                ' <span class="text-yellow-400">cd &lt;chemin&gt;</span>: Changer de répertoire (ex: cd ../images).<br>' +
                ' <span class="text-yellow-400">cat &lt;fichier&gt;</span>: Afficher le contenu d\'un fichier.';
            break;
        case 'clear':
            // Efface l'historique
            outputElement.innerHTML = '';
            break;
        case 'ping':
            response = 'Pinging 127.0.0.1...<br>64 bytes from 127.0.0.1: time=0.01 ms<br>64 bytes from 127.0.0.1: time=0.01 ms<br>Done.';
            break;
        case 'echo':
            response = args.join(' ');
            break;

        // --- Commandes du Système de Fichiers ---
        case 'ls':
            response = executeLs(outputElement);
            break;
        case 'cd':
            response = executeCd(arg, promptElement);
            break;
        case 'cat':
            response = executeCat(arg);
            break;

        default:
            if (command === '') {
                response = ''; // Ne rien afficher pour une commande vide
            } else {
                response = `<span class="text-red-400">Erreur: commande non trouvée: ${command}</span>`;
            }
            break;
    }

    if (response) {
        // Ajouter la réponse
        outputElement.innerHTML += `<span class="text-white">${response}</span><br>`;
    }

    // Mettre à jour le prompt après l'exécution de la commande
    promptElement.textContent = getPromptPath() + ' $ ';
}

/**
 * Gère la commande 'ls'.
 * @param {HTMLElement} outputElement - L'élément de sortie de la console.
 * @returns {string} Le contenu formaté à afficher.
 */
function executeLs(outputElement) {
    const currentDir = getFileSystemObject(currentPath);
    if (!currentDir || currentDir.type !== 'dir') {
        return `<span class="text-red-400">Erreur: Le chemin actuel est invalide ou n'est pas un répertoire.</span>`;
    }

    const children = currentDir.children;
    let output = '';
    const names = Object.keys(children).sort();

    // Affichage des éléments en ligne, en colonnes
    const columnCount = 4;

    for (let i = 0; i < names.length; i++) {
        const name = names[i];
        const item = children[name];
        let displayClass = item.type === 'dir' ? 'dir' : 'file';
        let displayName = name + (item.type === 'dir' ? '/' : ''); // Ajouter / pour les dossiers

        // Créer l'élément HTML avec la bonne classe pour la couleur
        output += `<span class="${displayClass}" style="display: inline-block; width: 25%; min-width: 150px; margin-right: 1rem;">${displayName}</span>`;

        // Ajouter un saut de ligne après chaque colonneCount élément
        if ((i + 1) % columnCount === 0) {
            output += '<br>';
        }
    }

    // Si la dernière ligne n'a pas été terminée par un <br>, il faut l'ajouter
    if (names.length % columnCount !== 0) {
        output += '<br>';
    }

    return output;
}

/**
 * Gère la commande 'cat'.
 * @param {string} fileName - Le nom du fichier à afficher.
 * @returns {string} Le contenu du fichier ou un message d'erreur.
 */
function executeCat(fileName) {
    if (!fileName) {
        return `<span class="text-red-400">Erreur: Spécifiez un nom de fichier. Usage: cat &lt;nomDuFichier&gt;</span>`;
    }

    const currentDir = getFileSystemObject(currentPath);
    if (!currentDir || !currentDir.children) {
        return `<span class="text-red-400">Erreur système: Répertoire actuel introuvable.</span>`;
    }

    const item = currentDir.children[fileName];

    if (!item) {
        return `<span class="text-red-400">Erreur: Fichier '${fileName}' non trouvé.</span>`;
    }

    if (item.type === 'dir') {
        return `<span class="text-red-400">Erreur: '${fileName}' est un répertoire. Utilisez 'ls' pour voir son contenu.</span>`;
    }

    return `<span class="text-gray-300 whitespace-pre-wrap">${item.content}</span>`;
}

/**
 * Gère la commande 'cd'.
 * @param {string} targetPath - Le chemin cible (ex: '..', 'images', 'documents/personnels').
 * @param {HTMLElement} promptElement - L'élément pour mettre à jour le chemin.
 * @returns {string} Message d'erreur ou chaîne vide en cas de succès.
 */
function executeCd(targetPath, promptElement) {
    if (!targetPath || targetPath === '~' || targetPath === '/') {
        // cd, cd ~ ou cd / ramène à la maison
        currentPath = ['home'];
        return '';
    }

    // Clonage du chemin actuel pour la résolution
    let newPath = [...currentPath];
    // Sépare le chemin en segments, en filtrant les segments vides (ex: si le chemin commence/finit par /)
    const segments = targetPath.split('/').filter(s => s.length > 0);

    for (const segment of segments) {
        if (segment === '..') {
            // Remonter d'un niveau, sauf si nous sommes au répertoire 'home'
            if (newPath.length > 1) {
                newPath.pop();
            }
        } else if (segment === '.') {
            // Reste dans le répertoire courant
            continue;
        } else {
            // Tenter de descendre
            const currentDirObject = getFileSystemObject(newPath);
            if (!currentDirObject || currentDirObject.type !== 'dir' || !currentDirObject.children || !currentDirObject.children[segment]) {
                return `<span class="text-red-400">cd: Répertoire non trouvé: ${targetPath}</span>`;
            }

            const targetObject = currentDirObject.children[segment];
            if (targetObject.type !== 'dir') {
                return `<span class="text-red-400">cd: Le chemin spécifié n'est pas un répertoire: ${segment}</span>`;
            }

            newPath.push(segment);
        }
    }

    // Si tout va bien, mettez à jour le chemin global
    currentPath = newPath;
    return '';
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

        // NOUVEAU: Si le terminal est fermé, réinitialiser l'état de connexion pour la prochaine ouverture
        if (appName === 'Terminal') {
            state.isTerminalLoggedIn = false;
        }

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