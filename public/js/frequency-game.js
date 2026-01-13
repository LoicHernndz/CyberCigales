/**
 * ANALYSE FREQUENTIELLE - Mini-Jeu Style macOS
 * CyberCigales Escape Game
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elements du DOM
    const startScreen = document.getElementById('start-screen');
    const playScreen = document.getElementById('play-screen');
    const endScreen = document.getElementById('end-screen');

    const btnStart = document.getElementById('btn-start-game');
    const btnCheck = document.getElementById('btn-check');
    const btnReset = document.getElementById('btn-reset');
    const btnHelp = document.getElementById('btn-toggle-help');
    const helpContent = document.getElementById('help-content');

    const cipherContainer = document.getElementById('cipher-text-container');
    const previewContainer = document.getElementById('preview-text-container');
    const feedbackSection = document.getElementById('feedback-section');
    const feedbackContent = document.getElementById('feedback-content');
    const dialogueText = document.getElementById('dialogue-text');

    // Etat du jeu
    let encryptedText = "";
    let userSubstitutions = {};

    // Initialisation
    if (btnStart) btnStart.addEventListener('click', startGame);
    if (btnCheck) btnCheck.addEventListener('click', checkSolution);
    if (btnReset) btnReset.addEventListener('click', resetSubstitutions);
    if (btnHelp) btnHelp.addEventListener('click', toggleHelp);

    function toggleHelp() {
        if (helpContent.style.display === 'none') {
            helpContent.style.display = 'block';
        } else {
            helpContent.style.display = 'none';
        }
    }

    // Demarrage du jeu
    function startGame() {
        const formData = new FormData();
        formData.append('action', 'start_game');

        fetch('/game/frequency', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    encryptedText = data.encrypted_text;

                    startScreen.style.display = 'none';
                    playScreen.style.display = 'block';

                    userSubstitutions = {};

                    renderCipherText();
                    updatePreview();

                    if (dialogueText) {
                        dialogueText.textContent = "Analyse le message chiffre ! Clique sur une lettre pour definir sa correspondance.";
                    }
                }
            })
            .catch(err => {
                console.error('Erreur au demarrage:', err);
                alert('Erreur de connexion au serveur');
            });
    }

    // Rendu du texte chiffre
    function renderCipherText() {
        if (!cipherContainer) return;
        cipherContainer.innerHTML = '';

        for (let i = 0; i < encryptedText.length; i++) {
            const char = encryptedText[i];
            const span = document.createElement('span');
            span.textContent = char;

            if (/[A-Z]/.test(char)) {
                span.className = 'cipher-letter';
                if (userSubstitutions[char]) {
                    span.classList.add('substituted');
                }
                span.addEventListener('click', () => openSubstitutionModal(char));
            }

            cipherContainer.appendChild(span);
        }
    }

    // Modal de substitution
    function openSubstitutionModal(cipherLetter) {
        const currentValue = userSubstitutions[cipherLetter] || '';
        const newValue = prompt(
            `La lettre "${cipherLetter}" correspond a quelle lettre ?\n(Laissez vide pour effacer)`,
            currentValue
        );

        if (newValue === null) return;

        if (newValue === '') {
            delete userSubstitutions[cipherLetter];
        } else {
            const upperValue = newValue.toUpperCase().charAt(0);
            if (/[A-Z]/.test(upperValue)) {
                userSubstitutions[cipherLetter] = upperValue;
            }
        }

        renderCipherText();
        updatePreview();
    }

    // Apercu du dechiffrement
    function updatePreview() {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        for (let i = 0; i < encryptedText.length; i++) {
            const char = encryptedText[i];
            const span = document.createElement('span');

            if (/[A-Z]/.test(char)) {
                span.className = 'preview-letter';
                if (userSubstitutions[char]) {
                    span.textContent = userSubstitutions[char];
                } else {
                    span.textContent = '_';
                    span.classList.add('missing');
                }
            } else {
                span.textContent = char;
            }

            previewContainer.appendChild(span);
        }
    }

    // Reinitialisation
    function resetSubstitutions() {
        if (confirm('Reinitialiser toutes les substitutions ?')) {
            userSubstitutions = {};
            renderCipherText();
            updatePreview();
            showFeedback("Reinitialise !", 'success');
        }
    }

    // Validation de la solution
    function checkSolution() {
        let userSolution = '';
        for (let char of encryptedText) {
            if (/[A-Z]/.test(char)) {
                userSolution += userSubstitutions[char] || '_';
            } else {
                userSolution += char;
            }
        }

        const formData = new FormData();
        formData.append('action', 'check_solution');
        formData.append('solution', userSolution);

        fetch('/game/frequency', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.correct) {
                    showVictory(userSolution);
                } else {
                    showFeedback(data.message || "Ce n'est pas correct. Continue !", 'error');
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                showFeedback("Erreur de connexion", 'error');
            });
    }

    // Affichage du feedback
    function showFeedback(message, type) {
        if (!feedbackSection || !feedbackContent) return;
        feedbackSection.style.display = 'block';
        feedbackContent.textContent = message;
        feedbackContent.className = 'feedback-content ' + type;

        setTimeout(() => {
            feedbackSection.style.display = 'none';
        }, 3000);
    }

    // Ecran de victoire
    function showVictory(decryptedText) {
        if (playScreen) playScreen.style.display = 'none';
        if (endScreen) endScreen.style.display = 'block';

        const finalMessageEl = document.getElementById('final-message');
        if (finalMessageEl) finalMessageEl.textContent = decryptedText;

        if (dialogueText) {
            dialogueText.textContent = "Bravo ! Tu as decrypte le message secret !";
        }
    }
});
