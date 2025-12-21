/**
 * JavaScript pour le jeu du carré de Hamming
 * Suit les conventions du cours (S_, I_, B_, A_, O_)
 * 
 * Ce fichier gère l'affichage et l'interaction du carré de Hamming :
 * - Génération du HTML du carré 3x3
 * - Gestion des clics sur les cellules
 * - Communication avec le serveur via AJAX
 * - Mise à jour dynamique du carré sans rechargement de page
 */

/**
 * Génère le HTML du carré 3x3 à partir des données
 * 
 * @param {Array} A_squareData - Tableau 2D représentant le carré 3x3
 *                                Format : [[bit, bit, bit], [bit, bit, bit], [bit, bit, bit]]
 * @return {string} - Chaîne HTML contenant 9 boutons représentant le carré
 */
function generateSquareHtml(A_squareData) {
    // Initialiser une chaîne vide qui contiendra tout le HTML généré
    var S_html = '';
    
    // Première boucle : parcourir les 3 lignes du carré (indices 0, 1, 2)
    for (var I_row = 0; I_row < 3; I_row++) {
        // Deuxième boucle : parcourir les 3 colonnes de chaque ligne (indices 0, 1, 2)
        for (var I_col = 0; I_col < 3; I_col++) {
            // Récupérer la valeur du bit à la position [ligne][colonne]
            // Exemple : A_squareData[0][1] = le bit à la ligne 0, colonne 1
            var I_bit = A_squareData[I_row][I_col];
            
            // Construire le HTML d'un bouton pour cette cellule
            // - class="bit-cell" : classe CSS pour le style
            // - data-row : attribut data contenant le numéro de ligne (0, 1 ou 2)
            // - data-col : attribut data contenant le numéro de colonne (0, 1 ou 2)
            // - Contenu du bouton : la valeur du bit (0 ou 1)
            // Exemple résultat : <button class="bit-cell" data-row="0" data-col="1">0</button>
            S_html += '<button class="bit-cell" data-row="' + I_row + '" data-col="' + I_col + '">' + I_bit + '</button>';
        }
    }
    
    // Retourner la chaîne HTML complète contenant les 9 boutons
    return S_html;
}

/**
 * Met à jour le carré dans le DOM (Document Object Model)
 * 
 * Cette fonction remplace le contenu HTML du conteneur par un nouveau carré
 * et réattache les event listeners pour que les clics fonctionnent
 * 
 * @param {Array} A_squareData - Nouveau carré 3x3 à afficher
 */
function updateSquare(A_squareData) {
    // Récupérer l'élément HTML qui contient le carré (div avec id="square-container")
    // getElementById retourne l'élément ou null s'il n'existe pas
    var O_container = document.getElementById('square-container');
    
    // Vérifier que :
    // - Le conteneur existe (O_container n'est pas null)
    // - Les données du carré existent (A_squareData n'est pas null/undefined)
    // - Le carré a bien 3 lignes (A_squareData.length === 3)
    if (O_container && A_squareData && A_squareData.length === 3) {
        // Remplacer tout le contenu HTML du conteneur par le nouveau carré généré
        // innerHTML permet d'insérer du HTML dans un élément
        O_container.innerHTML = generateSquareHtml(A_squareData);
        
        // Après avoir inséré le nouveau HTML, il faut réattacher les event listeners
        // car les anciens boutons ont été supprimés et remplacés par de nouveaux
        attachClickListeners();
    }
}

/**
 * Gère le clic sur une cellule du carré
 * 
 * Cette fonction est appelée quand l'utilisateur clique sur un bouton du carré.
 * Elle envoie une requête AJAX au serveur pour vérifier si la position cliquée
 * est correcte, puis met à jour le carré si la réponse est bonne.
 * 
 * @param {number} I_row - Numéro de la ligne cliquée (0, 1 ou 2)
 * @param {number} I_col - Numéro de la colonne cliquée (0, 1 ou 2)
 */
function handleCellClick(I_row, I_col) {
    // Créer un objet FormData pour envoyer des données au serveur
    // FormData est utilisé pour envoyer des données de formulaire via AJAX
    var O_formData = new FormData();
    
    // Ajouter la ligne cliquée au FormData
    // toString() convertit le nombre en chaîne de caractères
    // Le serveur recevra 'row' = "0", "1" ou "2"
    O_formData.append('row', I_row.toString());
    
    // Ajouter la colonne cliquée au FormData
    // Le serveur recevra 'col' = "0", "1" ou "2"
    O_formData.append('col', I_col.toString());
    
    // Envoyer une requête HTTP POST au serveur avec fetch API
    // fetch() retourne une Promise (objet qui représente une opération asynchrone)
    fetch(window.location.pathname, {
        // Méthode HTTP : POST (pour envoyer des données)
        method: 'POST',
        // Corps de la requête : les données du formulaire (row et col)
        body: O_formData,
        // En-têtes HTTP : indiquer au serveur que c'est une requête AJAX
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    // .then() : exécuté quand la requête est terminée (succès ou échec HTTP)
    // O_response : objet Response contenant la réponse du serveur
    .then(function(O_response) {
        // Vérifier si la réponse HTTP est OK (status 200-299)
        // Si ce n'est pas OK, on lance une erreur pour la gérer dans .catch()
        if (!O_response.ok) {
            throw new Error('Erreur HTTP: ' + O_response.status);
        }
        // Convertir la réponse en objet JavaScript depuis le JSON
        // response.json() retourne aussi une Promise
        return O_response.json();
    })
    // Deuxième .then() : exécuté quand le JSON est parsé
    // O_data : objet JavaScript contenant les données du serveur
    .then(function(O_data) {
        // Vérifier si la réponse est positive :
        // - success === 1 : la position cliquée était correcte
        // - newSquare === true : un nouveau carré a été généré
        // - square existe : le nouveau carré est présent dans la réponse
        if (O_data.success === 1 && O_data.newSquare && O_data.square) {
            // Mettre à jour le carré avec le nouveau carré reçu du serveur
            updateSquare(O_data.square);
        }
    })
    // .catch() : exécuté en cas d'erreur (réseau, parsing JSON, etc.)
    .catch(function(O_error) {
        // Erreur silencieuse - le jeu continue même en cas d'erreur réseau
    });
}

/**
 * Attache les event listeners (écouteurs d'événements) sur toutes les cellules
 * 
 * Cette fonction est appelée après chaque mise à jour du carré pour que
 * les nouveaux boutons soient cliquables. On clone les noeuds pour éviter
 * d'avoir plusieurs listeners sur le même élément.
 */
function attachClickListeners() {
    // Sélectionner tous les boutons avec la classe "bit-cell" dans le DOM
    // querySelectorAll retourne une NodeList (liste de noeuds)
    var A_cells = document.querySelectorAll('.bit-cell');
    
    // Tableau qui contiendra les nouveaux noeuds clonés
    var A_newCells = [];
    
    // Première étape : cloner tous les noeuds pour retirer les anciens listeners
    // On parcourt tous les boutons trouvés
    for (var I_index = 0; I_index < A_cells.length; I_index++) {
        // Récupérer le bouton à l'index courant
        var O_cell = A_cells[I_index];
        
        // Cloner le noeud avec tous ses attributs et son contenu (true = deep clone)
        // Cloner un noeud supprime automatiquement tous ses event listeners
        var O_newCell = O_cell.cloneNode(true);
        
        // Remplacer l'ancien noeud par le nouveau dans le DOM
        // parentNode : le conteneur parent (square-container)
        // replaceChild : remplace un enfant par un autre
        O_cell.parentNode.replaceChild(O_newCell, O_cell);
        
        // Ajouter le nouveau noeud au tableau pour y attacher les listeners après
        A_newCells.push(O_newCell);
    }
    
    // Deuxième étape : attacher les nouveaux event listeners sur les noeuds clonés
    // On parcourt tous les nouveaux noeuds
    for (var I_index = 0; I_index < A_newCells.length; I_index++) {
        // Récupérer le noeud à l'index courant
        var O_cell = A_newCells[I_index];
        
        // Attacher un écouteur d'événement 'click' sur ce bouton
        // Quand le bouton est cliqué, la fonction anonyme est exécutée
        O_cell.addEventListener('click', function() {
            // this = le bouton qui a été cliqué
            // getAttribute('data-row') : récupère la valeur de l'attribut data-row
            // parseInt() : convertit la chaîne en nombre entier
            var I_row = parseInt(this.getAttribute('data-row'));
            
            // Récupérer la colonne de la même manière
            var I_col = parseInt(this.getAttribute('data-col'));
            
            // Appeler la fonction qui gère le clic avec les coordonnées
            handleCellClick(I_row, I_col);
        });
    }
}

/**
 * Initialise le carré de Hamming au chargement de la page
 * 
 * Cette fonction est appelée une fois au chargement de la page.
 * Le HTML du carré est déjà généré par PHP, on n'a qu'à attacher les event listeners.
 */
function initHamming() {
    // Récupérer le conteneur qui contient le carré
    var O_container = document.getElementById('square-container');
    
    // Vérifier que le conteneur existe
    // Si !O_container est vrai, c'est que O_container est null/undefined
    if (!O_container) {
        // Arrêter l'exécution de la fonction
        return;
    }
    
    // Le HTML est déjà généré par PHP, on n'a qu'à attacher les event listeners
    // pour rendre les boutons cliquables
    attachClickListeners();
}

// Note : Cette fonction n'est pas appelée automatiquement ici.
// Elle sera appelée depuis le HTML après que le script soit chargé.
// Voir hamming.html pour l'appel de initHamming()
