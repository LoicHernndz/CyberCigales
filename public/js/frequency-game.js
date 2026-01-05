document.addEventListener('DOMContentLoaded', () => {
    const btnStart = document.getElementById('btn-start-game');
    const startScreen = document.getElementById('start-screen');
    const playScreen = document.getElementById('play-screen');
    const cipherTextContainer = document.getElementById('cipher-text-container');
    const plainTextPreview = document.getElementById('plain-text-preview');
    const substitutionGrid = document.getElementById('substitution-grid');
    const frequencyChart = document.getElementById('frequency-chart');
    const btnCheck = document.getElementById('btn-check');
    const btnReset = document.getElementById('btn-reset');
    const feedbackMessage = document.getElementById('feedback-message');

    let currentEncryptedText = "";
    let userMappings = {}; 

    // Standard French Frequencies (approx order)
    const frenchFreqOrder = ['E', 'A', 'S', 'I', 'N', 'T', 'R', 'L', 'U', 'O', 'D', 'C', 'P', 'M', 'V', 'Q', 'F', 'B', 'G', 'H', 'J', 'X', 'Y', 'Z', 'K', 'W'];

    btnStart.addEventListener('click', startGame);
    btnCheck.addEventListener('click', checkSolution);
    btnReset.addEventListener('click', () => {
        userMappings = {};
        renderSubstitutionGrid();
        updatePreview();
    });

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
                currentEncryptedText = data.encrypted_text;
                startScreen.style.display = 'none';
                playScreen.style.display = 'block';
                
                // Initialize mappings
                userMappings = {};
                
                renderGame();
            }
        });
    }

    function renderGame() {
        cipherTextContainer.textContent = currentEncryptedText;
        analyzeFrequencies();
        renderSubstitutionGrid();
        updatePreview();
    }

    function analyzeFrequencies() {
        const counts = {};
        let total = 0;
        
        for (let char of currentEncryptedText) {
            if (/[A-Z]/.test(char)) {
                counts[char] = (counts[char] || 0) + 1;
                total++;
            }
        }

        const sortedChars = Object.keys(counts).sort((a, b) => counts[b] - counts[a]);
        
        let html = '';
        sortedChars.forEach(char => {
            const pct = ((counts[char] / total) * 100).toFixed(1);
            html += `
                <div class="freq-row">
                    <span class="freq-label">${char}</span>
                    <div class="freq-bar" style="width: ${pct * 3}px;"></div>
                    <span>${pct}%</span>
                </div>
            `;
        });
        
        // Add hint about French frequencies
        html += '<hr><p style="font-size:0.7em">Ordre FR: ' + frenchFreqOrder.join(' ') + '</p>';
        
        frequencyChart.innerHTML = html;
    }

    function renderSubstitutionGrid() {
        // Get unique chars from text
        const chars = [...new Set(currentEncryptedText.split(''))].filter(c => /[A-Z]/.test(c)).sort();
        
        substitutionGrid.innerHTML = '';
        chars.forEach(char => {
            const pair = document.createElement('div');
            pair.className = 'sub-pair';
            
            const label = document.createElement('label');
            label.textContent = char;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.maxLength = 1;
            input.value = userMappings[char] || '';
            input.dataset.char = char;
            
            input.addEventListener('input', (e) => {
                const val = e.target.value.toUpperCase();
                userMappings[char] = val;
                e.target.value = val;
                updatePreview();
            });

            pair.appendChild(label);
            pair.appendChild(input);
            substitutionGrid.appendChild(pair);
        });
    }

    function updatePreview() {
        let result = '';
        for (let char of currentEncryptedText) {
            if (userMappings[char]) {
                result += userMappings[char];
            } else if (/[A-Z]/.test(char)) {
                result += '_'; // Placeholder
            } else {
                result += char; // Spaces, punctuation
            }
        }
        plainTextPreview.textContent = result;
    }

    function checkSolution() {
        const currentSolution = plainTextPreview.textContent;
        
        const formData = new FormData();
        formData.append('action', 'check_solution');
        formData.append('solution', currentSolution);

        fetch('/game/frequency', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            feedbackMessage.textContent = data.message;
            if (data.correct) {
                feedbackMessage.style.color = 'green';
                plainTextPreview.style.backgroundColor = '#ccffcc';
            } else {
                feedbackMessage.style.color = 'red';
            }
        });
    }
});
