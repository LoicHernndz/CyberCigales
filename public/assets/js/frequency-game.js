/**
 * ANALYSE FREQUENTIELLE - Mini-Jeu Style macOS
 * CyberCigales Escape Game
 */

document.addEventListener('DOMContentLoaded', () => {
    // Elements du DOM
    const startScreen = document.getElementById('good-code-screen');
    const playScreen = document.getElementById('play-screen');
    const endScreen = document.getElementById('end-screen');

    const btnVerify = document.getElementById('btn-unlock');
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

    const btnReplay = document.getElementById('btn-replay');

    // Initialisation
    if (btnVerify) btnVerify.addEventListener('click', startVerify);
    if (btnStart) btnStart.addEventListener('click', startGame);
    if (btnCheck) btnCheck.addEventListener('click', checkSolution);
    if (btnReset) btnReset.addEventListener('click', resetSubstitutions);
    if (btnHelp) btnHelp.addEventListener('click', toggleHelp);
    if (btnReplay) btnReplay.addEventListener('click', function () { location.reload(); });

    function toggleHelp() {
        if (helpContent) helpContent.classList.toggle('hidden');
    }

    function startVerify() {
        const codeInput = document.querySelector('input[name="code"]');

        const formData = new FormData();
        formData.append('action', 'verify_code');
        formData.append('code', codeInput.value);

        fetch('/game/frequency', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('unlock-screen').classList.add('hidden');
                    document.getElementById('good-code-screen').classList.remove('hidden');
                } else {
                    document.getElementById('error-message-code').classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error(err);
                showFeedback("Erreur serveur", 'error');
            });
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

                    startScreen.classList.add('hidden');
                    playScreen.classList.remove('hidden');

                    userSubstitutions = {};

                    renderCipherText();
                    updatePreview();

                    if (dialogueText) {
                        dialogueText.textContent = "Analyse le message chiffre ! Clique sur une lettre pour definir sa correspondance.";
                    }
                } else {
                    btnStart.addEventListener("click", () => {
                        text.classList.toggle("visible");
                    })
                    showFeedback(data.message, 'error')
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
        feedbackSection.classList.remove('hidden');
        feedbackContent.textContent = message;
        feedbackContent.className = 'feedback-content ' + type;

        setTimeout(() => {
            feedbackSection.classList.add('hidden');
        }, 3000);
    }

    // Ecran de victoire
    function showVictory(decryptedText) {
        if (playScreen) playScreen.classList.add('hidden');
        if (endScreen) endScreen.classList.remove('hidden');

        const finalMessageEl = document.getElementById('final-message');
        if (finalMessageEl) finalMessageEl.textContent = decryptedText;

        if (dialogueText) {
            dialogueText.textContent = "Bravo ! Tu as decrypte le message secret !";
        }
    }
});
