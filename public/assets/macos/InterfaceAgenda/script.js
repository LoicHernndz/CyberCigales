/**
 * Liste des noms des mois en français.
 * Utilisé pour l'affichage du titre du calendrier.
 * @const {string[]}
 */
const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

/**
 * Date actuellement sélectionnée et affichée par le calendrier.
 * Initialisée par défaut à la date système lors du chargement.
 * @type {Date}
 */
let currentDate = new Date();

/**
 * Génère et affiche la grille du calendrier dans le DOM.
 *
 * Cette fonction effectue les opérations suivantes :
 * 1. Vide la grille existante.
 * 2. Met à jour le titre (Mois Année).
 * 3. Calcule le décalage pour commencer la semaine le Lundi.
 * 4. Génère les cases vides pour le début du mois.
 * 5. Génère les cases des jours avec détection de la date actuelle ("Aujourd'hui").
 * 6. Injecte aléatoirement des événements factices pour la démonstration.
 *
 * @returns {void}
 */
function renderCalendar() {
    // 1. Récupération des éléments du DOM
    // 'gridDays' est le conteneur (div) qui recevra les cases du calendrier
    const grid = document.getElementById('gridDays');
    // 'monthYear' est le titre h2 ou span qui affiche "Janvier 2024"
    const title = document.getElementById('monthYear');

    // 2. Nettoyage
    // On vide le contenu HTML de la grille pour éviter d'empiler
    // les mois les uns sur les autres lors d'un changement de date.
    grid.innerHTML = '';

    // 3. Mise à jour du Titre
    // On utilise le tableau 'monthNames' défini plus haut pour avoir le nom en français
    title.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

    // 4. Calculs des dates pour le mois affiché
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth(); // 0 = Janvier, 11 = Décembre

    // Date du premier jour du mois (ex: 1er Octobre)
    // Utile pour savoir quel jour de la semaine ça tombe.
    const firstDayOfMonth = new Date(year, month, 1);

    // Date du dernier jour du mois.
    // ASTUCE JS : En mettant le jour à "0" du mois suivant (month + 1),
    // JavaScript renvoie automatiquement le dernier jour du mois courant.
    const lastDayOfMonth = new Date(year, month + 1, 0);

    // On récupère le nombre total de jours (ex: 30, 31, ou 28/29)
    const daysInMonth = lastDayOfMonth.getDate();

    // 5. Ajustement du jour de départ (Lundi vs Dimanche)
    // JS : .getDay() renvoie 0 pour Dimanche, 1 pour Lundi... 6 pour Samedi.
    // Nous voulons : 0 pour Lundi ... 6 pour Dimanche.
    let startDay = firstDayOfMonth.getDay() - 1;

    // Si le résultat est -1, c'était un Dimanche (0 - 1), donc on le force à 6 (dernier jour de la semaine fr)
    if (startDay === -1) startDay = 6;

    // Date système actuelle (pour surligner "Aujourd'hui")
    const today = new Date();

    // 6. Boucle 1 : Le "Padding" (Cases vides)
    // On crée des cases vides pour combler les jours de la semaine précédente
    // avant le 1er du mois (ex: si le mois commence un Mercredi, Lundi et Mardi sont vides).
    for (let i = 0; i < startDay; i++) {
        const cell = document.createElement('div');
        // 'other-month' permet de les griser via CSS
        cell.classList.add('day-cell', 'other-month');
        grid.appendChild(cell);
    }

    // 7. Boucle 2 : Les jours réels du mois
    for (let i = 1; i <= daysInMonth; i++) {
        // Création de la cellule principale du jour
        const cell = document.createElement('div');
        cell.classList.add('day-cell');

        // Vérification : Est-ce aujourd'hui ?
        // On doit vérifier le Jour (i), le Mois et l'Année pour éviter de surligner
        // le 12 du mois suivant ou de l'année précédente.
        if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            cell.classList.add('current-day'); // Classe CSS pour mettre en surbrillance
        }

        // Création du numéro du jour (le petit chiffre dans le coin)
        const dayNum = document.createElement('span');
        dayNum.classList.add('day-number');
        dayNum.textContent = i;
        cell.appendChild(dayNum);

        // --- DÉBUT SIMULATION ---
        // Ajout aléatoire d'événements pour la démo
        // (À remplacer par un appel API ou une boucle sur des données réelles plus tard)
        if (Math.random() > 0.8) {
            addFakeEvent(cell, "Réunion", "work");
        }
        if (Math.random() > 0.9) {
            addFakeEvent(cell, "Sport", "personal");
        }

        // Ajout final de la cellule dans la grille
        grid.appendChild(cell);
    }
}

/**
 * Crée un élément DOM représentant un événement et l'ajoute à une cellule de jour.
 *
 * @param {HTMLElement} parent - L'élément DOM de la cellule du jour (container).
 * @param {string} text - Le libellé de l'événement.
 * @param {string} type - Le type d'événement (utilisé comme classe CSS, ex: 'work', 'personal').
 * @returns {void}
 */
function addFakeEvent(parent, text, type) {
    const evt = document.createElement('div');
    evt.classList.add('event', type);
    evt.textContent = text;
    parent.appendChild(evt);
}

/**
 * Change le mois affiché en ajoutant ou soustrayant un nombre de mois.
 * Gère automatiquement le changement d'année via l'objet Date de JS.
 *
 * @param {number} delta - Le nombre de mois à ajouter (ex: 1 pour suivant) ou retirer (ex: -1 pour précédent).
 * @returns {void}
 */
function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
}

/**
 * Réinitialise la vue du calendrier à la date d'aujourd'hui.
 * Met à jour `currentDate` avec une nouvelle instance de `Date` et rafraîchit l'affichage.
 *
 * @returns {void}
 */
function goToToday() {
    currentDate = new Date();
    renderCalendar();
}

// Initialisation au chargement de la page
renderCalendar();