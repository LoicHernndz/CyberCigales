// Variables globales
let S_encryptedText = '';
let S_numColumns = 0;
let S_columns = [];
let S_initialOrder = [];
let S_draggedColumn = null;

/**
 * Génère la grille de colonnes à partir du texte chiffré.
 */
function I_generateGrid() {
    S_encryptedText = document.getElementById('encrypted-text').value.trim();
    S_numColumns = parseInt(document.getElementById('num-columns').value);

    if (!S_encryptedText) {
        alert('Veuillez entrer un texte chiffré.');
        return;
    }

    if (S_numColumns < 2 || S_numColumns > 20) {
        alert('Le nombre de colonnes doit être entre 2 et 20.');
        return;
    }

    // Calculer le nombre de lignes
    const O_numRows = Math.ceil(S_encryptedText.length / S_numColumns);

    // Remplir avec des underscores si nécessaire
    const O_paddedLength = O_numRows * S_numColumns;
    S_encryptedText = S_encryptedText.padEnd(O_paddedLength, '_');

    // Extraire les colonnes
    // Le texte chiffré est organisé en blocs de O_numRows caractères
    // Chaque bloc représente une colonne complète (de haut en bas)
    S_columns = [];
    S_initialOrder = [];

    for (let i = 0; i < S_numColumns; i++) {
        const O_column = [];
        const O_startIndex = i * O_numRows;

        for (let j = 0; j < O_numRows; j++) {
            O_column.push(S_encryptedText[O_startIndex + j] || '_');
        }

        S_columns.push(O_column);
        S_initialOrder.push(i);
    }

    // Afficher la grille
    I_renderGrid();
    document.getElementById('grid-container').classList.add('show');
}

/**
 * Affiche la grille de colonnes.
 */
function I_renderGrid() {
    const S_container = document.getElementById('columns-container');
    S_container.innerHTML = '';

    S_columns.forEach((O_column, O_index) => {
        const S_columnDiv = document.createElement('div');
        S_columnDiv.className = 'column';
        S_columnDiv.draggable = true;
        S_columnDiv.dataset.index = O_index;

        // Header de la colonne
        const S_header = document.createElement('div');
        S_header.className = 'column-header';
        S_header.textContent = `Col ${O_index + 1}`;

        // Contenu de la colonne
        const S_content = document.createElement('div');
        S_content.className = 'column-content';

        O_column.forEach(O_letter => {
            const S_letterSpan = document.createElement('span');
            S_letterSpan.className = 'column-letter';
            S_letterSpan.textContent = O_letter;
            S_content.appendChild(S_letterSpan);
        });

        S_columnDiv.appendChild(S_header);
        S_columnDiv.appendChild(S_content);

        // Événements drag & drop
        S_columnDiv.addEventListener('dragstart', I_handleDragStart);
        S_columnDiv.addEventListener('dragend', I_handleDragEnd);
        S_columnDiv.addEventListener('dragover', I_handleDragOver);
        S_columnDiv.addEventListener('drop', I_handleDrop);
        S_columnDiv.addEventListener('dragenter', I_handleDragEnter);
        S_columnDiv.addEventListener('dragleave', I_handleDragLeave);

        S_container.appendChild(S_columnDiv);
    });

    // Mettre à jour le résultat
    I_updateResult();
}

/**
 * Gère le début du drag.
 */
function I_handleDragStart(event) {
    S_draggedColumn = this;
    this.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', this.innerHTML);
}

/**
 * Gère la fin du drag.
 */
function I_handleDragEnd(event) {
    this.classList.remove('dragging');

    // Retirer la classe drag-over de toutes les colonnes
    document.querySelectorAll('.column').forEach(col => {
        col.classList.remove('drag-over');
    });
}

/**
 * Gère le survol pendant le drag.
 */
function I_handleDragOver(event) {
    if (event.preventDefault) {
        event.preventDefault();
    }
    event.dataTransfer.dropEffect = 'move';
    return false;
}

/**
 * Gère l'entrée dans une zone de drop.
 */
function I_handleDragEnter(event) {
    if (this !== S_draggedColumn) {
        this.classList.add('drag-over');
    }
}

/**
 * Gère la sortie d'une zone de drop.
 */
function I_handleDragLeave(event) {
    this.classList.remove('drag-over');
}

/**
 * Gère le drop.
 */
function I_handleDrop(event) {
    if (event.stopPropagation) {
        event.stopPropagation();
    }

    if (S_draggedColumn !== this) {
        // Échanger les colonnes
        const O_draggedIndex = parseInt(S_draggedColumn.dataset.index);
        const O_targetIndex = parseInt(this.dataset.index);

        // Échanger dans le tableau
        const O_temp = S_columns[O_draggedIndex];
        S_columns[O_draggedIndex] = S_columns[O_targetIndex];
        S_columns[O_targetIndex] = O_temp;

        // Réafficher la grille
        I_renderGrid();
    }

    return false;
}

/**
 * Met à jour le texte déchiffré en lisant ligne par ligne.
 */
function I_updateResult() {
    if (S_columns.length === 0) return;

    const O_numRows = S_columns[0].length;
    let O_result = '';

    // Lire ligne par ligne (de gauche à droite)
    for (let row = 0; row < O_numRows; row++) {
        for (let col = 0; col < S_columns.length; col++) {
            O_result += S_columns[col][row];
        }
    }

    document.getElementById('result-text').textContent = O_result;
}

/**
 * Copie le résultat dans le presse-papier.
 */
function I_copyResult() {
    const O_result = document.getElementById('result-text').textContent;
    navigator.clipboard.writeText(O_result).then(() => {
        alert('Texte copié dans le presse-papier !');
    }).catch(() => {
        alert('Erreur lors de la copie.');
    });
}

/**
 * Réinitialise les colonnes à leur ordre initial.
 */
function I_resetColumns() {
    if (confirm('Voulez-vous vraiment réinitialiser les colonnes à leur ordre initial ?')) {
        I_generateGrid();
    }
}

// Liaison des boutons par ID (remplace les onclick inline)
document.addEventListener('DOMContentLoaded', function () {
    var btnGenerate = document.getElementById('btn-generate');
    var btnCopy = document.getElementById('btn-copy');
    var btnReset = document.getElementById('btn-reset-columns');

    if (btnGenerate) btnGenerate.addEventListener('click', I_generateGrid);
    if (btnCopy) btnCopy.addEventListener('click', I_copyResult);
    if (btnReset) btnReset.addEventListener('click', I_resetColumns);
});