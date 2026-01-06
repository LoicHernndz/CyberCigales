/**
 * Hamming Rush - Mini-jeu CyberCigales
 * Conventions : S_ (string), I_ (int), B_ (bool), A_ (array), O_ (object)
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Elements du DOM
    let O_container = document.getElementById('square-container');
    let O_streakCount = document.getElementById('streak-count');
    let O_streakDisplay = document.getElementById('streak-display');
    let O_progressBar = document.getElementById('progress-bar');
    let O_feedbackSection = document.getElementById('feedback-section');
    let O_feedbackContent = document.getElementById('feedback-content');
    let O_dialogueText = document.getElementById('dialogue-text');
    let O_btnHelp = document.getElementById('btn-toggle-help');
    let O_helpContent = document.getElementById('help-content');
    
    // Donnees du jeu
    let A_square = [];
    let I_streak = 0;
    let I_target = 5;
    let B_canClick = true;
    
    /**
     * Initialise le jeu
     */
    function initGame() {
        // Charger les donnees du carre
        let O_squareData = document.getElementById('square-data');
        let O_gameData = document.getElementById('game-data');
        
        if (O_squareData) {
            try {
                let S_json = O_squareData.textContent.trim();
                A_square = JSON.parse(S_json);
            } catch (e) {
                console.error('Erreur parsing square data');
            }
        }
        
        if (O_gameData) {
            try {
                let S_json = O_gameData.textContent.trim();
                let O_data = JSON.parse(S_json);
                I_streak = O_data.streak || 0;
                I_target = O_data.target || 5;
            } catch (e) {
                console.error('Erreur parsing game data');
            }
        }
        
        // Afficher le carre
        renderSquare();
        updateUI();
        
        // Event listener pour l'aide
        if (O_btnHelp) {
            O_btnHelp.addEventListener('click', toggleHelp, false);
        }
    }
    
    /**
     * Genere et affiche le carre HTML
     */
    function renderSquare() {
        if (!O_container || !A_square || A_square.length !== 3) {
            return;
        }
        
        let S_html = '';
        
        for (let I_row = 0; I_row < 3; I_row++) {
            for (let I_col = 0; I_col < 3; I_col++) {
                let I_bit = A_square[I_row][I_col];
                S_html += '<button class="bit-cell" data-row="' + I_row + '" data-col="' + I_col + '">' + I_bit + '</button>';
            }
        }
        
        O_container.innerHTML = S_html;
        attachClickListeners();
    }
    
    /**
     * Attache les listeners de clic sur les cellules
     */
    function attachClickListeners() {
        let A_cells = document.querySelectorAll('.bit-cell');
        
        for (let I_i = 0; I_i < A_cells.length; I_i++) {
            A_cells[I_i].addEventListener('click', function() {
                if (!B_canClick) return;
                
                let I_row = parseInt(this.getAttribute('data-row'));
                let I_col = parseInt(this.getAttribute('data-col'));
                handleCellClick(I_row, I_col, this);
            }, false);
        }
    }
    
    /**
     * Gere le clic sur une cellule
     */
    function handleCellClick(I_row, I_col, O_cell) {
        B_canClick = false;
        
        let O_formData = new FormData();
        O_formData.append('row', I_row.toString());
        O_formData.append('col', I_col.toString());
        
        fetch('/game/hamming', {
            method: 'POST',
            body: O_formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(O_response) {
            if (!O_response.ok) {
                throw new Error('Erreur HTTP');
            }
            return O_response.json();
        })
        .then(function(O_data) {
            handleResponse(O_data, O_cell);
        })
        .catch(function(O_error) {
            console.error('Erreur:', O_error);
            B_canClick = true;
        });
    }
    
    /**
     * Traite la reponse du serveur
     */
    function handleResponse(O_data, O_cell) {
        let B_isCorrect = O_data.success === 1;
        
        // Mettre a jour les donnees
        I_streak = O_data.streak || 0;
        
        // Animation de la cellule
        if (B_isCorrect) {
            O_cell.classList.add('correct');
        } else {
            O_cell.classList.add('wrong');
        }
        
        // Afficher le feedback
        showFeedback(O_data.message, B_isCorrect, I_streak >= I_target);
        
        // Mettre a jour l'UI
        updateUI();
        
        // Mettre a jour le dialogue
        if (B_isCorrect) {
            if (I_streak >= I_target || I_streak === 0) {
                O_dialogueText.textContent = 'Incroyable ! Tu as complete la serie de 5 ! Nouvelle serie commencee.';
            } else {
                O_dialogueText.textContent = 'Bravo ! Continue comme ca. Plus que ' + (I_target - I_streak) + ' pour finir la serie !';
            }
        } else {
            O_dialogueText.textContent = 'Oups ! Ce n\'etait pas le bon bit. La serie est reintialisee. Reessaie !';
        }
        
        // Attendre puis charger le nouveau carre
        setTimeout(function() {
            if (O_data.newSquare && O_data.square) {
                A_square = O_data.square;
                renderSquare();
            }
            B_canClick = true;
            hideFeedback();
        }, 1500);
    }
    
    /**
     * Affiche un message de feedback
     */
    function showFeedback(S_message, B_success, B_complete) {
        if (!O_feedbackSection || !O_feedbackContent) return;
        
        O_feedbackContent.textContent = S_message;
        O_feedbackContent.className = 'feedback-content';
        
        if (B_complete) {
            O_feedbackContent.classList.add('complete');
        } else if (B_success) {
            O_feedbackContent.classList.add('success');
        } else {
            O_feedbackContent.classList.add('error');
        }
        
        O_feedbackSection.style.display = 'block';
    }
    
    /**
     * Cache le feedback
     */
    function hideFeedback() {
        if (O_feedbackSection) {
            O_feedbackSection.style.display = 'none';
        }
    }
    
    /**
     * Met a jour l'interface
     */
    function updateUI() {
        if (O_streakCount) {
            O_streakCount.textContent = I_streak;
        }
        
        if (O_streakDisplay) {
            O_streakDisplay.textContent = I_streak + ' / ' + I_target;
        }
        
        if (O_progressBar) {
            let I_percent = (I_streak / I_target) * 100;
            O_progressBar.style.width = I_percent + '%';
        }
    }
    
    /**
     * Toggle l'aide
     */
    function toggleHelp() {
        if (!O_helpContent) return;
        
        if (O_helpContent.style.display === 'none') {
            O_helpContent.style.display = 'block';
        } else {
            O_helpContent.style.display = 'none';
        }
    }
    
    // Initialiser le jeu
    initGame();
    
});

